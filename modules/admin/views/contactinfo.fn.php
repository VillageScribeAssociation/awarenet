<?

//--------------------------------------------------------------------------------------------------
//|	displays contact information for software distributor
//--------------------------------------------------------------------------------------------------

function admin_contactinfo($args) {
	global $theme;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	no permissions checks or arguments as yet
	//----------------------------------------------------------------------------------------------

	$html = $theme->loadBlock('modules/admin/views/contactinfo.block.php');
	return $html;
}


?>
