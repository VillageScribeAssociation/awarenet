<?

//--------------------------------------------------------------------------------------------------
//	show a mail item
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check the user is authorised to view the message
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->data['ofGroup']) { do403(); }					// not logged in
	require_once($installPath . 'modules/messages/models/message.mod.php');

	if ($request['ref'] == '') { do404(); }									// no message
	if (dbRecordExists('messages', $request['ref']) == false) { do404(); }	// nonexistent message

	$model = new Message($request['ref']);
	if ($model->data['owner'] != $user->data['UID']) { do403(); }			// not my message	

	//----------------------------------------------------------------------------------------------
	//	mark as read
	//----------------------------------------------------------------------------------------------

	if ('unread' == $model->data['status']) { $model->data['status'] = 'read'; $model->save(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/messages/actions/show.page.php');
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['folder'] = $model->data['folder'];
	$page->blockArgs['owner'] = $model->data['owner'];
	$page->render();

?>
