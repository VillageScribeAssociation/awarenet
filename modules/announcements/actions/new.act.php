<?

//--------------------------------------------------------------------------------------------------
//	add a new announcements post
//--------------------------------------------------------------------------------------------------

	if (authHas('announcements', 'edit', '') == false) { do403(); }
	if (array_key_exists('refmodule', $request['args']) == false) { do404(); }
	if (array_key_exists('refuid', $request['args']) == false) { do404(); }

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to make an announcement for this item
	//----------------------------------------------------------------------------------------------

	$isauth = false;

	$model = new Announcement($request['ref']);
	$cb = "[[:". $request['args']['refmodule'] ."::haseditauth::raUID=".  $request['args']['refuid'] .":]]";
	$result = expandBlocks($cb, '');
	if ('yes' == $result) { $isauth = true; }

	//echo "result: $result <br/>\n";

	if ($user->data['ofGroup'] == 'admin') { $isauth = true; }
	if ($user->data['ofGroup'] == 'teacher') { $isauth = true; }
	if (false == $isauth) { do403(); die(); }

	//----------------------------------------------------------------------------------------------
	//	OK then, create it
	//----------------------------------------------------------------------------------------------

	$model = new Announcement();
	$model->data['notifications'] = 'init';
	$model->data['title'] = 'Announcement';
	$model->data['refModule'] = $request['args']['refmodule'];
	$model->data['refUID'] = $request['args']['refuid'];
	$model->save();

	do302('announcements/edit/' . $model->data['UID']);

?>
