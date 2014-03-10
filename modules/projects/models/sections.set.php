<?

	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object for dealing with a set of project sections
//--------------------------------------------------------------------------------------------------

class Projects_Sections {
	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $members;			//_	range of serialized Projects_Section objects [array]
	var $loaded = false;	//_ is set to true when a range has been loaded [bool]

	var $projectUID;		//_ ref:Projects_Project [string]
	var $count = 0;			//_	number of sections in this set [int]
	var $maxWeight = 0;		//_	highest weighted item [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor (loads all sections in a project)
	//----------------------------------------------------------------------------------------------
	//opt: projectUID - UID of a Projects_Project object [string]

	function Projects_Sections($projectUID = '') {
		$this->projectUID = $projectUID;
		if ('' != $projectUID) {
			// $this->load();				// uncomment to disable lazy initialization
			//$this->checkWeights();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load the set of sections
	//----------------------------------------------------------------------------------------------
	//returns: true of success, false on failure [bool]

	function load() {
		global $kapenta;
		if ('' == $this->projectUID) { return false; }

		$this->members = array();
		$conditions = array();
		$conditions[] = "projectUID='" . $kapenta->db->addMarkup($this->projectUID) . "'";

		$range = $kapenta->db->loadRange('projects_section', '*', $conditions, 'weight ASC');
		if (false == $range) { return false; }
		
		foreach($range as $row) { 
			$this->members[$row['UID']] = $row;
			if ($row['weight'] > $this->maxWeight) { $this->maxWeight = $row['weight']; }
		}

		$this->loaded = true;
		$this->count = count($this->members);
		$this->checkWeights();

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	check that a section exists in this set
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Projectss_Section object [string]
	//returns: true if founf, false if not [string]

	function has($UID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		foreach($this->members as $section) { if ($section['UID'] == $UID) { return true; } }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	ensure that weights are contiguous
	//----------------------------------------------------------------------------------------------
	//returns: true on succes, false on failure [bool]

	function checkWeights() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		$idx = 0;
		$dirty = false;

		foreach($this->members as $row) {
			if ('yes' != $row['hidden']) {
				if ($row['weight'] != $idx) {
					$model = new Projects_Section($row['UID']);
					$model->weight = $idx;
					$model->save();
					$dirty = true;
				}
				$idx++;
			} else {
				// going to ignore hidden/'deleted' sections
			}
		}

		foreach($this->members as $row) { 
			if ($row['weight'] > $this->maxWeight) { $this->maxWeight = $row['weight']; }
		}

		if (true == $dirty) { $this->load(); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the index of an section in $this->members give its UID
	//----------------------------------------------------------------------------------------------
	//returns: index on success, -1 on failure [int]

	function getIndex($UID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return (-1); }
		foreach($this->members as $idx => $row) { if ($row['UID'] == $UID) { return $idx; } }
		return (-1);		
	}

	//----------------------------------------------------------------------------------------------
	//.	bump a section up in the list of weights
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an Projects_Section object [string]
	//returns: true on success, false on failure [string]

	function incWeight($UID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		$this->checkWeights();

		//------------------------------------------------------------------------------------------
		//	increase weight by 1
		//------------------------------------------------------------------------------------------
		$model = new Projects_Section($UID);
		if (false == $model->loaded) { return false; }
		if ($model->weight == $this->maxWeight) { return true; }	//	nothing to do
		$model->weight += 1;
		$model->save();

		//------------------------------------------------------------------------------------------
		//	decrease weight of next section by 1 (if any)
		//------------------------------------------------------------------------------------------
		$idx = $model->weight;
		foreach($this->members as $item) {
			if ($item['weight'] == $idx) {
				$model->loadArray($item);
				if (true == $model->loaded) { 
					$model->weight -= 1;
					$model->save();
				}
			}
		}

		$this->load();
		$this->checkWeights();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	bump a section down in the list of weights
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Sections_Section object [string]
	//returns: true on success, false on failure [string]

	function decWeight($UID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		$this->checkWeights();

		//------------------------------------------------------------------------------------------
		//	decrease weight by 1
		//------------------------------------------------------------------------------------------
		$model = new Projects_Section($UID);
		if (false == $model->loaded) { return false; }
		if (0 == $model->weight) { return true; }			// nothing to do
		$model->weight -= 1;
		$model->save();

		//------------------------------------------------------------------------------------------
		//	increase weight of previous section by 1 (if any)
		//------------------------------------------------------------------------------------------
		$idx = $model->weight;

		foreach($this->members as $item) {
			if ($item['weight'] == $idx) {
				$model->loadArray($item);
				if (true == $model->loaded) { 
					$model->weight += 1;
					$model->save();
				}
			}
		}

		$this->load();
		$this->checkWeights();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	set a section as the default (weight 0)
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Sections_Section object [string]
	//returns: true on succes, false on failure [bool]

	function setDefault($UID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		$found = false;						//%	return value [bool]
		$currWeight = 1;					//%	[int]

		// check that this is not already the default
		foreach ($this->members as $objArray) {
			if (($UID == $objArray['UID']) && (0 == $objArray['weight'])) { return true; }
		}

		// set weight to 0
		foreach ($this->members as $objArray) {
			$model = new Projects_Section();
			$model->loadArray($objArray);
			if ($UID == $model->UID) {
				$model->weight = 0;			//	this one is the default
				$found = true;
			} else {
				$model->weight = $currWeight;
				$currWeight++;
			}

			// save only if weight has changed
			if ($model->weight != $objArray['weight']) { $model->save(); }
		}

		$this->checkWeights();				//	in case of not found, this will reset weights
		return $found;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a wikicode table of section weights
	//----------------------------------------------------------------------------------------------

	function getWeightsTableWC() {
		$table = '';						//%	return value [string]

		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return $table; }

		foreach($this->members as $s) {
			$table .= "||" . $s['UID'] . "||" . $s['weight'] . "||" . $s['title'] . '||' . "\n";
		}
		return $table;
	}

	//----------------------------------------------------------------------------------------------
	//.	get weight of heaviest section
	//----------------------------------------------------------------------------------------------
	//returns: weight (positive integer) on success, -1 on failure [int]

	function getMaxWeight() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return -1; }
		return $this->maxWeight;
	}

	//----------------------------------------------------------------------------------------------
	//.	debugging report
	//----------------------------------------------------------------------------------------------
	//returns: HTML table or error message [string]

	function toHtml() {
		global $theme;
		$html = '';

		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return '(Could not load sections).'; }

		$table = array(array('UID', 'Title', 'Weight', 'Hidden'));
		foreach ($this->members as $row) {
			$table[] = array($row['UID'], $row['title'], $row['weight'], $row['hidden']);
		}

		$html = $theme->arrayToHtmlTable($table, true, true);
		return $html;
	}

}

?>
