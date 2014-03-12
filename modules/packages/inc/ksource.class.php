<?

	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent a kapenta software source / list of packages
//--------------------------------------------------------------------------------------------------
//+	Package listing is an XML document with the following format
//+
//+	<source>
//+		<url>http://my-repostory.com/code/</url>
//+		<checked>2011-12-10 01:01:45</checked>
//+		<packages>
//+			<package>
//+				<uid>123456789</uid>
//+				<name>Example-Package</name>
//+				<version>2</version>
//+				<revision>124</revision>
//+				<description>Plaintext description here.</description>
//+				<updated>2011-07-02 11:30:22</updated>
//+			</package>
//+			<package>
//+				<uid>987654321</uid>
//+				<name>Another-Package</name>
//+				<version>1</version>
//+				<revision>24</revision>
//+				<description>Plaintext description here.</description>
//+				<updated>2011-08-05 14:30:22</updated>
//+			</package>
//+		</packages>
//+	</source>
//+
//+	Software source files are stored in /data/updates/ (extension: source.php)

class KSource {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	
	var $url = '';				//_	URL of a kapenta repository [string]
	var $checked = '';			//_	datetime when last checked [string]
	var $packages;				//_	set of packages [array]
	var $recent = false;		//_	set to true if checked less than 24h ago [bool]
	var $loaded = false;		//_	set to true if package list loaded [bool]

	var $filename = '';			//_	local file in which package list is cached

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: url - URL of a kapenta repository [string]

	function KSource($url = '') {
		global $kapenta;
		global $utils;

		$this->url = $url;
		$this->packages = array();
		$this->checked = '1970-01-01 00:00:00';

		if ('' != $url) {
			$this->filename = 'data/updates/' . $utils->makeAlphaNumeric($url) . '.source.php';
			if (true == $kapenta->fs->exists($this->filename)) { 
				$this->loadXml($this->filename, true); 
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load package list serialized as XML 
	//----------------------------------------------------------------------------------------------
	//arg: xml - xml or filename relative to install path [string]
	//arg: isFile - set to true if filename andnot raw XML [bool]

	function loadXml($xml, $isFile = false) {
		global $kapenta;

		$doc = new KXmlDocument($xml, $isFile);
		$kids = $doc->getChildren(1);				//%	1 is the root node [array]

		foreach($kids as $childId) {
			$entity = $doc->getEntity($childId);
			switch(strtolower($entity['type'])) {
				case 'url':			$this->url = $entity['value'];			break;		//..........
				case 'checked':		$this->checked = $entity['value'];		break;		//..........

				case 'packages':	
					$this->packages = array();
					$packages = $doc->getChildren($childId);
					foreach($packages as $packageId) {
						$pkgAry = $doc->getChildren2d($packageId);	//TODO: testing here
						$this->packages[$pkgAry['uid']] = $pkgAry;
					}
					break;		//..................................................................
			}
		}

		//------------------------------------------------------------------------------------------
		// check if this listing is recent or not (24h old)	
		//------------------------------------------------------------------------------------------
		//TODO: make the update frequency a registry setting
		$yesterday = $kapenta->time() - (24 * 60 * 60);				//%	time 24 hours ago [int]
		if ($yesterday > $kapenta->strtotime($this->checked)) { $recent = false; }
		else { $recent = true; }

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize into XML and save to disk
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function saveXml() {
		global $kapenta;
		$xml = $this->toXml();
		$check = $kapenta->fs->put($this->filename, $xml, true, true);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialzie to XML
	//----------------------------------------------------------------------------------------------
	//returns: xml [string]

	function toXml() {
		$xml = '';				//%	return value [string]

		$packages = '';
		foreach($this->packages as $uid => $package) {
			$packages .= ''
			 . "\t\t<package>\n"
			 . "\t\t\t<uid>". $package['uid'] . "</uid>\n"
			 . "\t\t\t<name>". $package['name'] . "</name>\n"
			 . "\t\t\t<description>". $package['description'] . "</description>\n"
			 . "\t\t\t<version>". $package['version'] . "</version>\n"
			 . "\t\t\t<revision>". $package['revision'] . "</revision>\n"
			 . "\t\t\t<updated>". $package['updated'] . "</updated>\n"
			 . "\t\t</package>\n";
		}

		$xml = ''
		 . "<source>\n"
		 . "\t<url>" . $this->url . "</url>\n"
		 . "\t<checked>" . $this->checked . "</checked>\n"
		 . "\t<packages>\n"
		 . $packages
		 . "\t</packages>\n"
		 . "</source>\n";			

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	update from repository listing
	//----------------------------------------------------------------------------------------------
	//opt: print - print progress directly to output [string]
	//returns: html report of update [html]

	function update($print = false) {
		global $kapenta;
		global $kapenta;
		global $utils;
		global $session;

		$url = $this->url . 'listxml/';		//%	location of package list document [string]
		$xml = $utils->curlGet($url);		//%	raw XML [string]
		$kapenta->session->msg("$url <br/>" . strlen($xml) . " bytes");
		$result = false;					//% return value [bool]

		$report = ''
		 . "<b>$url</b>\n"
		 . "Downloading package list... " . strlen($xml) . " bytes.<br/>"
		 . "<textarea rows='10' style='width: 100%;'>" . htmlentities($xml) . "</textarea>";

		if (true == $print) { $this->printchat($report); }

		if (false == strpos($xml, '</source>')) { 
			$report .= "<span class='ajaxerror'>Skipping this software source.</span><br/>\n";
			if (true == $print) { $this->printchat($report); }
			return $report;
		}

		$this->loadXml($xml, false);
		$this->checked = gmdate("Y-m-d H:i:s", $kapenta->time());
		$check = $this->saveXml();

		foreach($this->packages as $uid => $pkg)
		{
	
			$pkgreport = '';
			$pkgcolor = 'green';

			$prefix = 'pkg.' . $uid . '.';
			if ('dirty' == $kapenta->registry->get($prefix . 'status'))
			{
				$kapenta->registry->set($prefix . 'status', 'installed');
			}

			if ('' == $kapenta->registry->get($prefix . 'source')) {
				$kapenta->registry->set($prefix . 'source', $this->url);
				$pkgreport .= "Set source for: " . $pkg['name'] . " (" . $this->url . ")<br/>\n";
			}

			if (
				($kapenta->registry->get($prefix . 'date') != $pkg['updated']) ||
				(false == $kapenta->fs->exists('data/packages/' . $uid . '.xml.php')) ||
				('unknown' == $kapenta->registry->get($prefix . 'name'))
			) {
				//----------------------------------------------------------------------------------
				//	add to / update registry						
				//----------------------------------------------------------------------------------

				$kapenta->registry->set($prefix . 'source', $this->url);
				$kapenta->registry->set($prefix . 'uid', $pkg['uid']);
				$kapenta->registry->set($prefix . 'name', $pkg['name']);
				$kapenta->registry->set($prefix . 'v', $pkg['version']);
				$kapenta->registry->set($prefix . 'r', $pkg['revision']);
				$kapenta->registry->set($prefix . 'date', $pkg['updated']);

				if ('' == $kapenta->registry->get($prefix . 'status'))
				{
					$kapenta->registry->set($prefix . 'status', 'available');
				}

				$pkgreport .= "Updating package: " . $pkg['name'] . " ($uid)<br/>";

				//----------------------------------------------------------------------------------
				//	download new manifest
				//----------------------------------------------------------------------------------
				$package = new KPackage($uid);
				$check = $package->updateFromRepository();

				if (true == $check) {
					$pkgreport .= "<span class='ajaxmsg'>Manifest updated.</span><br/>";
				} else {
					$pkgreport .= "<span class='ajaxerror'>Could not download manifest.</span><br/>";
				}

			} else {
				$pkgreport .= $pkg['name'] . " manifest is up to date.<br/>\n";
			}

			//--------------------------------------------------------------------------------------
			//	check for dirty files
			//--------------------------------------------------------------------------------------
			if ('installed' == $kapenta->registry->get($prefix . 'status'))
			{
				$package = new KPackage($uid);
				$dirtyFiles = $package->getLocalDifferent();	//	sets .dirty in registry
				foreach($dirtyFiles as $file) {
					$pkgcolor = 'red';
					$pkgreport .= ''
					 . "<small>"
					 . $file['path'] . ' (hash: ' . $file['hash'] . ') '
					 . "<span class='ajaxwarn'>" . $file['local'] . "</span>"
					 . "</small><br/>";
				}
			}

			if (true == $print) { $this->printchat($pkgreport, $pkgcolor); }
			$report .= $pkgreport;

		} // end foreach $packages

		return $report;	
	}

	//----------------------------------------------------------------------------------------------
	//.	get details of an individual package
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a package from this source [string]
	//returns:

	function getPackageDetails($UID) {
		global $kapenta;
		$package = array();					//%	return value [array]

		if (false == array_key_exists($UID, $this->packages)) { return $package; }
		$package = $this->packages[$UID];
		$package['source'] = $this->url;
		$package['repUrl'] = $package['source'] . 'showpackage/' . $package['uid'];
		$package['repLink'] = "<a href='" . $package['repUrl'] . "'>[details]</a>";

		$package['manifestUrl'] = '';
		$package['manifestLink'] = '';
		$package['installForm'] = '';	

		if ('installed' == $kapenta->registry->get('pkg.' . $UID . '.status')) {
			$package['manifestUrl'] = '%%serverPath%%packages/showpackage/' . $UID;
			$package['manifestLink'] = "<a href='" . $package['manifestUrl'] . "'>[local manifest]</a>";
		} else {
			$package['installForm'] = '[[:packages::installpackageform::UID=' . $UID . ':]]';
		}

		return $package;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a flat list of packages (UID => name v.r)
	//----------------------------------------------------------------------------------------------

	function listPackages() {
		$pkgs = array();

		foreach($this->packages as $uid => $pkg) {
			$pkgs[$uid] = $pkg['name'] . " v" . $pkg['version'] . "." . $pkg['revision'];
		}

		return $pkgs;
	}

	//----------------------------------------------------------------------------------------------
	//.	print directly to browser
	//----------------------------------------------------------------------------------------------

	function printchat($msg, $color = 'black') {
		echo ''
		 . "<div class='chatmessage" . $color . "'>\n"
		 . $msg
		 . "</div>\n"
		 . "<script>scrollToBottom();</script>";
		flush();
	}

}

?>
