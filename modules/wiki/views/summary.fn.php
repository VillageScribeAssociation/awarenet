<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_summary($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Wiki(sqlMarkup($args['raUID']));	
	return replaceLabels($model->extArray(), loadBlock('modules/wiki/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
