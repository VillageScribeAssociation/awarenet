<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object for maintaing the set of members belonging to a project
//--------------------------------------------------------------------------------------------------

class Projects_Memberships {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;
	var $loaded = false;				//%	set tot rue when members loaded [bool]
	var $projectUID = '';				//%	ref:Projects_Project [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: projectUID - UID of a Projects_Project object [string]

	function Projects_Memberships($projectUID = '') {
		$members = array();
		$this->projectUID = $projectUID;
		if ('' != $this->projectUID) {
			//$this->load();			// uncomment to disable lazy initialization
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load all members associated with this project
	//----------------------------------------------------------------------------------------------
	//;	note projectUID should be set before this is called
	//returns: true on succes, false on failure

	function load() {
		global $db;
		$this->members = array();
		if ('' == $this->projectUID) { return false; }
		$conditions = array("projectUID='" . $db->addMarkup($this->projectUID) . "'");
		$range = $db->loadRange('projects_membership', '*', $conditions);
		$this->members = $range;
		$this->loaded = true;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	add a member to the project, or change the role of an existing member
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//arg: role - role in the project (admin|member) [string]
	//returns: true on success, false on failure [bool]

	function add($userUID, $role) {
		global $db;
		if (false == $db->objectExists('users_user', $userUID)) { return false; }
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		//------------------------------------------------------------------------------------------
		//	check user's role in the project if an existing member
		//------------------------------------------------------------------------------------------
		foreach($this->members as $item) {
			if ($userUID == $item['userUID']) {
				$model = new Projects_Membership();
				$model->loadArray($item);
				$model->role = $role;
				$model->joined = $db->datetime();
				$check = $model->save();
				$this->load();							// reload memberships
				if ('' != $check) { return false; }
				return true;
			}
		}

		//------------------------------------------------------------------------------------------	
		//	create membership if one does not already exist
		//------------------------------------------------------------------------------------------	
		$model = new Projects_Membership();
		$model->projectUID = $this->projectUID;
		$model->userUID = $userUID;
		$model->role = $role;
		$model->joined = $db->datetime();
		$model->save();
		$check = $model->save();
		$this->load();									// reload memberships
		if ('' != $check) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a member from the project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user to remove from the project [string]
	//returns: true on success, false on failure [bool]

	function remove($userUID) {
		global $session;
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		if (false == $this->hasMember($userUID)) {
			$session->msg('User ' . $userUID . ' is not a member of this project.', 'bad');
			return false;
		}
		foreach($this->members as $membership) {
			if ($userUID == $membership['userUID']) {
				$model = new Projects_Membership();
				$model->loadArray($membership);
				$check = $model->delete();
				$this->load();							// reload memberships
				if (true == $check) { return true; }
			}
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if a given user (UID) is a member of the project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: membership record if a member, false if not [array][bool]

	function hasMember($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		foreach($this->members as $item) {
			if ($userUID == $item['userUID']) { return true; }
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if a given user (UID) is an admin of the project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: membership record if a member, false if not [array][bool]

	function hasAdmin($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		foreach($this->members as $item) {
			if (($userUID == $item['userUID']) && ('admin' == $item['role'])) { return true; }
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if a given user (UID) has asked to join this project
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: membership record if a member, false if not [array][bool]

	function hasAsked($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		foreach($this->members as $item) {
			if (($userUID == $item['userUID']) && ('asked' == $item['role'])) { return true; }
		}
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	get all members of a project (exc. prospective), returns array of [userUID] => [role]
	//----------------------------------------------------------------------------------------------
	//opt: prospective - is true them embers who have asked are included [bool]
	//returns: array of memberships (userUID => role) [array]

	function getMembers() {
		$map = array();													//% return value [array]

		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		foreach($this->members as $item) {
			if ('asked' != $item['role']) { $map[$item['userUID']] = $item['role']; }
		}

		return $map;
	}

	//----------------------------------------------------------------------------------------------
	//.	get prospective members of a project, returns array of [userUID] => [role] 
	//----------------------------------------------------------------------------------------------
	//returns: array of membership applications (userUID => role) [array]

	function getProspectiveMembers() {	//TODO: remove this entirely
		$map = array();													//% return value [array]
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		foreach($this->members as $item) {
			if ('asked' == $item['role']) { $map[$item['userUID']] = $item['role']; }
		}
		return $map;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the UID of a Projects_Membership object given userUID
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User obejct [string]
	//returns: UID of a Projects_Membership object, or empty string if not found [string]

	function getUID($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		foreach($this->members as $item) {
			if ($userUID == $item['userUID']) { return $item['UID']; }
		}
		return '';
	}

}

?>
