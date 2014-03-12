<?

	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Notice object
//--------------------------------------------------------------------------------------------------

function newsletter_addnotice($args) {
		global $kapenta;
		global $theme;


	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('newsletter', 'Newsletter_Notice', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/newsletter/views/addnotice.block.php');

	return $html;
}

?>
