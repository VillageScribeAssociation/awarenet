<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_editform($args) {
	global $theme, $user;

	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Wiki_Article($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('wiki', 'wiki_article', 'edit', $model->UID)) { return ''; }

	$block = $theme->loadBlock('modules/wiki/views/editform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
