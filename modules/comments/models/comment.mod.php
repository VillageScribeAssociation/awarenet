<?

//--------------------------------------------------------------------------------------------------
//*	object for to represent user comments
//--------------------------------------------------------------------------------------------------
//+	comments are owned by some record on another module (allowing comments to have comments would
//+  cause threading, but we can do without that for now, complicates and clutters things, and
//+	causes discussions to fragment).
//+
//+	comments can be retracted by whoever made them, and can be edited or deleted by admins

class Comments_Comment {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $refModule;			//_ module [string]
	var $refModel;			//_ model [string]
	var $refUID;			//_ ref:* [string]
	var $parent;			//_ ref:comments_comment [string]
	var $comment;			//_ wyswyg [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Comment object [string]

	function Comments_Comment($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);			// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Comment object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Comment object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->parent = $ary['parent'];
		$this->comment = $ary['comment'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
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
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		if (strlen($this->comment) < 2) { $report .= "Nothing said.\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'comments';
		$dbSchema['model'] = 'comments_comment';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'VARCHAR(50)',
			'refModel' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(33)',
			'parent' => 'VARCHAR(33)',
			'comment' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'parent' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array();

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
			'parent' => $this->parent,
			'comment' => $this->comment,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $kapenta;
		global $theme;

		$ary = $this->toArray();
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';

		$ary['replyJsLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $kapenta->user->authHas('comments', 'comments_comment', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%comments/' . $this->UID;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (true == $kapenta->user->authHas('comments', 'comments_comment', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%comments/edit/' . $this->UID;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (true == $kapenta->user->authHas('comments', 'comments_comment', 'edit', $this->UID)) {
			$ary['delUrl'] = '%%serverPath%%comments/confirmdelete/UID_'. $this->UID .'/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (true == $kapenta->user->authHas('comments', 'comments_comment', 'new', $this->UID)) { 
			$ary['newUrl'] = "%%serverPath%%comments/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new comment]</a>"; 
		}

		if (
			('' == $ary['parent']) && 
			($kapenta->user->authHas($this->refModule, $this->refModel, 'comments-add', $this->refUID))
		) {
			$ary['replyJsLink'] = ''
			 . "<a href=\"javascript:comments_showReplyInline('" . $this->UID . "');\">[reply]</a>"; 			
		}

		$createdBy = new Users_User($ary['createdBy']);
		$ary['userUrl'] = '/users/profile/' . $createdBy->UID;
		$ary['userName'] = $createdBy->getName();
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";

		//-----------------------------------------------------------------------------------------
		//	retraction URL
		//-----------------------------------------------------------------------------------------

		$ary['retractUrl'] = '';
		$ary['retractLink'] = '';

		if ( ($kapenta->user->UID == $ary['createdBy']) 
			OR (true == $kapenta->user->authHas('comments', 'Comment_Comment', 'retractall')) ) {
			$ary['retractUrl'] = '/comments/retract/' . $this->UID;
			$ary['retractLink'] = "<a href='" . $ary['retractUrl'] . "'>[retract]</a>";
		}

		//-----------------------------------------------------------------------------------------
		//	user details
		//-----------------------------------------------------------------------------------------

		$model = new Users_User($this->createdBy);
		$ary['userName'] = $model->firstname . ' ' . $model->surname;
		$ary['userUrl'] = '/users/profile/' . $model->alias;
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";
		$ary['userThumb'] = "[[:images::default::refModule=users::size=thumbsm::link=no"
						  . "::refUID=" . $model->UID . ':]]';

		//-----------------------------------------------------------------------------------------
		//	summary 
		//-----------------------------------------------------------------------------------------

		$ary['contentHtml'] = str_replace(">\n", ">", $ary['comment']);
		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);
		$ary['summary'] = $theme->makeSummary($ary['comment'], 400);

		//-----------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//-----------------------------------------------------------------------------------------
		
		$ary['contentJs'] = $ary['comment'];
		$ary['contentJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);
	
		return $ary;
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
