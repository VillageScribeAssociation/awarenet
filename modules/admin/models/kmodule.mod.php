<?

//--------------------------------------------------------------------------------------------------
//*	object for working with module data
//--------------------------------------------------------------------------------------------------

class KModule {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $fileName = '';
	var $loaded = false;

	var $modulename = '';
	var $version = '';
	var $revision = '';
	var $description = '';
	var $core = '';
	var $installed = '';
	var $enabled = '';
	var $search = '';
	
	var $permissions;
	var $dependancy;
	var $blocks;

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: moduleName - name of a module [string]

	function KModule($moduleName = '') {
		$this->permissions = array();
		$this->dependency = array();
		$this->blocks = array();

		if ($moduleName != '') { $this->load($moduleName); }
	}	

	//----------------------------------------------------------------------------------------------
	//.	load a module.xml.php file
	//----------------------------------------------------------------------------------------------
	//arg: moduleName - name of a module [string]

	function load($moduleName) {
		global $kapenta;

		$fileName = 'modules/' . $moduleName . '/module.xml.php';
		if (false == $kapenta->fileExists($fileName)) { "echo NSF $fileName"; return false; }

		$this->modulename = $moduleName;
		$this->fileName = $fileName;
		$this->permissions = array();

		//------------------------------------------------------------------------------------------
		//	read xml
		//------------------------------------------------------------------------------------------
		$doc = new KXmlDocument($fileName, true);			// parse XML document
		$root = $doc->getEntity(1);							// try get root entity

		if (false == $root) { return false; }				// check that we did		

		//------------------------------------------------------------------------------------------
		//	get basic module fields
		//------------------------------------------------------------------------------------------

		foreach($root['children'] as $idx => $childId) {
			$child = $doc->getEntity($childId);
		    switch ($child['type']) {
				case 'modulename':	$this->modulename = trim($child['value']); 	break;
				case 'version':		$this->version = $child['value']; 		break;
				case 'revision':	$this->revision = $child['value']; 		break;
				case 'description':	$this->description = $child['value'];	break;
				case 'core':		$this->core = $child['value']; 			break;
				case 'installed':	$this->installed = $child['value'];		break;
				case 'enabled':		$this->enabled = $child['value']; 		break;
				case 'search':		$this->search = $child['value'];		break;
				case 'permissions':	
					foreach($child['children'] as $permissionId) {
						$permission = $doc->getEntity($permissionId);
						if ('permission' == $permission['type']) 
							{ $this->permissions[] = $permission['value']; }

					}
					break;

				case 'dependancies':	
					//TODO
					break;

			}
		}

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------
		$this->loaded = true;
		return true;		
	}

	//----------------------------------------------------------------------------------------------
	//.	save a module.xml.php file // TODO: use XmlEntity for this
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta;
		$deps = '';
		$perms = '';

		//foreach($this->dependancy as $dep) { $deps .= "        <depend>$dep</depend>\n"; }

		foreach($this->permissions as $permission) { 
			$perms .= "        <perm>$permission</perm>\n"; 
		}

		$xml = "<module>\n"
		     . "    <modulename>" . $this->modulename . "</modulename>\n"
		     . "    <version>" . $this->version . "</version>\n"
		     . "    <revision>" . $this->description . "</revision>\n"
		     . "    <description>" . $this->description . "</description>\n"
		     . "    <core>" . $this->core . "</core>\n"
		     . "    <installed>" . $this->installed . "</installed>\n"
		     . "    <enabled>" . $this->enabled . "</enabled>\n"
		     . "    <dbschema>" . $this->dbschema . "</dbschema>\n"
		     . "    <search>" . $this->search . "</search>\n"
		     . "    <dependancies>\n" . $deps . "    </dependancies>\n"
		     . "    <permissions>\n" . $perms . "    </permissions>\n"
		     . "    <blocks>\n"
		     . "    </blocks>\n"
		     . "</module>\n";

		//echo "fileName: " . $this->fileName . "<br/>\n";
		//echo "<textarea rows='30' cols='50'>" . $xml . "</textarea><br/>\n";

		$kapenta->filePutContents($fileName, $xml, false, true);
	}

	//----------------------------------------------------------------------------------------------
	//.	to array
	//----------------------------------------------------------------------------------------------
	//returns: array of module data [array]

	function toArray() {
		$ary = array(
			'modulename' => $this->modulename,
			'version' => $this->version,
			'revision' => $this->revision,
			'description' => $this->description,
			'core' => $this->core,
			'installed' => $this->installed,
			'enabled' => $this->enabled,
			'search' => $this->search
		);
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['editUrl'] = '';		$ext['editLink'] = '';
		$ext['installUrl'] = '';	$ext['installLink'] = '';

		if ('admin' == $user->role) {
			$ext['editUrl'] = '%%serverPath%%admin/module/' . $this->modulename;	
			$ext['installUrl'] = '%%serverPath%%admin/install/' . $this->modulename;
			//TODO: moar
		}
		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the current module's installation status
	//----------------------------------------------------------------------------------------------
	//returns: installation status message [string]
	//, this function depends upon a standard install.inc.php

	function getInstallStatusReport() {
		global $kapenta;

		$incFile = $kapenta->installPath . 'modules/' . $this->modulename . '/inc/install.inc.php';
		if (false == file_exists($incFile)) { return 'no install script'; }

		require_once($incFile);

		$statusFn = $this->modulename . '_install_status_report';
		if (function_exists($statusFn) == false) { return 'no status function'; }

		return $statusFn();
	}

	//----------------------------------------------------------------------------------------------
	//.	get the current module's installation status
	//----------------------------------------------------------------------------------------------
	//returns: installation status message [string]
	//, this function depends upon a standard install.inc.php

	function install() {
		global $kapenta;

		$incFile = $kapenta->installPath . 'modules/' . $this->modulename . '/inc/install.inc.php';
		if (false == file_exists($incFile)) { return 'no install script'; }

		require_once($incFile);

		$statusFn = $this->modulename . '_install_module';
		if (function_exists($statusFn) == false) { return 'no install_module function'; }

		return $statusFn();
	}

}

?>
