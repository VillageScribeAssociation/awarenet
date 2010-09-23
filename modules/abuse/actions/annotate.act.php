<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add an annotation to an abuse report
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $page->do404(); }
	if ('annotateReport' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not supplied.'); }

	$model = new Abuse_Report($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Abuse report not found.'); }

	if (false == array_key_exists('comment', $_POST)) { 
		$session->msg('No comment added.', 'bad');
		$page->do302('abuse/show/' . $model->UID);
	}

	//----------------------------------------------------------------------------------------------
	//	annotate redirect back to the issue
	//----------------------------------------------------------------------------------------------
	$model->annotate($user->data['UID'], $_POST['comment']);
	$page->do302('abuse/show/' . $model->UID);

?>
