<?

//--------------------------------------------------------------------------------------------------
//*	Container object which owns videos.
//--------------------------------------------------------------------------------------------------

class Videos_Gallery {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title [string]
	var $description;		//_ wyswyg [string]
	var $videocount;		//_ bigint [string]
	var $origin;			//_	(user|3rdparty) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $shared;			//_ share this object with other instances (yes|no) [string]
	var $revision;			//_ revision [string]
	var $alias;				//_ alias [string]

	var $videos;					//_	array of serialized Videos_Gallery objects [array]
	var $videosLoaded = false;		//_	set to true when videos array loaded [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Gallery object [string]

	function Videos_Gallery($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Gallery ' . $this->UID;		// set default title
			$this->origin = 'user';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Gallery object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Gallery object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->description = $ary['description'];
		$this->videocount = $ary['videocount'];
		$this->origin = $ary['origin'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];
		$this->revision = $ary['revision'];
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
		global $db;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('videos', 'videos_gallery', $this->UID, $this->title);
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

		$this->videocount = $this->countVideos();
		if ('' == $this->origin) { $this->origin = 'user'; }
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
		$dbSchema['model'] = 'videos_gallery';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'description' => 'MEDIUMTEXT',
			'videocount' => 'TEXT',
			'origin' => 'VARCHAR(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'shared' => 'VARCHAR(3)',
			'revision' => 'BIGINT(20)',
			'alias' => 'TEXT' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'origin' => '3',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will not be kept for these fields
		$dbSchema['nodiff'] = array(
			'title',
			'description',
			'videocount' 
		);

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
			'description' => $this->description,
			'videocount' => $this->videocount,
			'origin' => $this->origin,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared,
			'revision' => $this->revision,
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

		$xml = $indent . "<kobject type='videos_gallery'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <title>" . $this->title . "</title>\n"
			. $indent . "    <description><![CDATA[" . $this->description . "]]></description>\n"
			. $indent . "    <videocount>" . $this->videocount . "</videocount>\n"
			. $indent . "    <origin>" . $this->origin . "</origin>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "    <shared>" . $this->shared . "</shared>\n"
			. $indent . "    <revision>" . $this->revision . "</revision>\n"
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
		global $user;
		global $utils;
		global $theme;

		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('videos', 'videos_gallery', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%videos/showgallery/' . $ext['alias'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[more &gt;&gt;]</a>";
		}

		if (true == $user->authHas('videos', 'videos_gallery', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%videos/editgallery/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[edit gallery]</a>";
		}

		if (true == $user->authHas('videos', 'videos_gallery', 'edit', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%videos/confirmdelete/UID_' . $ext['UID'] . '/';
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[delete]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		$ext['descriptionJsVar64'] = 'description' . $utils->makeAlphaNumeric($ext['UID']) . 'Js64';
		$ext['descriptionJs64'] = $utils->base64EncodeJs($ext['descriptionJsVar64'], $ext['description']);
		$ext['descriptionSummary'] = $theme->makeSummary($ext['description']);

		$ext['videocount'] = (int)$ext['videocount'] . '';

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

	//==============================================================================================
	//	videos
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//. load all videos in this gallery
	//----------------------------------------------------------------------------------------------
	//returns: array of serialized Videos_Video objects [array]

	function loadVideos() {
		global $db;		

		$conditions = array();
		$conditions[] = "refModule='videos'";
		$conditions[] = "refModel='" . $db->addMarkup('videos_gallery') . "'";
		$conditions[] = "refUID='" . $db->addMarkup($this->UID) . "'";

		$range = $db->loadRange('videos_video', '*', $conditions, 'weight ASC');

		$this->videos = $range;
		$this->videosLoaded = true;
		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//. count videos in this gallery
	//----------------------------------------------------------------------------------------------
	//returns: number of videos in the gallery [int]

	function countVideos() {
		if (false == $this->videosLoaded) { $this->loadVideos(); }
		if (false == $this->videosLoaded) { return false; }
		$count = count($this->videos);
		return $count;
	}

}

?>
