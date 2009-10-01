<?

//--------------------------------------------------------------------------------------------------
//	add a new announcements post
//--------------------------------------------------------------------------------------------------

	if (authHas('announcements', 'edit', '') == false) { do403(); }
	if (array_key_exists('refmodule', $request['args']) == false) { do403(); }
	if (array_key_exists('refuid', $request['args']) == false) { do403(); }

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');

	$model = new Announcement();
	$model->data['notifications'] = 'init';
	$model->data['title'] = '';
	$model->data['refModule'] = $request['args']['refmodule'];
	$model->data['refUID'] = $request['args']['refuid'];
	$model->save();

	do302('announcements/edit/' . $model->data['UID']);

?>
