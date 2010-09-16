<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	article statistics formatted for nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_statsnav($args) {
	global $theme;

	if (false == array_key_exists('raUID', $args)) { return false; }
	$html = '';
	
	$model = new Wiki_Article($args['raUID']);
	if (false == $model->loaded) { return ''; }
	$extArray = $model->extArray();

	//----------------------------------------------------------------------------------------------
	//	look up revision stats
	//----------------------------------------------------------------------------------------------
	//TODO create some stats for the nav
	//$sql = "select * from wikirevisions where refUID='" . $extArray['UID'] . "'";

	//----------------------------------------------------------------------------------------------
	//	assemble the block
	//----------------------------------------------------------------------------------------------

	$html = $theme->replaceLabels($extArray, $theme->loadBlock('modules/wiki/views/stats.block.php'));
	return $html; 
}

//--------------------------------------------------------------------------------------------------

?>
