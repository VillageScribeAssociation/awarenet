<?php

	require_once($kapenta->installPath . 'modules/groups/models/schoolindex.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to manage the set of schools a group is associated with
//--------------------------------------------------------------------------------------------------
//+	Groups are associated with schools is one or more members belongs to both

class Groups_Schools {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $groupUID = '';					//_	UID of a Groups_Group object [string]
	var $members;						//_	range from groups_schoolindex table [array]
	var $loaded = false;				//_	the members array is lazily initialized [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Groups_Schools($groupUID = '') {
		$this->members = array();
		$this->groupUID = $groupUID;
	}

	//----------------------------------------------------------------------------------------------
	//.	load the set of schools a group belongs to
	//----------------------------------------------------------------------------------------------
	//	Expects that $this->groupUID is set before calling [string]
	//returns: true on success, false on failure [bool]

	function load() {
		global $kapenta;

		if ('' == $this->groupUID) { return false; }
		if (false == $kapenta->db->objectExists('groups_group', $this->groupUID)) { return false; }

		$conditions = array("groupUID='" . $kapenta->db->addMarkup($this->groupUID) . "'");
		$this->members = $kapenta->db->loadRange('groups_schoolindex', '*', $conditions);
		$this->loaded = true;
		return true;

	}

	//----------------------------------------------------------------------------------------------
	//.	discover if this group is associated with a school
	//----------------------------------------------------------------------------------------------
	//arg: schoolUID - UID of a Schools_School object to search for [string]
	//returns: UID of Groups_SchoolIndex object if found, empty string if not [string]

	function has($schoolUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		foreach($this->members as $schoolidx) {
			if ($schoolUID == $schoolidx['schoolUID']) { return $schoolidx['UID']; }
		}

		return '';		
	}

	//----------------------------------------------------------------------------------------------
	//.	get the number of group members at the given school
	//----------------------------------------------------------------------------------------------
	//arg: schoolUID - UID of a Schools_School object [string]
	//returns: number of members recorded in index, or -1 on failure [int]

	function getMemberCount($schoolUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return -1; }

		foreach($this->members as $idx) {
			if ($idx['schoolUID'] == $schoolUID) { return (int)$idx['memberCount']; }
		}
		
		return -1;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a school assciation
	//----------------------------------------------------------------------------------------------
	//arg: schoolUID - UID of a Schools_School object [string]
	//arg: memberCount - number of users in both school and group [int]
	//returns: true on success, false on failure [bool]

	function add($schoolUID, $memberCount) {
		global $kapenta;
		global $kapenta;
		global $session;

		if (false == $this->loaded) { $this->load(); }	//	effectively checks $this->groupUID
		if (false == $this->loaded) { return false; }	//	and database connection

		$report = '';									//%	from verifying and saving group [string]
		$UID = $this->has($schoolUID);					//%	UID of existing index [string]

		if ('' == $UID) {
			//--------------------------------------------------------------------------------------
			//	brand new association
			//--------------------------------------------------------------------------------------
			$model = new Groups_SchoolIndex();				//	(re)create with position and admin
			$model->UID = $kapenta->createUID();			//	privileges as called.
			$model->schoolUID = $schoolUID;
			$model->groupUID = $this->groupUID;
			$model->memberCount = $memberCount;
			$report = $model->save();

		} else {
			//--------------------------------------------------------------------------------------
			//	check if this actually needs to be done
			//--------------------------------------------------------------------------------------
			foreach($this->members as $idx) {
				if (($idx['schoolUID'] == $schoolUID) && ($idx['memberCount'] == $memberCount)) {
					return true;		//	already done
				}
			}

			//--------------------------------------------------------------------------------------
			//	member count has changed, update existing index
			//--------------------------------------------------------------------------------------
			$model = new Groups_SchoolIndex($UID);

			if (false == $model->loaded) { $report .= "Could not load school index to update it."; }
			else {
				$model->memberCount = $memberCount;
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
	//.	remove a school association from this group
	//----------------------------------------------------------------------------------------------
	//arg: schoolUID - UID of a Schools_School object [string]
	//returns: true on success, false on failure [bool]

	function remove($schoolUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		$UID = $this->has($schoolUID);

		if ('' == $UID) { return false; }

		$model = new Groups_SchoolIndex($UID);
		if (false == $model->loaded) { return false; }	//	database error

		$result = $model->delete();

		if (true == $result) { $this->load(); }			//	refresh index on this object

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	generate the set of schools a group should belong to based on membership
	//----------------------------------------------------------------------------------------------
	//arg: memberships - range of serialized Groups_Membership objects [string]
	//returns: array of schoolUID => memberCount [array]

	function makeSchoolsList($memberships) {
		global $theme;

		$schools = array();
		foreach($memberships as $membership) {
			$block = '[[:users::schooluid::userUID=' . $membership['userUID'] . ':]]';
			$schoolUID = $theme->expandBlocks($block);
			if ('' != $schoolUID) {
				if (false == array_key_exists($schoolUID, $schools)) {
					$schools[$schoolUID] = 1;
				} else {
					$schools[$schoolUID] += 1;
				}
			}
		}

		return $schools;
	}

	//----------------------------------------------------------------------------------------------
	//.	updates list of schools at which this group has members
	//----------------------------------------------------------------------------------------------	
	//arg: memberships - range of serialized Groups_Membership objects [string]
	//returns: true if anything was updated [bool]

	function updateSchoolsIndex($memberships) {
		global $theme;
		global $kapenta;
		global $session;

		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		$msg = '';												//%	admin / debug report [string]
		$updated = true;										//%	return value [bool]
		$schools = array();										//%	for counting duplicates [array]
		$canonical = $this->makeSchoolsList($memberships);		//%	assumed correct [array]

		//------------------------------------------------------------------------------------------
		//	debug information
		//------------------------------------------------------------------------------------------

		$msg = "updating school index of group " . $this->groupUID . "<br/>\n";

		foreach($canonical as $schoolUID => $memberCount) {
			$msg .= ''
			 . "Canonical: $schoolUID [[:schools::name::UID=$schoolUID:]] ($memberCount)<br/>\n";
		}

		foreach($this->members as $idx) {
			$msg .= ''
			 . "Extant: " . $idx['schoolUID']
			 . " [[:schools::name::UID=" . $idx['schoolUID'] . ":]] "
			 . "(" . $idx['memberCount'] . ")<br/>\n";
		}

		//------------------------------------------------------------------------------------------
		//	check for and remove duplicates
		//------------------------------------------------------------------------------------------
		foreach($this->members as $idx) {
			if (true == in_array($idx['schoolUID'], $schools)) {
				$msg .= "Removing duplicate for school " . $idx['schoolUID'] . "<br/>";
				$this->remove($idx['schoolUID']);
				$this->load();

			} else { $schools[] = $idx['schoolUID']; }
		}		

		//------------------------------------------------------------------------------------------
		//	for each extant association, check that it matches canonical set
		//------------------------------------------------------------------------------------------

		foreach($this->members as $idx) {
			$schoolUID = $idx['schoolUID'];

			if (false == array_key_exists($schoolUID, $canonical)) {
				//----------------------------------------------------------------------------------
				//	not in the list, remove association
				//----------------------------------------------------------------------------------
				$msg .= "Removing assciation for $schoolUID<br/>\n";
				$this->remove($schoolUID);
				$this->load();

			} else {
				//----------------------------------------------------------------------------------
				//	in the list, check member count is correct
				//----------------------------------------------------------------------------------
				if ($this->getMemberCount($schoolUID) != $canonical[$schoolUID]) {
					$msg .= "Updating membership count for $schoolUID ...<br/>";
					$this->add($schoolUID, $canonical[$schoolUID]);
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	for each canonical association, check that it exists
		//------------------------------------------------------------------------------------------

		foreach($canonical as $schoolUID => $memberCount) {
			if ('' == $this->has($schoolUID)) {
				$msg .= "Adding school $schoolUID to group.<br/>\n";
				$this->add($schoolUID, $memberCount);
			}
		}

		$kapenta->session->msgAdmin($msg);

		$this->load();									//	update list of schools
		return $updated;
	}

}

?>
