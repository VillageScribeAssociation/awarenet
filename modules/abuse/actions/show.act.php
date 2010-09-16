<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display the complete thread fo an abuse report
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $request['ref']) {
		$session->msg("Abuse report not speicified (UID).", 'bad'); 
		$page->do302('abuse/'); 
	}

	if (false == $db->objectExists('Abuse_Report', $req->ref) { $page->do404(); }
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/abuse/actions/show.page.php');
	$page->blockArgs['UID'] = $request['ref'];
	$page->render();

?>
