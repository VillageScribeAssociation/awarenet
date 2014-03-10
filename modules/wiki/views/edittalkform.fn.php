<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edittalk form (for discussion about articles)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Wiki_Article object (the talk article) [string]

function wiki_edittalkform($args) {
		global $theme;
		global $user;

	$html = '';
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Wiki_Article($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('wiki', 'wiki_article', 'edit', $model->UID)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/wiki/views/edittalkform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
