<?

	require_once($kapenta->installPath . 'modules/home/models/partner.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Partner object, formatted for nav
//--------------------------------------------------------------------------------------------------

function home_addpartnernav($args) {
	global $user, $theme;

	$html = '';								//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('home', 'Home_Partner', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/home/views/addpartnernav.block.php');

	return $html;
}

?>
