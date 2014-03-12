<?

	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Subscription object, formatted for nav
//--------------------------------------------------------------------------------------------------

function newsletter_addsubscriptionnav($args) {
		global $kapenta;
		global $theme;


	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('newsletter', 'Newsletter_Subscription', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/newsletter/views/addsubscriptionnav.block.php');

	return $html;
}

?>
