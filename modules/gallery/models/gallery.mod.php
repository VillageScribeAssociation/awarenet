<?

//--------------------------------------------------------------------------------------------------
//*	object to represent user galleries
//--------------------------------------------------------------------------------------------------

//+	note that galleries can nest, parent may be 'root' or the UID of another gallery, users can
//+	edit their own galleries, admins can edit any gallery.

class Gallery {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database table structure [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or recordAlias of a gallery [string]

	function Gallery($raUID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['parent'] = 'root';
		$this->data['title'] = 'New Gallery ' . $this->data['UID'];
		$this->data['imagecount'] = 0;
		if ($raUID != '') { $this->load($raUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or recordAlias of a gallery record [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		$ary = dbLoadRa('gallery', $raUID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('gallery', $d['UID'], $d['title'], 'gallery');
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';

		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'gallery';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'parent' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'imagecount' => 'BIGINT(20)',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'parent' => 10, 'recordAlias' => '10' );

		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		$ary = $this->data;

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
		if ($user->data['ofGroup'] == 'admin') { $auth = true; }
		if ($user->data['UID'] == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('gallery', 'show', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%gallery/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%gallery/edit/' . $ary['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%gallery/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new gallery]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%gallery/addchild/" . $ary['recordAlias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child gallery]</a>";  
			$ary['delUrl'] = "%%serverPath%%gallery/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete gallery]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['date']));
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['galleryTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------

		$ary['summary'] = strip_tags($ary['description']);
		$ary['summary'] = substr($ary['summary'], 0, 1000) . '...';
		$ary['summary'] = substr(strip_tags($ary['description']), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new User($ary['createdBy']);
		$ary['userName'] = $model->data['firstname'] . ' ' . $model->data['surname'];		
		$ary['userRa'] = $model->data['recordAlias'];
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.inc.php

	function install() {
		$report = "<h3>Installing Gallery Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create gallery table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('gallery') == false) {	
			echo "installing Gallery module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created gallery table and indices...<br/>';
		} else {
			$this->report .= 'Gallery table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current record
	//----------------------------------------------------------------------------------------------

	function delete() {	
		// delete this record and any recordAliases
		dbDelete('gallery', $this->data['UID']);

		// allow other modules to respond to this event
		$args = array('module' => 'gallery', 'UID' => $this->data['UID']);
		eventSendAll('object_deleted', $args);
	}

	//----------------------------------------------------------------------------------------------
	//.	update image count of the current gallery
	//----------------------------------------------------------------------------------------------

	function updateImageCount() {
		$block = "[[:images::count::refModule=gallery::refUID=" . $this->data['UID'] . ":]]";
		$this->data['imagecount'] = expandBlocks($block, '');
		$this->save();
	}

}

?>
