<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a gallery [string]
//opt: pageUID - overrides raUID [string]

function gallery_summary($args) {
	global $theme, $user;
	$html = '';		//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('pageUID', $args)) { $args['raUID'] = $args['pageUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Gallery_Gallery($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here

	$labels = $model->extArray();
	//TODO: live JS

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/gallery/views/summary.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

