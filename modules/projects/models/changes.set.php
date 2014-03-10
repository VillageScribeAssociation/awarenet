<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/change.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object for managing the set of changes which apply to a project or section
//--------------------------------------------------------------------------------------------------

class Projects_Changes {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;					//_	range of serialized Projects_Change objects [array]
	var $loaded = false;			//_	set to true when changes loaded [bool]

	var $projectUID = '';			//_	ref:Projects_Project [string]
	var $sectionUID = '';			//_	ref:Projects_Section [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: projectUID - UID of a Projects_Project object [string]
	//opt: sectionUID - UID of a Projects_Section object [string]

	function Projects_Changes($projectUID = '', $sectionUID = '*') {
		$this->members = array();
		$this->projectUID = $projectUID;
		$this->sectionUID = $sectionUID;
		if ('' != $projectUID) { $this->load($projectUID, $sectionUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load set of changes from the database
	//----------------------------------------------------------------------------------------------
	//;	project and section UID should be set before this is called
	//arg: projectUID - UID of a Projects_Project object [string]
	//opt: sectionUID - UID of a Projects_Section object [string]
	//returns: true on success, false on failure [bool]

	function load() {
		global $kapenta;

		if ('' == trim($this->projectUID)) { return false; }

		$changes = array();								//%	return value [array]
		$conditions = array();							//%	recordset filter [array]

		$conditions[] = "projectUID='" . $kapenta->db->addMarkup($this->projectUID) . "'";
		if (('*' != $this->sectionUID) && ('' != $this->sectionUID)) {
			$conditions[] = "sectionUID='" . $kapenta->db->addMarkup($this->sectionUID) . "'";
		}

		$range = $kapenta->db->loadRange('projects_change', '*', $conditions, 'createdOn ASC');
		foreach($range as $item) {
			$changes[$item['UID']] = $item;		
		}

		$this->members = $changes;
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	record a new change to a project
	//----------------------------------------------------------------------------------------------
	//arg: changed - type of change which happend [string]
	//arg: message - description of this event [string]
	//arg: value - new value of field [string]

	function add($changed, $message, $value) {
		$model = new Projects_Change();
		$model->projectUID = $this->projectUID;
		$model->sectionUID = $this->sectionUID;
		$model->changed = $changed;
		$model->message = $message;
		$model->value = $value;
		$report = $model->save();
		if ('' == $report) { $this->load(); }
		return $report;
	}

}


?>
