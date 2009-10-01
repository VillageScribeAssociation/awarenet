<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show an article
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or wiki entry

function wiki_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Wiki($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/wiki/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>