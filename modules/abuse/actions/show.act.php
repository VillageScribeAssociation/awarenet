<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display the complete thread fo an abuse report
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $kapenta->request->ref) {
		$session->msg("Abuse report not speicified (UID).", 'bad'); 
		$page->do302('abuse/'); 
	}

	$model = new Abuse_Report($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Report not found.'); }
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/abuse/actions/show.page.php');
	$kapenta->page->blockArgs['UID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
