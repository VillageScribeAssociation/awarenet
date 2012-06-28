<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	short summary of video for the nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
//opt: videoUID - replaces raUID if present [string]
//opt: behavior - Behavior when links are clicked (link|editmodal) [string]

function videos_videosummarynav($args) {
	global $user;
	global $theme;
	global $page;

	$behavior = 'link';						//%	behavior of links [string]
	$html = '';		//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: check permissions here

	if (true == array_key_exists('behavior', $args)) { $behavior = $args['behavior']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/videosummarynav.block.php');
	$labels = $model->extArray();

	$labels['extra'] = '';

	if ('editmodal' == $behavior) {
		$page->requireJs('%%serverPath%%modules/videos/js/editor.js');
		$labels['viewUrl'] = "javascript:Videos_EditModal('" . $model->UID . "');";
		$labels['controls'] = ''
		 . "<span style='float: right;'>"
		 . "<small>"
		 . "<a href=\"javascript:Videos_EditTags('" . $model->UID . "');\">[edit tags]</a>"
		 . "<a href=\"javascript:Videos_Delete('" . $model->UID . "');\">[delete]</a>"
		 . "<a href=\"javascript:Videos_MakeDefault('" . $model->UID . "');\">[move to top]</a>"
		 . "</small>"
		 . '';
	}


	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
