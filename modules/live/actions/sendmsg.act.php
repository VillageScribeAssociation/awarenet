<?

	require_once($kapenta->installPath . 'modules/live/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//*	send a chat message from one user to another
//--------------------------------------------------------------------------------------------------
//note: this is called by individual chat windows as defined in ../js/chatwindow.js

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->doXmlError('Not logged in.'); }
	if ('banned' == $user->role) { $page->doXmlError('Banhammered.'); }

	if (false == array_key_exists('UID', $_POST)) { $page->doXmlError('UID not given.'); }
	if (false == array_key_exists('fromUID', $_POST)) { $page->doXmlError('fromUID not given.'); }
	if (false == array_key_exists('toUID', $_POST)) { $page->doXmlError('toUID not given.'); }
	if (false == array_key_exists('msg', $_POST)) { $page->doXmlError('msg not given.'); }

	$UID = $_POST['UID'];
	$fromUID = $_POST['fromUID'];
	$toUID = $_POST['toUID'];
	$msg = $_POST['msg'];
	
	if ($fromUID != $user->UID) { $page->doXmlError('Not logged in.'); }

	if (false == $kapenta->db->objectExists('users_user', $fromUID))
		{ $page->doXmlError('No such user (fromUID)'); }

	if (false == $kapenta->db->objectExists('users_user', $toUID))
		{ $page->doXmlError('No such user (fromUID)'); }

	//----------------------------------------------------------------------------------------------
	//	save creator's copy (outbox)
	//----------------------------------------------------------------------------------------------

	$model = new Live_Chat();
	$model->UID = $UID;
	$model->fromUID = $fromUID;
	$model->toUID = $toUID;
	$model->ownerUID = $fromUID;
	$model->msg = $utils->cleanString($msg);
	$model->state = 'new';
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	save recipient's copy (inbox)
	//----------------------------------------------------------------------------------------------

	$model = new Live_Chat();
	$model->UID = $UID;
	$model->fromUID =  $fromUID;
	$model->toUID =  $toUID;
	$model->ownerUID = $toUID;
	$model->msg =  $utils->cleanString($msg);
	$model->state = 'new';
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	echo "OK";

?>
