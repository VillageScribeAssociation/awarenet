<?

//--------------------------------------------------------------------------------------------------
//|	show picturelogin 
//--------------------------------------------------------------------------------------------------

function picturelogin_pictureloginpictures($args) {
	global $kapenta;
	global $theme;

	$html = '';						//%	return value [string]
	//----------------------------------------------------------------------------------------------
	//	make and return the form
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/picturelogin/views/pictureloginpictures.block.php');
	$html = $theme->replaceLabels($args, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
