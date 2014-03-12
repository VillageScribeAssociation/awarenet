<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	article statistics formatted for nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_statsnav($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check argument and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	
	$model = new Wiki_Article($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('wiki', 'wiki_article', 'show', $model->UID)) { return ''; }
	$extArray = $model->extArray();

	//----------------------------------------------------------------------------------------------
	//	look up revision stats
	//----------------------------------------------------------------------------------------------
	$conditions = array("articleUID='" . $model->UID . "'");
	$extArray['totalRevisions'] = $kapenta->db->countRange('wiki_revision', $conditions);

	//----------------------------------------------------------------------------------------------
	//	assemble the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/wiki/views/stats.block.php');
	$html = $theme->replaceLabels($extArray, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
