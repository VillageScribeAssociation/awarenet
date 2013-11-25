<?

	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Subscription object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('newsletter', 'newsletter_subscription', 'new')) {
		$page->do403('You are not authorized to create new Subscriptions.');
	}


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Subscription();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'status':		$model->status = $value;		break;
			case 'email':		$model->email = $value;			break;
			case 'shared':		$model->shared = $value;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Subscription<br/>', 'ok');
		$page->do302('/newsletter/listsubscriptions/' . $model->UID);
	} else {
		$session->msg('Could not create new Subscription:<br/>' . $report);
		$page->do302('/newsletter/');
	}

?>
