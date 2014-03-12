<?

	require_once($kapenta->installPath . 'modules/code/models/bug.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Bug object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == authHas('Code', 'bug', 'new')) {
		$kapenta->page->do403('You are not authorized to create new Bugs.');
	}


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Bug();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'package':		$model->package = $value;		break;
			case 'memberType':		$model->memberType = $value;		break;
			case 'guestName':		$model->guestName = $value;		break;
			case 'guestEmail':		$model->guestEmail = $value;		break;
			case 'title':		$model->title = $value;		break;
			case 'description':		$model->description = $value;		break;
			case 'status':		$model->status = $value;		break;
			case 'alias':		$model->alias = $value;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$kapenta->session->msg('Created new Bug<br/>', 'ok');
		$kapenta->page->do302('/Code/editbug/' . $model->alias);
	} else {
		$kapenta->session->msg('Could not create new Bug:<br/>' . $report);
	}

?>
