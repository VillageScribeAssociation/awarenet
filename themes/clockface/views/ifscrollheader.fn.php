<?

//--------------------------------------------------------------------------------------------------
//|	assembles HTML header for scrolling, progressively updated iframe
//--------------------------------------------------------------------------------------------------
//opt: title - page title, default is website name [string]

function theme_ifscrollheader($args) {
	global $theme;
	global $kapenta;

	$title = $kapenta->websiteName;				//%	page title [string]
	$html = '';									//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('title', $args)) { $title = $args['title']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('themes/clockface/views/ifscrollheader.block.php');

	$labels = array(
		'serverPath' => $kapenta->serverPath,
		'pageTitle' => $title
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
