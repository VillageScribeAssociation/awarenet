<?

//--------------------------------------------------------------------------------------------------
//|	public sign up link
//--------------------------------------------------------------------------------------------------

function users_signuplink($args) {
	global $theme;
	global $kapenta;
	$html = '';				//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	public signup is controlled by a registry setting on the users module
	//----------------------------------------------------------------------------------------------
	if ('yes' != $kapenta->registry->get('users.allowpublicsignup')) { return ''; }
	$html = $theme->loadBlock('modules/users/views/signuplink.block.php');

	return $html;
}

?>
