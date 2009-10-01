<?

//--------------------------------------------------------------------------------------------------
//	show form to create a new message
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot send mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->data['ofGroup']) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	is this to be sent to a specific user, or in response to another message
	//----------------------------------------------------------------------------------------------
	$jsrUID = ''; $jsrHtml ='';

	if  (	(array_key_exists('to', $request['args']) == true) 
		&& 	(dbRecordExists('users', $request['args']['to']) == true) ) { 
	
		$jsrUID = $request['args']['to'];
		$userBlock = expandBlocks("[[:users::summarynav::userUID=" . $jsrUID . ":]]", '');

		$jsrHtml = "<div id='usrd" . $jsrUID . "'>"
					 . "<table noborder><td><a href='#' onClick=\"mcRemoveRecipient('" . $jsrUID . "')\">"
					 . "<img src='/themes/clockface/icons/arrow_x.jpg' border='0' /></a></td><td>"
					 . "$userBlock</td></tr></table></div>";

		$jsrUID = '|' . $jsrUID . '|';
	}

	//----------------------------------------------------------------------------------------------
	//	show the form
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/messages/actions/compose.page.php');
	$page->blockArgs['owner'] = $user->data['UID'];
	$page->blockArgs['toUser'] = $toUser;
	$page->blockArgs['reMsg'] = $reMsg;
	$page->blockArgs['subject'] = '';
	$page->blockArgs['jsRecipientUID'] = $jsrUID;
	$page->blockArgs['jsRecipientHtml'] = $jsrHtml;
	$page->render();

?>
