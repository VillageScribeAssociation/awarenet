<?

	require_once($kapenta->installPath . 'modules/popular/models/ladder.mod.php');

//--------------------------------------------------------------------------------------------------
//|	records a local item view
//--------------------------------------------------------------------------------------------------
//arg: ladder - name of content ladder [string]
//arg: item - item to bump up the ladder [string]

function popular__cb_popularity_bump($args) {
	global $kapenta;
	global $session;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('ladder', $args)) { return false; }
	if (false == array_key_exists('item', $args)) { return false; }

	$ladder = $args['ladder'];
	$item = $args['item'];	

	//----------------------------------------------------------------------------------------------
	//	try load the ladder or create it if it does not exist
	//----------------------------------------------------------------------------------------------
	$model = new Popular_Ladder($ladder, true);
	if (false == $model->loaded) {
		$model->UID = $kapenta->createUID();
		$model->name = $ladder;
		$report = $model->save();
		if ('' != $report) { $session->msgAdmin("Could not create ladder:<br/>$report", 'bad'); }
	}

	//----------------------------------------------------------------------------------------------
	//	bump the item
	//----------------------------------------------------------------------------------------------
	//$oldRank = $model->getRank($item);
	$model->bump($item);
	
	//temp for debugging  TODO: remove
	//$rank = $model->getRank($item);
	//$session->msg("Bumped item: $item on ladder: $ladder Old rank: $oldRank New rank: $rank", 'ok');

}

?>
