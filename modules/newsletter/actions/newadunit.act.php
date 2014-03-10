<?

	require_once($kapenta->installPath . 'modules/newsletter/models/adunit.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Adunit object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('newsletter', 'newsletter_adunit', 'new')) {
		$kapenta->page->do403('You are not authorized to create new Adunits.');
	}


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Adunit();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':		$model->title = $value;		break;
			case 'tagline':		$model->tagline = $value;		break;
			case 'linktext':	$model->linktext = $value;		break;
			case 'linkurl':		$model->linkurl = $value;		break;
			case 'pinned':		$model->pinned = $value;		break;
			case 'weight':		$model->weight = $value;		break;
			case 'shared':		$model->shared = $value;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Adunit<br/>', 'ok');
		$kapenta->page->do302('/newsletter/editadunit/' . $model->UID);
	} else {
		$session->msg('Could not create new Adunit:<br/>' . $report);
		$kapenta->page->do302('/newsletter/');
	}

?>
