<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add an annotation to an abuse report
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404(); }
	if ('annotateReport' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not supplied.'); }

	$model = new Abuse_Report($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Abuse report not found.'); }

	if (false == array_key_exists('comment', $_POST)) { 
		$kapenta->session->msg('No comment added.', 'bad');
		$kapenta->page->do302('abuse/show/' . $model->UID);
	}

	//----------------------------------------------------------------------------------------------
	//	annotate
	//----------------------------------------------------------------------------------------------
	//TODO: sanitize
	$comment = $_POST['comment'];
	$model->annotate($kapenta->user->UID, $comment);

	//----------------------------------------------------------------------------------------------
	//	notify other admins and redirect back to abuse report
	//----------------------------------------------------------------------------------------------
	$title = "Abuse report annotated by " . $kapenta->user->getName();
	$url = '/abuse/show/' . $model->UID;
	$nUID = $notifications->create(
		'abuse', 'abuse_report', $model->UID, 'abuse_annotated', $title, $comment, $url
	);
	$notifications->addAdmins($nUID);

	$kapenta->page->do302('abuse/show/' . $model->UID);

?>
