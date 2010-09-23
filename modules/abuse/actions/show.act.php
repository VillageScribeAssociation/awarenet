<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display the complete thread fo an abuse report
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) {
		$session->msg("Abuse report not speicified (UID).", 'bad'); 
		$page->do302('abuse/'); 
	}

	$model = new Abuse_Report($req->ref);
	if (false == $model->loaded) { $page->do404('Report not found.'); }
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/abuse/actions/show.page.php');
	$page->blockArgs['UID'] = $req->ref;
	$page->render();

?>
