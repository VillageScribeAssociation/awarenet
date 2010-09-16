<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or groups entry
// * $args['postUID'] = overrides raUID

function sync_serversummarynav($args) {
	global $theme;
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('postUID', $args) == true) { $args['raUID'] = $args['postUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Sync_Server($args['raUID']);	
	if (false == $model->loaded) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make and return the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/sync/summarynav.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
