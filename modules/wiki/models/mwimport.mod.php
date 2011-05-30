<?

//--------------------------------------------------------------------------------------------------
//*	
//--------------------------------------------------------------------------------------------------

class Wiki_MWImport {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title [string]
	var $wikiUrl;			//_ varchar(255) [string]
	var $content;			//_ text [string]
	var $categories;		//_ text [string]
	var $assets;			//_ text [string]
	var $status;			//_ varchar(30) [string]
	var $pageid;			//_ varchar(50) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	var $apFromFile;

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a MWImport object [string]

	function Wiki_MWImport($raUID = '') {
		global $db, $kapenta;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New MWImport ' . $this->UID;	// set default title
			$this->loaded = false;
		}

		$this->apFromFile = 'data/apfrom.txt';

	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a MWImport object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load MWImport object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->wikiUrl = $ary['wikiUrl'];
		$this->content = $ary['content'];
		$this->categories = $ary['categories'];
		$this->assets = $ary['assets'];
		$this->status = $ary['status'];
		$this->pageid = $ary['pageid'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = '';
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'wiki';
		$dbSchema['model'] = 'wiki_mwimport';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'wikiUrl' => 'VARCHAR(255)',
			'content' => 'MEDIUMTEXT',
			'categories' => 'TEXT',
			'assets' => 'TEXT',
			'status' => 'VARCHAR(30)',
			'pageid' => 'VARCHAR(50)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'pageid' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'title',
			'wikiUrl',
			'content',
			'categories',
			'assets',
			'status',
			'pageid',
			'createdOn',
			'editedOn',
			'editedBy',
			'alias' );

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'title' => $this->title,
			'wikiUrl' => $this->wikiUrl,
			'content' => $this->content,
			'categories' => $this->categories,
			'assets' => $this->assets,
			'status' => $this->status,
			'pageid' => $this->pageid,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to xml
	//----------------------------------------------------------------------------------------------
	//arg: xmlDec - include xml declaration? [bool]
	//arg: indent - string with which to indent lines [bool]
	//returns: xml serialization of this object [string]

	function toXml($xmlDec = false, $indent = '') {
		//NOTE: any members which are not XML clean should be marked up before sending

		$xml = $indent . "<kobject type='Wiki_MWImport'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <title>" . $this->title . "</title>\n"
			. $indent . "    <wikiUrl>" . $this->wikiUrl . "</wikiUrl>\n"
			. $indent . "    <content>" . $this->content . "</content>\n"
			. $indent . "    <categories>" . $this->categories . "</categories>\n"
			. $indent . "    <assets>" . $this->assets . "</assets>\n"
			. $indent . "    <status>" . $this->status . "</status>\n"
			. $indent . "    <pageid>" . $this->pageid . "</pageid>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "    <alias>" . $this->alias . "</alias>\n"
			. $indent . "</kobject>\n";

		if (true == $xmlDec) { $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n" . $xml;}
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user, $utils, $theme;
		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('wiki', 'Wiki_MWImport', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Wiki/showmwimport/' . $ext['alias'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('wiki', 'Wiki_MWImport', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Wiki/editmwimport/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('wiki', 'Wiki_MWImport', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Wiki/delmwimport/' . $ext['alias'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. discover if an article exists, given its ID
	//----------------------------------------------------------------------------------------------
	//returns: UID if found, empty string if not [string]

	function articleExists($pageId) {
		global $db;
		$conditions = array("pageid='" . $db->addMarkup($pageId) . "'");
		$range = $db->loadRange('Wiki_MWImport', '*', $conditions);
		if (0 == count($range)) { return ''; }
		foreach($range as $row) { return $row['UID']; }
	}

	//==============================================================================================
	//	Parse MediaWiki API responses
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	expand article listing result
	//----------------------------------------------------------------------------------------------
	//arg: xml - returned by MediaWiki API [string:xml]
	//eg: http://example.org/api.php?action=query&list=allpages&apfrom=Los&aplimit=50&format=xml

	function expandAllPages($xml) {
		$result = array('apfrom' => '', 'allpages' => array());

		$xml = str_replace(">", ">\n", $xml);
		$lines = explode("\n", $xml);
		//foreach($lines as $line) {	echo htmlentities($line) . "<br/>\n"; }

		foreach($lines as $line) {
			if ('<allpages apfrom' == substr($line, 0, 16)) { 
				$parts = explode("\"", $line);
				$result['apfrom'] = $parts[1];
			}

			if ('<p pageid' == substr($line, 0, 9)) { 
				$parts = explode("\"", $line);
				$result['allpages'][] = array(
					'id' => $parts[1],
					'ns' => $parts[3],
					'title' => $parts[5]
				);
			}
		}

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	record a set of pages as existing
	//----------------------------------------------------------------------------------------------
	//arg: objary - output of expandAllPages [array]
	//arg: wikiUrl - API where this was downloaded from [string]
	//returns: number of new items added [int]

	function recordNewPages($objary, $wikiUrl) {
		$count = 0;

		foreach($objary['allpages'] as $article) {
			if (false == $this->articleExists($article['id'])) {

				echo "<b>article:</b> " . $article['title'] . " (id: " . $article['id'] . ")<br/>";
				flush();

				$model = new Wiki_MWImport();
				$model->wikiUrl = $wikiUrl;
				$model->title = $article['title'];
				$model->pageid = $article['id'];
				$model->status = 'new';
				$report = $model->save();
				if ('' == $report) { $count++; }
			}
		}

		return $count;
	}

	//----------------------------------------------------------------------------------------------
	//.	save apfrom point
	//----------------------------------------------------------------------------------------------
	//arg: title - article title we're up to in the scan [string]
	//returns: result of file save [bool]

	function saveApFrom($title) {
		global $kapenta;
		$result = $kapenta->filePutContents($this->apFromFile, $title, true, false);
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	restore apfrom point
	//----------------------------------------------------------------------------------------------
	//returns: the last article title which was saved [string]

	function restoreApFrom() {
		global $kapenta;
		if (false == $kapenta->fileExists($this->apFromFile)) { return ''; }
		$result = $kapenta->fileGetContents($this->apFromFile, true, false);
		return $result;
	}

}

?>
