<?

	require_once($kapenta->installPath . 'modules/chat/models/hash.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object for caching / retrieving stored hashes
//--------------------------------------------------------------------------------------------------

class Chat_Hashes {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	
	var $members;						//_	serialized Chat_Hash objects [array]
	var $membersLoaded = false;			//_	set to true when all members have been loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Chat_Hashes() {
		//TODO
	}

	//----------------------------------------------------------------------------------------------
	//.	get a stored hash by label
	//----------------------------------------------------------------------------------------------
	//returns: hash if found, empty string if not found [string]

	function get($label) {
		$model = new Chat_Hash($label);
		if (false == $model->loaded) { return ''; }
		return $model->hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	store a hash
	//----------------------------------------------------------------------------------------------

	function set($label, $hash) {
		$model = new Chat_Hash($label, true);
		$model->label = $label;
		$model->hash = $hash;
		$report = $model->save();
		if ('' == $report) { return true; }
		return false;
	}

}

?>
