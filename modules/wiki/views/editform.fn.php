<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show the edit form
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or wiki entry

function wiki_editform($args) {
	if (authHas('wiki', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Wiki($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/wiki/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>