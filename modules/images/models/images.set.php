<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of all images owned by some object
//--------------------------------------------------------------------------------------------------

class Images_Images {

	//----------------------------------------------------------------------------------------------
	//	memebr variables
	//----------------------------------------------------------------------------------------------

	var $members;			//_	range of serialized Images_Image objects [array]
	var $loaded = false;	//_ is set to true when a range has been loaded [bool]

	var $refModule;			//_ kapenta module [string]
	var $refModel;			//_	type of object [string]
	var $refUID;			//_	UID of object which owns images [string]
	var $count = 0;			//_	number of images in this set [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor (loads all images belonging to some object)
	//----------------------------------------------------------------------------------------------
	//arg: refModule - a kapenta module [string]
	//arg: refModel - type of object which owns images [string]
	//arg: refUID - UID of object which may own images

	function Images_Images($refModule, $refModel, $refUID) {
		$this->refModule = $refModule;
		$this->refModel = $refModel;
		$this->refUID = $refUID;
		$this->load();
		$this->checkWeights();
	}

	//----------------------------------------------------------------------------------------------
	//.	load the set of images
	//----------------------------------------------------------------------------------------------
	//returns: true of success, false on failure [bool]

	function load() {
		global $kapenta;

		$this->members = array();
		$conditions = array();
		$conditions[] = "refModule='" . $kapenta->db->addMarkup($this->refModule) . "'";
		$conditions[] = "refModel='" . $kapenta->db->addMarkup($this->refModel) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($this->refUID) . "'";

		$range = $kapenta->db->loadRange('images_image', '*', $conditions, 'weight');
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
				$model = new Images_Image($row['UID']);
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
	//.	get the index of an image in $this->members give its UID
	//----------------------------------------------------------------------------------------------
	//returns: index on success, -1 on failure [int]

	function getIndex($UID) {
		if (false == $this->loaded) { return (-1); }
		foreach($this->members as $idx => $row) { if ($row['UID'] == $UID) { return $idx; } }
		return (-1);		
	}

	//----------------------------------------------------------------------------------------------
	//.	bump an image up in the list of weights
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an Images_Image object [string]

	function incWeight($UID) {
		$this->checkWeights();
		$idx = $this->getIndex($UID);

		//------------------------------------------------------------------------------------------
		//	increase weight by 1
		//------------------------------------------------------------------------------------------

		$model = new Images_Image($UID);
		if (false == $model->loaded) { return false; }
		$model->weight += 1;
		$model->save();

		//------------------------------------------------------------------------------------------
		//	decrease weight of next image by 1 (if any)
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
	//.	bump an image down in the list of weights
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an Images_Image object [string]

	function decWeight($UID) {
		$this->checkWeights();
		$idx = $this->getIndex($UID);

		//------------------------------------------------------------------------------------------
		//	decrease weight by 1
		//------------------------------------------------------------------------------------------

		$model = new Images_Image($UID);
		if (false == $model->loaded) { return false; }
		if (0 == $model->weight) { return true; }			// nothing to do
		$model->weight -= 1;
		$model->save();

		//------------------------------------------------------------------------------------------
		//	increase weight of previous image by 1 (if any)
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
	//.	set an image as the default (weight 0)
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an Images_Image object [string]
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
			$model = new Images_Image();
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
	//.	count the number of images in this set
	//----------------------------------------------------------------------------------------------
	
	function count() {
		$count = count($this->members);
		return $count;
	}

}

?>
