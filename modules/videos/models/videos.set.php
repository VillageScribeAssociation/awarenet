<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of all videos owned by some object
//--------------------------------------------------------------------------------------------------

class Videos_Videos {

	//----------------------------------------------------------------------------------------------
	//	memebr variables
	//----------------------------------------------------------------------------------------------

	var $members;			//_	range of serialized Videos_Video objects [array]
	var $loaded = false;	//_ is set to true when a range has been loaded [bool]

	var $refModule;			//_ kapenta module [string]
	var $refModel;			//_	type of object [string]
	var $refUID;			//_	UID of object which owns videos [string]
	var $count = 0;			//_	number of videos in this set [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor (loads all videos belonging to some object)
	//----------------------------------------------------------------------------------------------
	//arg: refModule - a kapenta module [string]
	//arg: refModel - type of object which owns videos [string]
	//arg: refUID - UID of object which may own videos

	function Videos_Videos($refModule, $refModel, $refUID) {
		$this->refModule = $refModule;
		$this->refModel = $refModel;
		$this->refUID = $refUID;
		$this->load();
		$this->checkWeights();
	}

	//----------------------------------------------------------------------------------------------
	//.	load the set of videos
	//----------------------------------------------------------------------------------------------
	//returns: true of success, false on failure [bool]

	function load() {
		global $db;

		$this->members = array();
		$conditions = array();
		$conditions[] = "refModule='" . $db->addMarkup($this->refModule) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($this->refModel) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($this->refUID) . "'";

		$range = $db->loadRange('videos_video', '*', $conditions, 'weight');
		if (false == $range) { return false; }
		
		foreach($range as $row) { $this->members[] = $row; }
		$this->count = count($this->members);
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	ensure that weights are contiguous
	//----------------------------------------------------------------------------------------------
	//returns: true on succes, false on failure [bool]

	function checkWeights() {
		if (false == $this->loaded) { return false; }
		$idx = 0;
		$dirty = false;
		foreach($this->members as $row) {
			if ($row['weight'] != $idx) {
				$model = new Videos_Video($row['UID']);
				$model->weight = $idx;
				$model->save();
				$dirty = true;
			}
			$idx++;
		}
		if (true == $dirty) { $this->load(); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the index of an video in $this->members give its UID
	//----------------------------------------------------------------------------------------------
	//returns: index on success, -1 on failure [int]

	function getIndex($UID) {
		if (false == $this->loaded) { return (-1); }
		foreach($this->members as $idx => $row) { if ($row['UID'] == $UID) { return $idx; } }
		return (-1);		
	}

	//----------------------------------------------------------------------------------------------
	//.	bump a video up in the list of weights
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an Videos_Video object [string]

	function incWeight($UID) {
		$this->checkWeights();
		$idx = $this->getIndex($UID);

		//------------------------------------------------------------------------------------------
		//	increase weight by 1
		//------------------------------------------------------------------------------------------

		$model = new Videos_Video($UID);
		if (false == $model->loaded) { return false; }
		$model->weight += 1;
		$model->save();

		//------------------------------------------------------------------------------------------
		//	decrease weight of next video by 1 (if any)
		//------------------------------------------------------------------------------------------
		$idx++;
		if (true == array_key_exists($idx, $this->members)) {
			$model->load($this->members[$idx]['UID']);
			if (true == $model->loaded) { 
				$model->weight -= 1;
				$model->save();
			}
		}
		$this->load();
		$this->checkWeights();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	bump a video down in the list of weights
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an Videos_Video object [string]

	function decWeight($UID) {
		$this->checkWeights();
		$idx = $this->getIndex($UID);

		//------------------------------------------------------------------------------------------
		//	decrease weight by 1
		//------------------------------------------------------------------------------------------

		$model = new Videos_Video($UID);
		if (false == $model->loaded) { return false; }
		if (0 == $model->weight) { return true; }			// nothing to do
		$model->weight -= 1;
		$model->save();

		//------------------------------------------------------------------------------------------
		//	increase weight of previous video by 1 (if any)
		//------------------------------------------------------------------------------------------
		$idx--;
		if (true == array_key_exists($idx, $this->members)) {
			$model->load($this->members[$idx]['UID']);
			if (true == $model->loaded) { 
				$model->weight += 1;
				$model->save();
			}
		}
		$this->load();
		$this->checkWeights();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	set an video as the default (weight 0)
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an Videos_Video object [string]
	//returns: true on succes, false on failure [bool]

	function setDefault($UID) {
		$found = false;						//%	return value [bool]
		$currWeight = 1;

		// check that this is not already the default
		foreach ($this->members as $objArray) {
			if (($UID == $objArray['UID']) && (0 == $objArray['weight'])) { return true; }
		}

		// set weight to 0
		foreach ($this->members as $objArray) {
			$model = new Videos_Video();
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
	//.	count the number of videos in this set
	//----------------------------------------------------------------------------------------------
	
	function count() {
		$count = count($this->members);
		return $count;
	}

}

?>
