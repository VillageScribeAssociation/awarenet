<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//	show form to create a new message
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot send mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	is this to be sent to a specific user, or in response to another message
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('re', $req->args)) { $page->do404('message not specified'); }
	$model = new Messages_Message($req->args['re']);
	if ($model->owner != $user->UID) { $page->do403(); }	// not your message

	$jsrUID = $model->fromUID;

	$userBlock = $theme->expandBlocks("[[:users::summarynav::userUID=" . $jsrUID . ":]]", '');

	$jsrHtml = "<div id='usrd" . $jsrUID . "'>"
					 . "<table noborder><td><a href='#' onClick=\"mcRemoveRecipient('" . $jsrUID . "')\">"
					 . "<img src='/themes/clockface/icons/arrow_x.jpg' border='0' /></a></td><td>"
					 . "$userBlock</td></tr></table></div>";

	$jsrUID = '|' . $jsrUID . '|';

	//----------------------------------------------------------------------------------------------
	//	show the form
	//----------------------------------------------------------------------------------------------

	$page->load('modules/messages/actions/reply.page.php');
	$page->blockArgs['toUser'] = $model->fromUID;
	$page->blockArgs['reMsg'] = $model->UID;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['folder'] = $model->folder;
	$page->blockArgs['owner'] = $model->owner;
	$page->blockArgs['subject'] = 'RE: ' . $model->title;
	$page->blockArgs['jsRecipientUID'] = $jsrUID;
	$page->blockArgs['jsRecipientHtml'] = $jsrHtml;

	$page->render();

?>
