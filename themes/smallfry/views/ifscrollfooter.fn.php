<?

//--------------------------------------------------------------------------------------------------
//|	ends a scrolling, progressive iframe
//--------------------------------------------------------------------------------------------------

function theme_ifscrollfooter($args) {
	global $theme;
	$html = '';				//%	return value [string]

	$html = $theme->loadBlock('themes/smallfry/views/ifscrollfooter.block.php');

	return $html;
}

?>
