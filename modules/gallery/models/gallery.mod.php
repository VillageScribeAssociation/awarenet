<?

//--------------------------------------------------------------------------------------------------
//*	object to represent user galleries
//--------------------------------------------------------------------------------------------------
//TODO: consider moveing school association into a separate index record.

class Gallery_Gallery {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title [string]
	var $description;		//_ wyswyg [string]
	var $imagecount;		//_ bigint [string]
	var $ownerName;			//_ name of user who created this gallery [string]
	var $schoolUID;			//_ for grouping [string]
	var $schoolName;		//_ for sorting [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Gallery object [string]

	function Gallery_Gallery($raUID = '') {
		global $kapenta;
		global $kapenta;
		global $theme;

		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Gallery ' . $this->UID;
			$this->imagecount = 0;
			$this->ownerName = $kapenta->user->getName();			// for listing by creator
			$this->schoolUID = $kapenta->user->school;
			$this->schoolName = $kapenta->user->getSchoolName();		// for listing by school
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Gallery object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $kapenta;
		$objary = $kapenta->db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Gallery object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->description = $ary['description'];
		$this->imagecount = $ary['imagecount'];
		$this->ownerName = $ary['ownerName'];
		$this->schoolUID = $ary['schoolUID'];
		$this->schoolName = $ary['schoolName'];
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
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('gallery', 'gallery_gallery', $this->UID, $this->title);
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
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
		$dbSchema['module'] = 'gallery';
		$dbSchema['model'] = 'gallery_gallery';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'imagecount' => 'BIGINT(20)',
			'ownerName' => 'VARCHAR(255)',
			'schoolUID' => 'VARCHAR(40)',
			'schoolName' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'title' => '10',
			'imagecount' => '',
			'ownerName' => '10',
			'schoolUID' => '10',
			'schoolName' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//no revision history will be kept for these fields
		$dbSchema['nodiff'] = array('UID', 'imagecount');

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
			'imagecount' => $this->imagecount,
			'ownerName' => $this->ownerName,
			'schoolUID' => $this->schoolUID,
			'schoolName' => $this->schoolName,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $kapenta;
		global $theme;

		$ary = $this->toArray();

		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['addChildUrl'] = '';
		$ary['addChildLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	check authorisation
		//------------------------------------------------------------------------------------------

		$auth = false;
		if ('admin' == $kapenta->user->role) { $auth = true; }
		if ($kapenta->user->UID == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if ($kapenta->user->authHas('gallery', 'gallery_gallery', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%gallery/' . $ary['alias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
			$ary['titleLink'] = "<a href='" . $ary['viewUrl'] . "'>" . $ary['title'] . "</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%gallery/edit/' . $ary['alias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%gallery/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new gallery]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%gallery/addchild/" . $ary['alias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child gallery]</a>";  
			$ary['delUrl'] = "%%serverPath%%gallery/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete gallery]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	upper case title TODO: discover if this is ever used, remove if not
		//------------------------------------------------------------------------------------------
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['galleryTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------
		//TODO: remove this to view?
		$ary['summary'] = $theme->makeSummary($ary['description'], 400);

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------
		//TODO: remove this, use views of user module instead
		$model = new Users_User($ary['createdBy']);
		$ary['userName'] = $model->firstname . ' ' . $model->surname;		
		$ary['userRa'] = $model->alias;
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	update image count of the current gallery
	//----------------------------------------------------------------------------------------------

	function updateImageCount() {
		global $theme;
		$block = "[[:images::count::refModule=gallery::refUID=" . $this->UID . ":]]";
		$this->imagecount = $theme->expandBlocks($block, '');
		$this->save();
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

}

?>
