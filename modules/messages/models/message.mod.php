<?

//--------------------------------------------------------------------------------------------------
//	object for personal messages
//--------------------------------------------------------------------------------------------------

class Message {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Message($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] = 'New Message ' . $this->data['UID'];
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('messages', $uid);
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
		$dbSchema['table'] = 'messages';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'owner' => 'VARCHAR(30)',
			'folder' => 'VARCHAR(30)',
			'fromUID' => 'VARCHAR(30)',
			'toUID' => 'VARCHAR(30)',
			'cc' => 'TEXT',
			'title' => 'VARCHAR(255)',
			're' => 'VARCHAR(30)',
			'content' => 'TEXT',
			'status' => 'VARCHAR(10)',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME' );

		$dbSchema['indices'] = array('UID' => '10', 'fromUID' => 10, 'toUID' => '10' );
		$dbSchema['nodiff'] = array('UID');
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

		$ary['editUrl'] = '';		$ary['editLink'] = '';
		$ary['viewUrl'] = '';		$ary['viewLink'] = '';
		$ary['newUrl'] = '';		$ary['newLink'] = '';
		$ary['delUrl'] = '';		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	check authorisation
		//------------------------------------------------------------------------------------------

		$auth = false;
		if ($user->data['ofGroup'] == 'admin') { $auth = true; }
		if ($user->data['UID'] == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('messages', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%messages/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[see all threads &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%messages/edit/' . $ary['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%messages/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new messages]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%messages/addchild/" . $ary['recordAlias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child messages]</a>";  
			$ary['delUrl'] = "%%serverPath%%messages/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete messages]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['date']));
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['messageTitle'] = $ary['title'];

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
		$report = "<h3>Installing Messages Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create messages table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('messages') == false) {	
			echo "installing messages module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created messages table and indices...<br/>';
		} else {
			$this->report .= 'messages table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		//------------------------------------------------------------------------------------------
		//	delete any images associated with this message
		//------------------------------------------------------------------------------------------

		//------------------------------------------------------------------------------------------
		//	delete any files associated with this message
		//------------------------------------------------------------------------------------------

		//------------------------------------------------------------------------------------------
		//	delete this record
		//------------------------------------------------------------------------------------------		
		raDeleteAll('messages', $this->data['UID']);
		dbDelete('messages', $this->data['UID']);
	}

}

?>
