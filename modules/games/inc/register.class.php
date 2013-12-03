<?php

//--------------------------------------------------------------------------------------------------
//*	utility object for registering and re-ordering games
//--------------------------------------------------------------------------------------------------

class Games_Register {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $games = array();			//_	list of games [array:string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Games_Register() {
		global $kapenta;

		$list = $kapenta->registry->get('games.list');
		$games = explode(",", $list);

		$this->games = array();

		foreach($games as $title) {
			$this->games[] = trim($title);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	reset the list in the registry
	//----------------------------------------------------------------------------------------------

	function store() {
		global $kapenta;
		$check = $kapenta->registry->set('games.list', implode(',', $this->games));
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover is a game is known to the list
	//----------------------------------------------------------------------------------------------

	function has($title) { return in_array($title, $this->games); }

	//----------------------------------------------------------------------------------------------
	//.	add a game to the list
	//----------------------------------------------------------------------------------------------
	//arg: $title - name of a game to add to the list [string]
	//returns: true on success, false on failure [bool]

	function add($title) {
		if (true == $this->has($title)) { return false; }
		$this->games[] = $title;
		$check = $this->store();
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a game from the list
	//----------------------------------------------------------------------------------------------
	//arg: $title - name of a game to remove from the list [string]
	//returns: true on success, false on failure [bool]	

	function remove($title) {
		if (false == $this->has($title)) { return false; }
		$newList = array();

		foreach($this->games as $item) {
			if ($title != $item) {
				$newList[] = $item;
			}
		}

		$this->games = $newList;
		$check = $this->store();
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	bump a game up the list
	//----------------------------------------------------------------------------------------------
	//TODO: this

	//----------------------------------------------------------------------------------------------
	//.	bump a game down the list
	//----------------------------------------------------------------------------------------------
	//TODO: this


}

?>
