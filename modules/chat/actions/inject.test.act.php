<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/inbox.mod.php');

//--------------------------------------------------------------------------------------------------
//*	test /development action to inject a chat message directly into a user's local inbox
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('room', $_POST)) { $page->do404('room not given'); }
	if (false == array_key_exists('message', $_POST)) { $page->do404('message not given'); }
	if (false == array_key_exists('from', $_POST)) { $page->do404('from UID not given'); }

	$room = new Chat_Room($_POST['room']);
	if (false == $room->loaded) { $page->do404('Room not found.'); }

	//----------------------------------------------------------------------------------------------
	//	add the inbox item
	//----------------------------------------------------------------------------------------------
	echo "Injecting message: " . $_POST['message'] . "<br/>";

	$model = new Chat_Inbox();
	$model->room = $_POST['room'];
	$model->fromUser =  $_POST['from'];
	$model->toUser = $user->UID;
	$model->message = $_POST['message'];
	$model->delivered = 'no';
	$model->createdBy = $_POST['from'];
	$report = $model->save();

	if ('' == $report) { echo "Message sent.<br/>"; }
	else { echo $report; }

?>
