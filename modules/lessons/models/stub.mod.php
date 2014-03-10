<?php

//--------------------------------------------------------------------------------------------------
//*	this stub object allows database object to depend on course documents
//--------------------------------------------------------------------------------------------------

class Lessons_Stub {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database object [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	records whether object was loaded from database [bool]

	var $UID = '';			//_	UID [string]
	var $course = '';		//_	module [string]
	var $document = '';		//_	UID fo a document [string]
	var $title = '';		//_	name of document [string]
	var $type = '';			//_	format of document [string]
	var $description = '';	//_	descriptionof this item [string]
	var $cover = '';		//_	canonical location of cover image [string]
	var $thumb = '';		//_ canonical location of thumbnail image [string]
	var $file = '';			//_ canonical location of this item on disk [string]
	var $attribname = '';	//_	name of copyright holder /organization [string]
	var $attriburl = '';	//_	online presence of copyright holder, if available [string]
	var $licencename = '';	//_	licence under which 3rd party provided this to us [string]
	var $licenceurl = '';	//_	3rd party licence location [string]
	var	$meta = '';			//_	any additional content-specific metadata [string]
	var $createdOn;			//_	datetime
	var $createdBy;			//_	ref:users-user
	var $editedOn;			//_	datetime
	var $editedBy;			//_	ref:users-user
	var $alias;				//_	varchar(255), not yet used [string]
	var $shared ='yes';		//_	ref:users-user

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Alias object [string]

	function Lessons_Stub($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();					// initialise table schema
		if ('' != $UID) { $this->load($UID); }					// try load an object, if given
		if (false == $this->loaded) { 
			$this->loadArray($db->makeBlank($this->dbSchema));
			$this->loaded = false;
		}
	}


	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Alias object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$ary = $db->load($UID, $this->dbSchema);
		if ($ary != false) { $this->loadArray($ary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Alias object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
	
		$checkExists = array(
			'UID', 'document', 'course', 'attriburl', 'licencename', 'licenceurl', 'downloadfrom',
			'file', 'description', 'cover', 'thumb', 'file',
			'attribname', 'attriburl', 'createdOn', 'createdBy', 'editedOn', 'editedBy', 'alias'
		);

		foreach($checkExists as $item) {
			if (false == array_key_exists($item, $ary)) { $ary[$item] = ''; }
		}

		$this->data = $ary;
		$this->UID = $ary['UID'];
		$this->course = $ary['course'];
		$this->document = $ary['document'];
		$this->title = $ary['title'];
		$this->type = $ary['type'];
		$this->description = $ary['description'];
		$this->cover = $ary['cover'];
		$this->thumb = $ary['thumb'];
		$this->file = $ary['file'];
		$this->attribname = $ary['attribname'];
		$this->attriburl = $ary['attriburl'];
		$this->licencename = $ary['licencename'];
		$this->licenceurl = $ary['licenceurl'];
		$this->meta = $ary['meta'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];

		if (true == array_key_exists('uid', $ary)) { $this->UID = $ary['uid']; }
		if ('' == $ary['document']) { $ary['document'] = $ary['UID']; }

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]

	function save() {
		global $kapenta;
		global $db;

		$report = $this->verify();
		if ('' != $report) { return $report; }

		$check = $db->save($this->toArray(), $this->dbSchema);

		if (true == $check) { return ''; }
		else { return "Database error: " . $db->lasterr . "\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';

		$this->alias = trim($this->alias);
		$this->aliaslc = strtolower($this->alias);
		if ('' == $this->UID) { $report .= "No UID.\n"; }
		if ('' == $this->title) { $report .= "This document has no title.\n"; }
		if ('' == $this->file) { $report .= "No file.\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'lessons';
		$dbSchema['model'] = 'lessons_stub';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'course' => 'VARCHAR(50)',
			'document' => 'VARCHAR(50)',
			'title' => 'VARCHAR(255)',
			'type' => 'VARCHAR(30)',
			'description' => 'TEXT',
			'cover' => 'VARCHAR(255)',
			'thumb' => 'VARCHAR(255)',
			'file' => 'VARCHAR(255)',
			'attribname' => 'VARCHAR(255)',
			'attriburl' => 'VARCHAR(255)',
			'licencename' => 'VARCHAR(80)',
			'licenceurl' => 'VARCHAR(255)',
			'meta' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'course' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array();

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$ser = array(
			'UID' => $this->UID,
			'course' => $this->course,
			'document' => $this->document,
			'title' => $this->title,
			'type' => $this->title,
			'description' => $this->description,
			'cover' => $this->cover,
			'thumb' => $this->thumb,
			'file' => $this->file,
			'attribname' => $this->attribname,
			'attriburl' => $this->attriburl,
			'licencename' => $this->licencename,
			'licenceurl' => $this->licenceurl,
			'meta' => $this->meta,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $ser;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended arry of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('lessons', 'lessons_stub', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%lessons/item/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more >gt; ]</a>";
		}

		if (true == $user->authHas('lessons', 'lessons_stub', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%lessons/item/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('lessons', 'lessons_stub', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%alias/delalias/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database and memory cache
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		global $db;
		if (false == $this->loaded) { return false; }
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }

		if (true == $kapenta->mcEnabled) {
			//TODO: fix up cache
		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. check whether this resource has a cover image
	//----------------------------------------------------------------------------------------------
	//returns: true if cover exists, false on failure [bool]

	function hasCover() {
		global $kapenta;
		if ('' === $this->cover) { return false; }
		if (false === $kapenta->fs->exists($this->cover, true)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. check whether this resource has a thumbnail
	//----------------------------------------------------------------------------------------------
	//returns: true if thumbnail exists, false on failure [bool]

	function hasThumb() {
		global $kapenta;
		if ('' === $this->thumb) { return false; }
		if (false === $kapenta->fs->exists($this->thumb, true)) { return false; }
		return true;
	}


}

?>
