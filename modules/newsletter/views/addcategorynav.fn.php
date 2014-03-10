<?

	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Category object, formatted for nav
//--------------------------------------------------------------------------------------------------

function newsletter_addcategorynav($args) {
		global $user;
		global $theme;


	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('newsletter', 'Newsletter_Category', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/newsletter/views/addcategorynav.block.php');

	return $html;
}

?>
