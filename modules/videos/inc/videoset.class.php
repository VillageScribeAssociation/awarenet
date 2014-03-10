<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of all videos owned by some object
//--------------------------------------------------------------------------------------------------

class Videos_Videoset {

	//----------------------------------------------------------------------------------------------
	//	memebr variables
	//----------------------------------------------------------------------------------------------

	var $videos;			//_	range of serialized Videos_Video objects [array]
	var $loaded = false;	//_ is set to true when a range has been loaded [bool]

	var $refModule;			//_ kapenta module [string]
	var $refModel;			//_	type of object [string]
	var $refUID;			//_	UID of object which owns videos [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor (loads all videos belonging to some object)
	//----------------------------------------------------------------------------------------------
	//arg: refModule - a kapenta module [string]
	//arg: refModel - type of object which may own videos [string]
	//arg: refUID - UID of object which may own videos [string]

	function Videos_VideoSet($refModule, $refModel, $refUID) {
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
		global $kapenta;

		$this->videos = array();
		$conditions = array();
		$conditions[] = "refModule='" . $kapenta->db->addMarkup($this->refModule) . "'";
		$conditions[] = "refModel='" . $kapenta->db->addMarkup($this->refModel) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($this->refUID) . "'";

		$range = $kapenta->db->loadRange('videos_video', '*', $conditions, 'weight');
		if (false == $range) { return false; }
		
		foreach($range as $row) { $this->videos[] = $row; }
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
		foreach($this->videos as $row) {
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
	//.	get the index of a video in $this->videos give its UID
	//----------------------------------------------------------------------------------------------
	//returns: index on success, -1 on failure [int]

	function getIndex($UID) {
		if (false == $this->loaded) { return (-1); }
		foreach($this->videos as $idx => $row) { if ($row['UID'] == $UID) { return $idx; } }
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
		if (true == array_key_exists($idx, $this->videos)) {
			$model->load($this->videos[$idx]['UID']);
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
		if (true == array_key_exists($idx, $this->videos)) {
			$model->load($this->videos[$idx]['UID']);
			if (true == $model->loaded) { 
				$model->weight += 1;
				$model->save();
			}
		}
		$this->load();
		$this->checkWeights();
		return true;
	}

}

?>
