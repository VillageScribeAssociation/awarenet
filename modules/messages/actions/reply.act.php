<?

//--------------------------------------------------------------------------------------------------
//	show form to create a new message
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot send mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->data['ofGroup']) { do403(); }
	require_once($installPath . 'modules/messages/models/message.mod.php');

	//----------------------------------------------------------------------------------------------
	//	is this to be sent to a specific user, or in response to another message
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('re', $request['args']) == false) { do404(); }
	$model = new Message($request['args']['re']);
	if ($model->data['owner'] != $user->data['UID']) { do403(); }	// not your message

	$jsrUID = $model->data['fromUID'];

	$userBlock = expandBlocks("[[:users::summarynav::userUID=" . $jsrUID . ":]]", '');

	$jsrHtml = "<div id='usrd" . $jsrUID . "'>"
					 . "<table noborder><td><a href='#' onClick=\"mcRemoveRecipient('" . $jsrUID . "')\">"
					 . "<img src='/themes/clockface/icons/arrow_x.jpg' border='0' /></a></td><td>"
					 . "$userBlock</td></tr></table></div>";

	$jsrUID = '|' . $jsrUID . '|';

	//----------------------------------------------------------------------------------------------
	//	show the form
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/messages/actions/reply.page.php');
	$page->blockArgs['toUser'] = $toUser;
	$page->blockArgs['reMsg'] = $reMsg;
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['folder'] = $model->data['folder'];
	$page->blockArgs['owner'] = $model->data['owner'];
	$page->blockArgs['subject'] = 'RE: ' . $model->data['title'];
	$page->blockArgs['jsRecipientUID'] = $jsrUID;
	$page->blockArgs['jsRecipientHtml'] = $jsrHtml;

	$page->render();

?>
