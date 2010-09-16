<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save updates to server records
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); } // only admins can use this module
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('updateServer' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Server not specified (UID).'); }


	//----------------------------------------------------------------------------------------------
	//	load and update the Sync_Server object
	//----------------------------------------------------------------------------------------------
	$model = new Sync_Server($UID);
	if (false == $model->loaded) { $page->do404("could not load Server $UID");}

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'servername':	$model->servername = $utils->cleanString($value); break;
			case 'serverurl':	$model->serverurl = $utils->cleanString($value); break;
			case 'password':	$model->password = $utils->cleanString($value); break;
			case 'direction':	$model->direction = $utils->cleanString($value); break;
			case 'active':	$model->active = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to server.', 'ok'); }
	else { $session->msg('Could not save Server:<br/>' . $report, 'bad');

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('/sync/show/' . $model->UID); }

?>
