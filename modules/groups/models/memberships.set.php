<?php

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of memberships of a group
//--------------------------------------------------------------------------------------------------

class Groups_Memberships {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $groupUID = '';					//_	UID of a Groups_Group obejct [string]
	var $members;						//_	range from groups_membership table [array]
	var $loaded = false;				//_	set to true when membership set is loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Groups_Memberships($groupUID = '') {
		$this->members = array();
		$this->groupUID = $groupUID;
	}

	//----------------------------------------------------------------------------------------------
	//.	load the set of members
	//----------------------------------------------------------------------------------------------
	//;	Expects that $this->groupUID is set before calling [string]
	//returns: true on success, false on failure [bool]

	function load() {
		global $kapenta;

		if ('' == $this->groupUID) { return false; }
		if (false == $kapenta->db->objectExists('groups_group', $this->groupUID)) { return false; }

		$conditions = array("groupUID='" . $kapenta->db->addMarkup($this->groupUID) . "'");
		$this->members = $kapenta->db->loadRange('groups_membership', '*', $conditions);
		$this->loaded = true;
		$this->deleteDuplicates();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the set of serialized group memberships
	//----------------------------------------------------------------------------------------------
	//returns: array of serialized Groups_Membership objects [array]

	function get() {
		if (false == $this->loaded) { $this->load(); }
		return $this->members;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a user belongs to a group
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object to search for [string]
	//returns: UID of Groups_Membership object if one is found, empty string if not [string]

	function has($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		foreach($this->members as $membership) {
			if ($userUID == $membership['userUID']) { return $membership['UID']; }
		}

		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	add a new member to the group
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//arg: position - position or role within the group [string]
	//arg: admin - whether the member is an admin of this group (yes|no) [string]
	//returns: true on success, false on failure [bool]

	function add($userUID, $position, $admin) {
		global $kapenta;
		global $kapenta;
		global $session;

		if (false == $this->loaded) { $this->load(); }		//	effectively checks $this->groupUID
		if (false == $this->loaded) { return false; }		//	and database connection

		$report = '';										//%	from verifying and saving group

		$extant = $this->has($userUID);

		if ('' == $extant) {
			//--------------------------------------------------------------------------------------
			//	brand new membership
			//--------------------------------------------------------------------------------------
			$model = new Groups_Membership();				//	(re)create with position and admin
			$model->UID = $kapenta->createUID();			//	privileges as called.
			$model->userUID = $userUID;
			$model->groupUID = $this->groupUID;
			$model->position = $position;
			$model->admin = $admin;
			$model->joined = $kapenta->db->datetime();
			$report = $model->save();

		} else {
			//--------------------------------------------------------------------------------------
			//	update existing membership
			//--------------------------------------------------------------------------------------
			$model = new Groups_Membership($extant);

			if (false == $model->loaded) { $report .= "Could not load membership to update it."; }
			else {
				$model->position = $position;
				$model->admin = $admin;
				$report = $model->save();
			}

		}

		if ('' == $report) { 							//	any html report indicates db error
			$this->load();								//	update members list

			//TOSO: raise event to cause an update to schools index for this group
			//$this->updateSchoolsIndex();				//	update associations with schools
			return true;
		}
		
		$kapenta->session->msg("Could not add member:<br/>" . $report, 'bad');
		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	remove a member from this group
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true on success, false on failure [bool]

	function remove($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		$UID = $this->has($userUID);

		if ('' == $UID) { return false; }

		$model = new Groups_Membership($UID);
		if (false == $model->loaded) { return false; }	//	database error

		$result = $model->delete();

		if (true == $result) {
			$this->load();								//	update members list

			//TOSO: raise an event which will update schools idnex for this group
			//$this->updateSchoolsIndex();				//	update associations with schools
		}

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete any duplicate memberships
	//----------------------------------------------------------------------------------------------

	function deleteDuplicates() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		$members = array();

		foreach($this->members as $membership) {
			if (false == in_array($membership['userUID'], $members)) {
				$members[] = $membership['userUID'];
			} else {
				$model = new Groups_Membership($membership['UID']);
				$model->delete();
			}
		}

		return true;
	}
}

?>
