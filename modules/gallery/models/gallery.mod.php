<?

//--------------------------------------------------------------------------------------------------
//	object for user galleries
//--------------------------------------------------------------------------------------------------

//	note that galleries can nest, parent may be 'root' or the UID of another gallery, users can
//	edit their own galleries, admins can edit any gallery.

class Gallery {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Gallery($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['parent'] = 'root';
		$this->data['title'] = 'New Gallery ' . $this->data['UID'];
		$this->data['comments'] = 'no';
		$this->data['createdBy'] = $user->data['UID'];
		$this->data['createdOn'] = mysql_datetime(time());
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('gallery', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('gallery', $d['UID'], $d['title'], 'gallery');
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';

		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'gallery';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'parent' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'parent' => 10, 'recordAlias' => '20' );

		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

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
	
		//------------------------------------------------------------------------------------------
		//	format for WYSWYG editor
		//------------------------------------------------------------------------------------------

		$ary['descriptionJs'] = $ary['description'];
		$ary['descriptionJs'] = str_replace("'", '--squote--', $ary['descriptionJs']);
		$ary['descriptionJs'] = str_replace("'", '--dquote--', $ary['descriptionJs']);
	
		$ary['summary'] = substr(strip_tags($ary['description']), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new Users($ary['createdBy']);
		$ary['userName'] = $model->data['firstname'] . ' ' . $model->data['surname'];		
		$ary['userRa'] = $model->data['recordAlias'];
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Gallery Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create Gallery table if it does not exist
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
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		$sql = "delete from images where refModule='gallery' and refUID='" . $this->data['UID']. "'";
		dbQuery($sql);
		
		raDeleteAll('gallery', $this->data['UID']);
		dbDelete('gallery', $this->data['UID']);
	}

}

?>
