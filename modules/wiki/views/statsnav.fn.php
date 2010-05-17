<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	article statistics formatted for nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or wiki entry [string]

function wiki_statsnav($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';
	
	$model = new Wiki($args['raUID']);
	$extArray = $model->extArray();

	//----------------------------------------------------------------------------------------------
	//	look up revision stats
	//----------------------------------------------------------------------------------------------

	$sql = "select * from wikirevisions where refUID='" . $extArray['UID'] . "'";

	//----------------------------------------------------------------------------------------------
	//	assemble the block
	//----------------------------------------------------------------------------------------------

	$html = replaceLabels($extArray, loadBlock('modules/wiki/views/stats.block.php'));
	return $html; 
}

//--------------------------------------------------------------------------------------------------

?>
