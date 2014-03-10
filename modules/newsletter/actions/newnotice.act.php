<?

	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Notice object
//--------------------------------------------------------------------------------------------------
//TODO: force edition UID

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('newsletter', 'newsletter_notice', 'new')) {
		$kapenta->page->do403('You are not authorized to create new Notices.');
	}


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Notice();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'edition':		$model->edition = $value;		break;
			case 'title':		$model->title = $value;		break;
			case 'content':		$model->content = $value;		break;
			case 'category':		$model->category = $value;		break;
			case 'shared':		$model->shared = $value;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Notice<br/>', 'ok');
		$kapenta->page->do302('/newsletter/showedition/' . $model->edition);
	} else {
		$session->msg('Could not create new Notice:<br/>' . $report);
		$kapenta->page->do302('/newsletter/');
	}

?>
