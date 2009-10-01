<?

//--------------------------------------------------------------------------------------------------
//	object for managing comments
//--------------------------------------------------------------------------------------------------
//	comments are owned by some record on another module (allowing comments to have comments would
//  cause threading, but we can do without that for now, complicates and clutters things, and
//	causes discussions to fragment).

//	comments can be retracted by whoever made them, and can be edited or deleted by admins

class Comment {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Comment($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] = 'New comment';
		$this->data['createdOn'] = mysql_datetime();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('comments', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//	save a record (and send notifications if they have not been sent)
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
		if (strlen($this->data['comment']) < 2) { $verify .= "Nothing said.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'comments';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'refModule' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(30)',
			'comment' => 'TEXT',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;

	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		global $user;
		$ary = $this->data;
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('comments', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%comments/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (authHas('comments', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%comments/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('comments', 'edit', $this->data)) {
			$ary['delUrl'] =  '%%serverPath%%comments/confirmdelete/UID_' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (authHas('comments', 'new', $this->data)) { 
			$ary['newUrl'] = "%%serverPath%%comments/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new comment]</a>"; 
		}

		$createdBy = new Users($ary['createdBy']);
		$ary['userUrl'] = '/users/profile/' . $createdBy->data['recordAlias'];
		$ary['userName'] = $createdBy->data['firstname'] . ' ' . $createdBy->data['surname'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";

		//------------------------------------------------------------------------------------------
		//	retraction URL
		//------------------------------------------------------------------------------------------

		$ary['retractUrl'] = '';
		$ary['retractLink'] = '';

		if (($user->data['UID'] == $ary['createdBy']) OR (authHas('comments', 'retractall', ''))) {
			$ary['retractUrl'] = '/comments/retract/' . $this->data['UID'];
			$ary['retractLink'] = "<a href='" . $ary['retractUrl'] . "'>[retract]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	user details
		//------------------------------------------------------------------------------------------

		$model = new Users($this->data['createdBy']);
		$ary['userName'] = $model->data['firstname'] . ' ' . $model->data['surname'];
		$ary['userUrl'] = '/users/profile/' . $model->data['recordAlias'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";
		$ary['userThumb'] = "[[:users::avatar::size=thumbsm::link=no"
						  . "::userUID=" . $model->data['UID'] . ':]]';

		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------

		$ary['contentHtml'] = str_replace(">\n", ">", $ary['content']);
		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);
		$ary['summary'] = substr(strip_tags($ary['content']), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
		
		$ary['contentJs'] = $ary['comment'];
		$ary['contentJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Comments Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create comments table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('comments') == false) {	
			echo "installing comments module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created comments table and indices...<br/>';
		} else {
			$this->report .= 'comments table already exists...<br/>';	
		}

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('comments', $this->data['UID']);
	}

}

?>
