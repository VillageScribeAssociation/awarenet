<?

//--------------------------------------------------------------------------------------------------
//*	object to represent site users.  This module is required.
//--------------------------------------------------------------------------------------------------
//+	permissions field is formatted in lines: |auto/special|modulename|perm|cond| where condition
//+	may be blank or something like: %%createdBy%%=%%user.UID%%
//+	'auto' permissions are generated from module.xml.php files, 'special' permissions are manually
//+	granted by an administrator.
//+
//+	PERMISSIONS
//+
//+	Users and groups may be granted permissions on individual objects or more often on entire 
//+	classes of objects.  Permissions are defined by the module to which an object belongs, and 
//+	may be exported to other modules (ie, inherited permisisons)
//+
//+ Inheritable permissions are exported by the module which maintains an owned object, say a 
//+	comment which belongs to a blog post.  They are granted according to the relationship between
//+	the owner object and a user.  For example, a user may have permission to delete an image 
//+	belonging to a blog post because the user created that post.  These relationships between 
//+ objects and users are defined by the module which owns an object.
//+
//+	Format of basic permissions:
//+	p|module|model|permission								- blanket permission
//+	c|module|model|permission|(if)|relationship				- conditional permission
//+	
//+	Note that in the case of inherited permissions, the first object is the owner, the second
//+	is the owned object, the permission applies to the owned object, and the condition to the
//+	owner object.
//+
//+ Examples:
//+	c|blog|Blog_Post|edit|(if)|creator						- may edit their own blog posts
//+	p|blog|Blog_Post|create									- may create blog posts
//+ p|blog|Blog_Post|comment-create							- may comment on blog posts
//+	c|blog|Blog_Post|image-create|(if)|creator				- may add images to their own blog posts
//+	c|projects|Projects_Project|images-delete|(if)|member	- may delete images if member of project
//+
//+ TODO: consider adding third permission type 'e', evalutaing a member, say to allow an individual
//+	user to edit an individual document (when)|UID=1234, or to allow a role permissions on a
//+ subset of objects - say members of the 'sales' role can edit wiki pages in category 'marketing',
//+	but not in category 'accounts', or to give 'moderators' the ability to delete comments from
//+	unregistered users without review.
//+
//+	TODO: consider a better way for admins to decide on profile fields
//+	TODO: make profile expansion explicit, current method is wasteful, parses XML that is 
//+	almost never used.

require_once($installPath . 'modules/users/models/login.mod.php');
require_once($installPath . 'modules/users/models/friendship.mod.php');
require_once($installPath . 'modules/schools/models/school.mod.php');	// move to this module?

class Users_User {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded record [array]
	var $dbSchema;			//_	database structure [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool] 

	var $UID;				// UID
	var $role;				// title
	var $school;			// varchar(30)
	var $grade;				// varchar(30)
	var $firstname;			// varchar(100)
	var $surname;			// varchar(100)
	var $username;			// varchar(50)
	var $password;			// varchar(50)
	var $lang;				// varchar(30)
	var $profile;			// stored as XML in a text field [array]
	var $permissions;		// actions this user is authorised to take [array]
	var $lastOnline;		// datetime
	var $createdOn;			// datetime
	var $createdBy;			// ref:users-user
	var $editedOn;			// datetime
	var $editedBy;			// ref:users-user
	var $alias;				// alias

	//TODO: make this an admin-editable setting
	var $profileFields = 'interests|hometown|music|goals|books|also|birthyear|tel|email';
	
	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - recordAlias or UID of a user [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a User object [string]

	function Users_User($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema

		if ('public' == $raUID) { $raUID = ''; }			//	no user object
		if ('' != $raUID) { $this->load($raUID); }			// 	try load an object from the database

		if (false == $this->loaded) {						// check if we did
			//note: we can't use $db->makeBlank yet, it requires global user
			$this->UID = 'public';
			$this->role = 'public';							// set default role
			$this->firstname = 'public';
			$this->firstname = 'user';
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a User object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID = '') {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) {
		if (false == $ary) { return false; }
		$this->data = $ary; 
		$this->UID = $ary['UID'];
		$this->role = $ary['role'];
		$this->school = $ary['school'];
		$this->grade = $ary['grade'];
		$this->firstname = $ary['firstname'];
		$this->surname = $ary['surname'];
		$this->username = $ary['username'];
		$this->password = $ary['password'];
		$this->lang = $ary['lang'];
		$this->profile = $this->expandProfile($ary['profile']);
		$this->permissions = $this->expandPermissions($ary['permissions']);
		$this->lastOnline = $ary['lastOnline'];
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
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('users', 'Users_User', $this->UID, $this->role);
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that an object is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		global $db;
		$report = '';

		if (strlen($this->UID) < 5) 
			{ $report .= "UID not present.\n"; }

		if (strlen($this->firstname) < 1) 
			{ $report .= "Please enter the users first name.\n"; }

		if (strlen($this->surname) < 1) 
			{ $report .= "Please enter the users surname.\n"; }

		if (strlen($this->username) < 4) 
			{ $report .= "Please enter a username (4 characters or more).\n"; }

		if (strlen($this->password) < 6) 
			{ $report .= "Please enter a password (6 characters or more).\n"; }

		if (false == $db->objectExists('Schools_School', $this->school)) 
			{ $report .= "Please select a school for this user.\n"; }

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'users';
		$dbSchema['model'] = 'Users_User';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'role' => 'VARCHAR(33)',
			'school' => 'VARCHAR(33)',
			'grade' => 'VARCHAR(30)',
			'firstname' => 'VARCHAR(100)',
			'surname' => 'VARCHAR(100)',
			'username' => 'VARCHAR(50)',
			'password' => 'VARCHAR(50)',
			'lang' => 'VARCHAR(30)',
			'profile' => 'TEXT',
			'permissions' => 'TEXT',
			'lastOnline' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'username' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array('UID');
		//TODO: add more nodiff fields

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'role' => $this->role,
			'school' => $this->school,
			'grade' => $this->grade,
			'firstname' => $this->firstname,
			'surname' => $this->surname,
			'username' => $this->username,
			'password' => $this->password,
			'lang' => $this->lang,
			'profile' => $this->collapseProfile(),
			'permissions' => $this->collapsePermissions(),
			'lastOnline' => $this->lastOnline,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user, $theme, $utils;		// note that current user and $this may not be the same
		$ary = $this->toArray();

		$ary['fullName'] = trim($ary['firstname'] . ' ' . $ary['surname']);

		$ary['editUrl'] = '';			$ary['editLink'] = '';
		$ary['editProfileUrl'] = '';	$ary['editProfileLink'] = '';
		$ary['viewUrl'] = '';			$ary['viewLink'] = '';
		$ary['delUrl'] = '';			$ary['delLink'] = '';
		$ary['newUrl'] = '';			$ary['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		//if (false == $user->authHas('users', 'Users_User', 'show', '') == false) 
		//	{ echo "no permission to view users.<br/>"; }

		if ( (true == $user->authHas('users', 'Users_User', 'view', $this->UID)) 
			OR ($user->UID == $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%users/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[profile]</a>"; 
		}

		if ( (true == $user->authHas('user', 'Users_User', 'edit', $this->UID)) 
			OR ($user->UID == $this->UID) ) {
			$ary['editUrl'] =  '%%serverPath%%users/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['editProfileUrl'] =  '%%serverPath%%users/editprofile/' . $this->alias;
			$ary['editProfileLink'] = "<a href='" . $ary['editProfileUrl'] . "'>[edit]</a>"; 
		}

		// to ponder - should users be able to delete their profile?
		if (true == $user->authHas('users', 'Users_User', 'delete', $this->UID)) { 
			$ary['delUrl'] =  '%%serverPath%%users/confirmdelete/UID_' . $this->UID . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (true == $user->authHas('users', 'Users_User', 'new', $this->UID)) { 
			$ary['newUrl'] = "%%serverPath%%users/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[new user]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	chatlink- if logged in to awarenet (not necessarily on this peer)
		//------------------------------------------------------------------------------------------
		$block = '[[:users::chatlink::userUID='. $ary['UID'] .':]]';
		$ary['chatLink'] = $theme->expandBlocks($block, '');

		//------------------------------------------------------------------------------------------
		//	add profile fields
		//------------------------------------------------------------------------------------------

		foreach($this->profile as $field => $value) { 
			$value = str_replace("\n", "<br/>\n", $value);
			if (trim($value) == '') { $ary['profile_' . $field] = '';  }
			else { $ary['profile_' . $field] = "<p><b>" . $field . ":</b> " .  $value . "</p>\n"; }
		}

		//------------------------------------------------------------------------------------------
		//	look up school info
		//------------------------------------------------------------------------------------------
		$mySchool = new Schools_School($ary['school']);

		$ary['schoolName'] = $mySchool->name;
		$ary['schoolCountry'] = $mySchool->country;
		$ary['schoolRecordAlias'] = $mySchool->alias;
		$ary['schoolUrl'] = '%%serverPath%%schools/' . $mySchool->alias;
		$ary['schoolLink'] = "<a href='" . $ary['schoolUrl'] . "'>" . $mySchool->name . "</a>";

		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current user
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
	//	AUTHENTICATION AND PERMISSIONS
	//==============================================================================================


	//----------------------------------------------------------------------------------------------
	//.	check if the current user has a permission on a specified object
	//----------------------------------------------------------------------------------------------
	//arg: module - name of a kapenta module [string]
	//arg: model - object class [string]
	//arg: permission - name of permission sought [string]
	//opt: UID - specific object on which permission is sought (if any) [string]
	//returns: true if the user is authorized, otherwise false [bool]

	function authHas($module, $model, $permission, $UID = '') {
		global $kapenta, $role, $page;
		if ('admin' == $this->role) { return true; }			// admins have all permissions
		if (false == $role->loaded) { return false; }			// no such role

		$page->logDebug('auth', "Checking: $module - $model - $permission - $UID <br/>\n");

		//------------------------------------------------------------------------------------------
		//	check role permisisons first
		//------------------------------------------------------------------------------------------
		if (true == array_key_exists($module, $role->permissions)) {
			foreach($role->permissions[$module] as $p) {
				if (($model == $p['model']) && ($permission == $p['permission'])) {
					if ('p' == $p['type']) { 
						//--------------------------------------------------------------------------
						// user has blanket permission to perform this action
						//--------------------------------------------------------------------------
						return true;
					}

					if ('c' == $p['type']) {
						//--------------------------------------------------------------------------
						// user has permission to perform this action is a condition is met
						//--------------------------------------------------------------------------
						$met = $kapenta->relationshipExists(
							$module,	
							$model,
							$UID,
							$p['condition'],
							$this->UID
						);

						if (true == $met) { return true; }
					}
				}
			}
		} 

		$page->logDebug('auth', "Role does not support permission.<br/>\n");

		//------------------------------------------------------------------------------------------
		//	if role does not authorize action, try user-specific permissions
		//------------------------------------------------------------------------------------------
		if (false == is_array($this->permissions)) { $this->permissions = array(); }	// TODO: fix
		if (true == array_key_exists($module, $this->permissions)) {
			foreach($this->permissions[$module] as $p) {
				if (($model == $p['model']) && ($permission == $p['permission'])) {
					if ('p' == $p['type']) { 
						//--------------------------------------------------------------------------
						// user has blanket permission to perform this action
						//--------------------------------------------------------------------------
						return true;
					}

					if ('c' == $p['type']) {
						//--------------------------------------------------------------------------
						// user has permission to perform this action is a condition is met
						//--------------------------------------------------------------------------
						$met = $kapenta->relationshipExists(
							$module,	
							$model,
							$UID,
							$p['condition'],
							$this->UID
						);

						if (true == $met) { return true; }
					}
				}
			}
		} 

		//------------------------------------------------------------------------------------------
		//	at this point to policy has been shown to authorize this user to permform this action
		//------------------------------------------------------------------------------------------
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	read [permissions] into array for authentication
	//----------------------------------------------------------------------------------------------
	//arg: serialized - permissions set as raw string [string]
	//returns: permissions set as an array [array]

	function expandPermissions($data) {
		$permissions = array();
		$lines = explode("\n", $data);
		foreach($lines as $line) {
		  if (strpos($line, '|') != false) {
			$parts = explode("|", $line);
			$perm = array();

			$perm['type'] = $parts[0];
			$perm['module'] = $parts[1];
			$perm['model'] = $parts[2];
			$perm['permission'] = $parts[3];

			if ('c' == $perm['type']) {	$perm['condition'] = $parts[5];	}
	
			if (false == array_key_exists($perm['module'], $permissions)) 
				{ $permissions[$perm['module']] = array(); }

			$permissions[$perm['module']][] = $perm;
		  }
		}

		return $permissions;
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse permissions array back to an XML string
	//----------------------------------------------------------------------------------------------
	//returns: permissions serialized as a string [string]

	function collapsePermissions() {
		$txt = '';

		if (false == is_array($this->permissions))
			{ $this->permissions = $this->expandPermissions($this->permissions); }

		foreach($this->permissions as $moduleName => $modPerms) {
			foreach($modPerms as $p) {
				$txt .= $p['type'] . '|' . $p['module'] . '|' . $p['model'] . '|' . $p['permission'];
				if ('c' == $p['type']) { $txt .= "|(if)|" . $p['condition']; }
				$txt .= "\n";
			}
		}

		return $txt;
	}

	//==============================================================================================
	//	USER PROFILES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	create blank profile - for new records
	//----------------------------------------------------------------------------------------------

	function initProfile() {
		$pF = explode('|', $this->profileFields);
		$this->profile = array();
		foreach($pF as $field) {
			$this->profile[$field] = '';
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	expand profile  //TODO: fix this, security checks
	//----------------------------------------------------------------------------------------------
	//arg: xml - raw XML containing profile [string]
	//returns: hash table of profile fields and values [array]

	function expandProfile($xml) {
		global $session;
		// initialize all fields blank
		$profile = array();
		$pF = explode('|', $this->profileFields);
		foreach($pF as $profileField) { $profile[$profileField] = ''; }

		$doc = new KXMLDocument($xml);	//%	parse XML [class:KXMLDocument]
		$keys = $doc->getChildren2d();	//%	all children of root node [array]
		if (false == $keys) { return $profile; }

		foreach($keys as $field => $value) 
			{ if (true == array_key_exists($field, $profile)) { $profile[$field] = $value;  } }

		return $profile;
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse profile
	//----------------------------------------------------------------------------------------------

	function collapseProfile() {
		$xml = '';

		if (false == is_array($this->profile)) 
			{ $this->profile = $this->expandProfile($this->profile); }

		foreach($this->profile as $field => $value) 
			{ $xml .= "<" . $field . ">" . strip_tags($value) . "</" . $field . ">\n"; }

		$xml = "<profile>\n$xml</profile>\n";
		return $xml;
	}

	//==============================================================================================
	//	TODO: remove this
	//==============================================================================================
	//----------------------------------------------------------------------------------------------
	//.	find users in the same grade as this user  //TODO: $db->loadRange
	//----------------------------------------------------------------------------------------------
	//returns: array of UID => row for loadArray [array]

	function sameGrade() {
		global $db;
		$conditions = array();
		$conditions[] = "school='" . $db->addMarkup($this->school) . "'";
		$conditions[] = "grade='" . $db->addMarkup($this->grade) . "'";
		$range = $db->loadRange('Users_User', '*', $conditions, 'surname, firstname');
		return $range;
	}

	//==============================================================================================
	//	USER RELATIONSHIPS
	//==============================================================================================
	//----------------------------------------------------------------------------------------------
	//.	get the current users friends
	//----------------------------------------------------------------------------------------------
	//returns: array of friendshipUID => friendship for loadArray [array]
	//, TODO: handle this differently, perhaps with a block
	//, TODO: see if this can be removed entirely

	function getFriends() {
		global $db;
		$conditions = array();
		$conditions[] = "userUID='" . $db->addMarkup($this->UID) . "'";
		$conditions[] = "status='confirmed'";
		$range = $db->loadRange('Users_Friendship', '*', $conditions, 'surname, firstname');
		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	get inbound friend requests (that have been made to this user) // TODO: make this a block?
	//----------------------------------------------------------------------------------------------

	function getFriendRequestsIn() {
		global $db;
		$conditions = array();
		$conditions[] = "friendUID='" . $db->addMarkup($this->UID) . "'";
		$conditions[] = "status='unconfirmed'";
		$range = $db->loadRange('Users_Friendship', '*', $conditions, 'surname, firstname');
		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	get outbound friend requests (that this user has made) // TODO: make this a block?
	//----------------------------------------------------------------------------------------------
	
	function getFriendRequestsOut() {
		global $db;
		$conditions = array();
		$conditions[] = "userUID='" . $db->addMarkup($this->UID) . "'";
		$conditions[] = "status='unconfirmed'";
		$range = $db->loadRange('Users_Friendship', '*', $conditions, 'surname, firstname');
		return $range;
	}

	//==============================================================================================
	//	TOSTRING METHODS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	get the current user's name
	//----------------------------------------------------------------------------------------------
	//returns: firstname and surname [string]

	function getName() { return trim($this->firstname . ' ' . $this->surname);	}

	//----------------------------------------------------------------------------------------------
	//.	get the current user's profile URL
	//----------------------------------------------------------------------------------------------
	//returns: absolute URL [string]

	function getUrl() { return "%%serverPath%%users/profile/". $this->alias; }

	//----------------------------------------------------------------------------------------------
	//.	make a link to the current user's profile
	//----------------------------------------------------------------------------------------------
	//returns: HTML anchor tag [string]

	function getNameLink() { return "<a href='". $this->getUrl() ."'>". $this->getName() ."</a>"; }

}

?>
