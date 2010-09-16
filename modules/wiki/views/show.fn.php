<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show an article
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_show($args) {
	global $theme;

	if (false == array_key_exists('raUID', $args)) { return false; }
	$model = new Wiki_Article($args['raUID']);
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/wiki/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
