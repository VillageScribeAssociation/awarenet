<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Badge object, formatted for nav
//--------------------------------------------------------------------------------------------------

function badges_addbadgenav($args) {
	global $user, $theme;
	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('badges', 'Badges_Badge', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/badges/views/addbadgenav.block.php');
	return $html;
}

?>
