<?

//--------------------------------------------------------------------------------------------------
//*	show form to create a new message
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot send mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	is this to be sent to a specific user, or in response to another message
	//----------------------------------------------------------------------------------------------
	$jsrUID = '';
	$jsrHtml ='';
	$toUser = '';
	$reMsg = '';

	if ( (true == array_key_exists('to', $kapenta->request->args))
		&& (true == $kapenta->db->objectExists('users_user', $kapenta->request->args['to'])) ) { 
	
		$jsrUID = $kapenta->request->args['to'];
		$userBlock = $theme->expandBlocks("[[:users::summarynav::userUID=" . $jsrUID . ":]]", '');

		$jsrHtml = "<div id='usrd" . $jsrUID . "'>"
					 . "<table noborder><td><a href='#' onClick=\"mcRemoveRecipient('" . $jsrUID . "')\">"
					 . "<img src='/themes/%%defaultTheme%%/images/icons/arrow_x.jpg' border='0' /></a></td><td>"
					 . "$userBlock</td></tr></table></div>";

		$jsrUID = '|' . $jsrUID . '|';
	}

	//----------------------------------------------------------------------------------------------
	//	show the form
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/messages/actions/compose.page.php');
	$kapenta->page->blockArgs['owner'] = $kapenta->user->UID;
	$kapenta->page->blockArgs['toUser'] = $toUser;
	$kapenta->page->blockArgs['reMsg'] = $reMsg;
	$kapenta->page->blockArgs['subject'] = '';
	$kapenta->page->blockArgs['jsRecipientUID'] = $jsrUID;
	$kapenta->page->blockArgs['jsRecipientHtml'] = $jsrHtml;
	$kapenta->page->render();

?>
