<?

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Edition object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('newsletter', 'newsletter_edition', 'new')) {
		$kapenta->page->do403('You are not authorized to create new Editions.');
	}


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Edition();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'subject':		$model->subject = $value;		break;
			case 'status':		$model->status = $value;		break;
			case 'publishdate':		$model->publishdate = $value;		break;
			case 'sentto':		$model->sentto = $value;		break;
			case 'abstract':		$model->abstract = $value;		break;
			case 'shared':		$model->shared = $value;		break;
			case 'alias':		$model->alias = $value;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$kapenta->session->msg('Created new Edition<br/>', 'ok');
		$kapenta->page->do302('/newsletter/editedition/' . $model->alias);
	} else {
		$kapenta->session->msg('Could not create new Edition:<br/>' . $report);
		$kapenta->page->do302('/newsletter/');
	}

?>
