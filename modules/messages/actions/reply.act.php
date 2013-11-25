<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show form to create a new message
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot send mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	is this to be sent to a specific user, or in response to another message
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('re', $kapenta->request->args)) { $page->do404('message not specified'); }
	$msgUID = str_replace('?', '', $kapenta->request->args['re']);		// TODO: better removal of querystring
	$model = new Messages_Message($msgUID);		
	if (false == $model->loaded) { $page->do404('Message not found'); }
	if ($model->owner != $user->UID) { $page->do403('not owner of message'); }	// not your message

	$jsrUID = $model->fromUID;

	$userBlock = $theme->expandBlocks("[[:users::summarynav::userUID=" . $jsrUID . ":]]", '');

	$jsrHtml = ''
	 . "<div id='usrd" . $jsrUID . "'>\n"
	 . "  <table noborder>\n"
	 . "    <tr>\n"
	 . "      <td>\n"
	 . "        <a href='#' onClick=\"mcRemoveRecipient('" . $jsrUID . "')\">\n"
	 . "          <img src='/themes/%%defaultTheme%%/images/icons/arrow_x.jpg' border='0' />\n"
	 . "        </a>\n"
	 . "      </td>\n"
	 . "      <td>$userBlock</td>\n"
	 . "    </tr>\n"
	 . "  </table>\n"
	 . "</div>\n";

	$jsrUID = '|' . $jsrUID . '|';

	//----------------------------------------------------------------------------------------------
	//	show the form
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/messages/actions/reply.page.php');
	$kapenta->page->blockArgs['toUser'] = $model->fromUID;
	$kapenta->page->blockArgs['reMsg'] = $model->UID;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['folder'] = $model->folder;
	$kapenta->page->blockArgs['owner'] = $model->owner;
	$kapenta->page->blockArgs['subject'] = 'RE: ' . $model->title;
	$kapenta->page->blockArgs['jsRecipientUID'] = $jsrUID;
	$kapenta->page->blockArgs['jsRecipientHtml'] = $jsrHtml;

	$kapenta->page->render();

?>
