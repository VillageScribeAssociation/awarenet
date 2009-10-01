<?

//--------------------------------------------------------------------------------------------------
//	object for managing site users.  This module is required.
//--------------------------------------------------------------------------------------------------
//	permissions field is formatted in lines: |auto/special|modulename|perm|cond| where condition
//	may be blank or something like: %%createdBy%%=%%user.UID%%
//	'auto' permissions are generated from module.xml.php files, 'special' permissions are manually
//	granted by an administrator.

//	TODO: consider a better way for admins to decide on profile fields

require_once($installPath . 'modules/users/models/friendships.mod.php');

class Users {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure
	var $permissions;	// actions this user is authorised to take
	var $profile;		// stored as XML in a text field

	var $profileFields = 'interests|hometown|music|goals|books|also';

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Users($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['ofGroup'] == 'install';
		$this->initProfile();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('users', $uid, 'true');
		return $this->loadArray($ary);
	}

	function loadArray($ary) {
		if ($ary == false) { return false; }
		$this->data = $ary; 
		$this->expandPermissions();
		$this->expandProfile();
		return true; 
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$this->collapseProfile();
		$verify = $this->verify();
		if ($verify == '') { 
			$this->data['recordAlias'] = raSetAlias('users', $this->data['UID'], $this->data['username'], 'users');
			dbSave($this->data, $this->dbSchema); 
		} 
		else { return $verify; }	
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		if (strlen($d['firstname']) < 1) 
			{ $verify .= "Please enter the users first name.\n"; }
		if (strlen($d['surname']) < 1) 
			{ $verify .= "Please enter the users surname.\n"; }
		if (strlen($d['username']) < 4) 
			{ $verify .= "Please enter a username (4 characters or more).\n"; }
		if (strlen($d['password']) < 6) 
			{ $verify .= "Please enter a password (6 characters or more).\n"; }

		if (dbRecordExists('schools', $this->data['school']) == false) 
			{ $verify .= "Please select a school for this user.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'users';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'ofGroup' => 'VARCHAR(10)',
			'school' => 'VARCHAR(30)',
			'grade' => 'VARCHAR(30)',
			'firstname' => 'VARCHAR(100)',	
			'surname' => 'VARCHAR(100)',
			'username' => 'VARCHAR(30)',	
			'password' => 'VARCHAR(255)',
			'lang' => 'VARCHAR(30)',	
			'profile' => 'TEXT',
			'permissions' => 'TEXT',	
			'lastOnline' => 'DATETIME',
			'createdOn' => 'DATETIME',	
			'createdBy' => 'VARCHAR(30)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
		$dbSchema['nodiff'] = array('UID', 'lastOnline', 'recordAlias', 'password');

		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		global $user;
		$ary = $this->data;
		$ary['fullName'] = trim($ary['firstname'] . ' ' . $ary['surname']);

		$ary['editUrl'] = '';	$ary['editLink'] = '';
		$ary['editProfileUrl'] = '';	$ary['editProfileLink'] = '';
		$ary['viewUrl'] = '';	$ary['viewLink'] = '';
		$ary['delUrl'] = '';	$ary['delLink'] = '';
		$ary['newUrl'] = '';	$ary['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('users', 'view', '') == false) { echo "no permission to view users.<br/>"; }

		if ((authHas('users', 'view', '') == true) OR ($user->data['UID'] == $this->data['UID'])) { 
			$ary['viewUrl'] = '%%serverPath%%users/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[profile]</a>"; 
		}

		if ((authHas('users', 'edit', $this->data)) OR ($user->data['UID'] == $this->data['UID'])) {
			$ary['editUrl'] =  '%%serverPath%%users/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['editProfileUrl'] =  '%%serverPath%%users/editprofile/' . $this->data['recordAlias'];
			$ary['editProfileLink'] = "<a href='" . $ary['editProfileUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('users', 'edit', $this->data)) { // should users be able to delete their profile
			$ary['delUrl'] =  '%%serverPath%%users/confirmdelete/UID_' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (authHas('groups', 'new', $this->data)) { 
			$ary['newUrl'] = "%%serverPath%%users/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new group]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	chatlink - if lastOnline < 20 seconds ago
		//------------------------------------------------------------------------------------------

		$ary['chatLink'] = "<a href='#' onClick=\"cookieAddWindow('" 
						. $ary['UID'] . "', '100', '100'); cookieSetChatUpdate();\">[chat]</a>";

		$timeDiff = time() - strtotime($ary['lastOnline']);
		if ($timeDiff > 7250) { $ary['chatLink'] = '[offline]'; }
		if ($user->data['UID'] == $ary['UID']) { $ary['chatLink'] = '[online]'; }

		//------------------------------------------------------------------------------------------
		//	add profile fields
		//------------------------------------------------------------------------------------------

		foreach($this->profile as $field => $value) { 
			if (trim($value) == '') {
				$ary['profile_' . $field] = ''; 
			} else {
				$ary['profile_' . $field] = "<p><b>" . $field . ":</b> "
										  .  str_replace("\n", "<br/>\n", $value) . "</p>\n"; 
			}
		}

		//------------------------------------------------------------------------------------------
		//	look up school info
		//------------------------------------------------------------------------------------------
	
		require_once($installPath . 'modules/schools/models/schools.mod.php');
		$mySchool = new School($ary['school']);

		$ary['schoolName'] = $mySchool->data['name'];
		$ary['schoolCountry'] = $mySchool->data['country'];
		$ary['schoolRecordAlias'] = $mySchool->data['recordAlias'];
		$ary['schoolUrl'] = '%%serverPath%%schools/' . $mySchool->data['recordAlias'];
		$ary['schoolLink'] = "<a href='" . $ary['schoolUrl'] . "'>" . $mySchool->data['name'] . "</a>";

		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Users Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create users table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('users') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created users table and indices...<br/>';
		} else {
			$this->report .= 'users table already exists...<br/>';	
		}

		//------------------------------------------------------------------------------------------
		//	create 'admin' and 'public user' records
		//------------------------------------------------------------------------------------------

		if (dbRecordExists('users', 'admin') == false) {
			$d = array();
			$d['UID'] = 'admin';
			$d['ofGroup'] = 'admin';
			$d['firstname'] = 'System';
			$d['surname'] = 'Administrator';
			$d['username'] = 'admin';
			$d['password'] = sha1('admin' . $d['UID']);
			$d['lang'] = 'en';
			$d['profile'] = '';
			$d['lastOnline'] = mysql_datetime();
			$d['createdOn'] = mysql_datetime();
			$d['createdBy'] = $d['UID'];
			$d['recordAlias'] = 'admin';
			$this->data = $d;
			$this->save();
			$this->report .= "created administrator record (user:admin pass:admin)<br/>";
		}

		if (dbRecordExists('users', 'public') == false) {
			$d = array();
			$d['UID'] = 'public';
			$d['ofGroup'] = 'public';
			$d['firstname'] = 'Public';
			$d['surname'] = 'User';
			$d['username'] = 'public';
			$d['password'] = sha1('public' . $d['UID']);
			$d['lang'] = 'en';
			$d['profile'] = '';
			$d['lastOnline'] = mysql_datetime();
			$d['createdOn'] = mysql_datetime();
			$d['createdBy'] = $d['UID'];
			$d['recordAlias'] = 'public';
			$this->data = $d;
			$this->save();
			$this->report .= "created public user record (user:public pass:public)<br/>";
		}

		//------------------------------------------------------------------------------------------
		//	create friendships table
		//------------------------------------------------------------------------------------------
	
		$model = new Friendship();
		$this->report .= $model->install();	

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	read [permissions] into array for authentication
	//----------------------------------------------------------------------------------------------

	function expandPermissions() {
		$this->permissions = array();
		$lines = explode("\n", $this->data['permissions']);
		foreach($lines as $line) {
		  if (strpos('_' . $line, '|') != false) {
			$parts = explode("|", $line);
			$perm = array();

			$perm['type'] = $parts[1];
			$perm['module'] = $parts[2];
			$perm['permission'] = $parts[3];
			$perm['condition'] = $parts[4];
	
			if (array_key_exists($perm['module'], $this->permissions) == false) {
				$this->permissions[$perm['module']] = array();
			}

			$this->permissions[$perm['module']][] = $perm;
		  }
		}
	}

	//----------------------------------------------------------------------------------------------
	//	collapse permissions array back to string
	//----------------------------------------------------------------------------------------------

	function collapsePermissions() {
		$txt = '';
		foreach($this->permissions as $moduleName => $perm) {
			$txt .= '|' . $perm['type'] . '|' . $perm['module'] . '|' 
			      . '|' . $perm['permission'] . '|' . $perm['condition'] . "|\n";
		}
		$this->data['permissions'] = $txt;
	}

	//----------------------------------------------------------------------------------------------
	//	create blank profile (new records)
	//----------------------------------------------------------------------------------------------

	function initProfile() {
		$pF = explode('|', $this->profileFields);
		$this->profile = array();
		foreach($pF as $field) {
			$this->profile[$field] = '';
		}
	}

	//----------------------------------------------------------------------------------------------
	//	expand profile
	//----------------------------------------------------------------------------------------------

	function expandProfile() {
		// initialize all fields blank
		$this->profile = array();
		$pF = explode('|', $this->profileFields);
		foreach($pF as $profileField) { $this->profile[$profileField] = ''; }

		// read from profile xml
		$xe = new XmlEntity($this->data['profile']);
		foreach($xe->children as $index => $child) 
			{ $this->profile[$child->type] = $child->value;	}
	}

	//----------------------------------------------------------------------------------------------
	//	collapse profile
	//----------------------------------------------------------------------------------------------

	function collapseProfile() {
		$xml = '';
		foreach($this->profile as $field => $value) 
			{ $xml .= "<" . $field . ">" . strip_tags($value) . "</" . $field . ">\n"; }

		$this->data['profile'] = "<profile>\n$xml</profile>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	find users in the same grade as this user, returns array of [UID][row] objects for loadarray
	//----------------------------------------------------------------------------------------------

	function sameGrade() {
		$retVal = array();
		$sql = "select * from users "
			 . "where school='" . $this->data['school'] ."' "
			 . "and grade='" . $this->data['grade'] . "' order by surname, firstname";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$retVal[$row['UID']] = $row;
		}
	
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	discover if a user is an admin of a group
	//----------------------------------------------------------------------------------------------

	function isGroupAdmin($groupUID) {
		$sql = "select * from groupmembers "
			 . "where userUID='" . $this->data['UID'] . "' "
			 . "and groupUID='" . sqlMarkup($groupUID) . "' "
			 . "and admin='yes'";

		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	get users friends
	//----------------------------------------------------------------------------------------------

	function getFriends() {
		$retVal = array();
		$sql = "select * from friendships "
			 . "where userUID='" . $this->data['UID'] . "' and status='confirmed'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { $retVal[$row['UID']] = sqlRMArray($row); }
		return $retVal;
	}

	function getFriendRequestsIn() {
		$retVal = array();
		$sql = "select * from friendships "
			 . "where friendUID='" . $this->data['UID'] . "' and status='unconfirmed'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { $retVal[$row['UID']] = sqlRMArray($row); }
		return $retVal;
	}

	function getFriendRequestsOut() {
		$retVal = array();
		$sql = "select * from friendships "
			 . "where userUID='" . $this->data['UID'] . "' and status='unconfirmed'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { $retVal[$row['UID']] = sqlRMArray($row); }
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	delete this user
	//----------------------------------------------------------------------------------------------
	function delete() {
		$UID = $this->data['UID'];

		dbQuery("delete from announcements where createdBy='" . $UID . "'");	
		dbQuery("delete from comments where createdBy='" . $UID . "'");
		dbQuery("delete from calendar where createdBy='" . $UID . "'");
		dbQuery("delete from calendar where createdBy='" . $UID . "'");
		dbQuery("delete from chat where user='" . $UID . "'");

		// TODO: delete files and images
		
		dbQuery("delete from forumreplies where createdBy='" . $UID . "'");
		dbQuery("delete from friendships where userUID='" . $UID . "'");
		dbQuery("delete from friendships where friendUID='" . $UID . "'");
		dbQuery("delete from forums where createdBy='" . $UID . "'");
		dbQuery("delete from forumthreads where createdBy='" . $UID . "'");
		dbQuery("delete from groupmembers where userUID='" . $UID . "'");
		dbQuery("delete from messages where toUID='" . $UID . "'");
		dbQuery("delete from messages where fromUID='" . $UID . "'");
		dbQuery("delete from moblog where createdBy='" . $UID . "'");
		dbQuery("delete from notices where user='" . $UID . "'");
		dbQuery("delete from projectmembers where userUID='" . $UID . "'");
		dbQuery("delete from projects where createdBy='" . $UID . "'");

		// delete this record

		dbQuery("delete from users where UID='" . $UID . "'");

	}

	//----------------------------------------------------------------------------------------------
	//	shorthand
	//----------------------------------------------------------------------------------------------

	function getName() { return trim($this->data['firstname'] . ' ' . $this->data['surname']);	}
	function getUrl() { return "%%serverPath%%users/profile/". $this->data['recordAlias']; }
	function getNameLink() { return "<a href='". $this->getUrl() ."'>". $this->getName() ."</a>"; }



}

?>
