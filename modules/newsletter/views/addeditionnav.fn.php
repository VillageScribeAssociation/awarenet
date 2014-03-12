<?

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Edition object, formatted for nav
//--------------------------------------------------------------------------------------------------

function newsletter_addeditionnav($args) {
		global $kapenta;
		global $theme;


	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('newsletter', 'Newsletter_Edition', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/newsletter/views/addeditionnav.block.php');

	return $html;
}

?>
