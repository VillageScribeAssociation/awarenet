<?

//--------------------------------------------------------------------------------------------------
//|	show picturelogin 
//--------------------------------------------------------------------------------------------------

function picturelogin_pictureloginpassword($args) {
	global $kapenta;
	global $theme;

	$html = '';						//%	return value [string]
	//----------------------------------------------------------------------------------------------
	//	make and return the form
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/picturelogin/views/pictureloginpassword.block.php');
	$html = $theme->replaceLabels($args, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
