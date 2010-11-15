<?

//--------------------------------------------------------------------------------------------------
//*	Represents a single uploaded flash or MP4 video.
//--------------------------------------------------------------------------------------------------

class Videos_Video {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $refModule;			//_ module [string]
	var $refModel;			//_ model [string]
	var $refUID;			//_ ref:*_* [string]
	var $title;				//_ title [string]
	var $licence;			//_ varchar(50) [string]
	var $attribName;		//_ varchar(255) [string]
	var $attribUrl;			//_ varchar(255) [string]
	var $fileName;			//_ varchar(255) [string]
	var $hash;				//_ varchar(50) [string]
	var $format;			//_ varchar(30) [string]
	var $transforms;		//_ plaintext [string]
	var $caption;			//_ text [string]
	var $category;			//_ varchar(100) [string]
	var $weight;			//_ bigint [string]
	var $length;			//_ bigint [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Video object [string]

	function Videos_Video($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Video ' . $this->UID;		// set default title
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Video object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Video object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->title = $ary['title'];
		$this->licence = $ary['licence'];
		$this->attribName = $ary['attribName'];
		$this->attribUrl = $ary['attribUrl'];
		$this->fileName = $ary['fileName'];
		$this->hash = $ary['hash'];
		$this->format = $ary['format'];
		$this->transforms = $ary['transforms'];
		$this->caption = $ary['caption'];
		$this->category = $ary['category'];
		$this->weight = $ary['weight'];
		$this->length = $ary['length'];
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
		$this->alias = $aliases->create('videos', 'Videos_Video', $this->UID, $this->title);
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
		$dbSchema['module'] = 'videos';
		$dbSchema['model'] = 'Videos_Video';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'TEXT',
			'refModel' => 'TEXT',
			'refUID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'licence' => 'VARCHAR(50)',
			'attribName' => 'VARCHAR(255)',
			'attribUrl' => 'VARCHAR(255)',
			'fileName' => 'VARCHAR(255)',
			'hash' => 'VARCHAR(50)',
			'format' => 'VARCHAR(30)',
			'transforms' => 'MEDIUMTEXT',
			'caption' => 'TEXT',
			'category' => 'VARCHAR(100)',
			'weight' => 'TEXT',
			'length' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'TEXT' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'title',
			'licence',
			'attribName',
			'attribUrl',
			'fileName',
			'hash',
			'format',
			'transforms',
			'caption',
			'category',
			'weight',
			'length' );

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'title' => $this->title,
			'licence' => $this->licence,
			'attribName' => $this->attribName,
			'attribUrl' => $this->attribUrl,
			'fileName' => $this->fileName,
			'hash' => $this->hash,
			'format' => $this->format,
			'transforms' => $this->transforms,
			'caption' => $this->caption,
			'category' => $this->category,
			'weight' => $this->weight,
			'length' => $this->length,
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

		$xml = $indent . "<kobject type='Videos_Video'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <refModule>" . $this->refModule . "</refModule>\n"
			. $indent . "    <refModel>" . $this->refModel . "</refModel>\n"
			. $indent . "    <refUID>" . $this->refUID . "</refUID>\n"
			. $indent . "    <title>" . $this->title . "</title>\n"
			. $indent . "    <licence>" . $this->licence . "</licence>\n"
			. $indent . "    <attribName>" . $this->attribName . "</attribName>\n"
			. $indent . "    <attribUrl>" . $this->attribUrl . "</attribUrl>\n"
			. $indent . "    <fileName>" . $this->fileName . "</fileName>\n"
			. $indent . "    <hash>" . $this->hash . "</hash>\n"
			. $indent . "    <format>" . $this->format . "</format>\n"
			. $indent . "    <transforms>" . $this->transforms . "</transforms>\n"
			. $indent . "    <caption>" . $this->caption . "</caption>\n"
			. $indent . "    <category>" . $this->category . "</category>\n"
			. $indent . "    <weight>" . $this->weight . "</weight>\n"
			. $indent . "    <length>" . $this->length . "</length>\n"
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
		if (true == $user->authHas('videos', 'Videos_Video', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Videos/showvideo/' . $ext['alias'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('videos', 'Videos_Video', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Videos/editvideo/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('videos', 'Videos_Video', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Videos/delvideo/' . $ext['alias'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		$ext['summary'] = $theme->makeSummary($ext['caption']);
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

}

?>
