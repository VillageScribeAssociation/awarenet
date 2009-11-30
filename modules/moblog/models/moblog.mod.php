<?

//--------------------------------------------------------------------------------------------------
//	object for managing moblog posts
//--------------------------------------------------------------------------------------------------
// NOTES:
// content can contain a special '{fold}' keyword, which determines how much of the post is included
// in the summary.
//
// 'published' field allows posts to be invisible to the public while they are being composed or 
// until they should be released, values can be 'yes', 'no'.  admins and the user which created a 
// post can see unpublished content.

class Moblog {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;		// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Moblog($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] == 'New Post';
		$this->data['published'] == 'no';
		$this->data['school'] = $user->data['school'];
		$this->data['grade'] = $user->data['grade'];
		$this->data['hitcount'] = '0';
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('moblog', $uid);
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

		$ra = raSetAlias('moblog', $this->data['UID'], $this->data['title'], 'moblog');
		$this->data['recordAlias'] = $ra;
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';

		if (strlen($this->data['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		if (trim($this->data['title']) == '') 
			{ $verify .= "Please choose a title for this post.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'moblog';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'school' => 'VARCHAR(30)',
			'grade' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'published' => 'VARCHAR(30)',
			'hitcount' => 'BIGINT',
			'createdOn' => 'DATETIME',	
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array(
			'UID' => '10', 
			'school' => '10',
			'grade' => '6',  
			'recordAlias' => '20', 
			'school' => '20');

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
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	authorisation
		//------------------------------------------------------------------------------------------

		$editAuth = false;
		if ($user->data['ofGroup'] == 'admin') { $editAuth = true; }
		if ($user->data['UID'] == $ary['createdBy']) { $editAuth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('moblog', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%moblog/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[permalink]</a>"; 
		}

		if ($editAuth) {
			$ary['editUrl'] =  '%%serverPath%%moblog/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if ($editAuth) {
			$ary['delUrl'] =  '%%serverPath%%moblog/confirmdelete/UID_' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}

		if ($editAuth) { 
			$ary['newUrl'] = "%%serverPath%%moblog/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[new post]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	namespace conflict
		//------------------------------------------------------------------------------------------

		$ary['mbTitle'] = $ary['title'];
		$ary['mbContent'] = $ary['content'];

		//------------------------------------------------------------------------------------------
		//	user
		//------------------------------------------------------------------------------------------

		$model = new Users($ary['createdBy']);
		$ary['userName'] = $model->data['firstname'] . ' ' . $model->data['surname'];
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $model->data['recordAlias'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";

		//------------------------------------------------------------------------------------------
		//	unpublished items
		//------------------------------------------------------------------------------------------

		$ary['unpublished'] = '';
		if ($ary['published'] == 'no')
			{ $ary['unpublished'] = "<font color='red'><b>(unpublished)</b></font>"; }

		//------------------------------------------------------------------------------------------
		//	summarysm (first 300 chars, for nav summary)
		//------------------------------------------------------------------------------------------

		$ary['summarysm'] = substr(strip_tags(strip_blocks($ary['content'])), 0, 300) . '...';

		//------------------------------------------------------------------------------------------
		//	summary (clipped to {fold})
		//------------------------------------------------------------------------------------------

		$ary['aboveFold'] = trim($this->data['content']);
		$foldPos = strpos($ary['aboveFold'], '{fold}');
		if ($foldPos > 0) { $ary['aboveFold'] = substr($ary['aboveFold'], 0, $foldPos);	}
		$ary['aboveFold'] = str_replace("\n", "<br/>\n", $ary['aboveFold']);

		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['content']);
		$ary['contentHtml'] = str_replace('{fold}', '', $ary['contentHtml']);

		//------------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
	
		$ary['content'] = str_replace('{fold}', '', $ary['content']);

		//------------------------------------------------------------------------------------------
		//	all done
		//------------------------------------------------------------------------------------------

		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Moblog Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('moblog') == false) {	
			echo "installing moblog module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created moblog table and indices...<br/>';
		} else {
			$this->report .= 'moblog table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a blog post
	//----------------------------------------------------------------------------------------------

	function delete() {	
		// delete this record and any recordAliases
		dbDelete('moblog', $this->data['UID']);	

		// allow other modules to respond to this event
		$args = array('module' => 'moblog', 'UID' => $this->data['UID']);
		eventSendAll('object_deleted', $args);
	}

	//----------------------------------------------------------------------------------------------
	//	increment hit count
	//----------------------------------------------------------------------------------------------

	function incHitCount() {
		$this->data['hitcount'] = $this->data['hitcount'] + 1;
		$this->save();
	}

}

?>
