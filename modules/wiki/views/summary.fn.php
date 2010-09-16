<?

	require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_summary($args) {
	global $db;

	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Wiki($db->addMarkup($args['raUID']));	
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/wiki/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>