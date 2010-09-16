<?

	require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edittalk form (for discussion about articles)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_edittalkform($args) {
	global $theme;

	if ($user->authHas('wiki', 'Wiki_Article', 'edit', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Wiki($args['raUID']);
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/wiki/views/edittalkform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>