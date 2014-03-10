<?

	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	summarize a Wiki_Revision in the nav (~300px wide)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Wiki_Revision object [string]

function wiki_revisionsummarynav($args) {
		global $user;
		global $kapenta;
		global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check argument and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Wiki_Revision($args['UID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('wiki', 'wiki_revision', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/wiki/views/revisionsummarynav.block.php');
	$ext = $model->extArray();
	if ('' == trim($ext['reason'])) { $ext['reason'] = "<i>(no reason given)</i>"; }
	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

?>
