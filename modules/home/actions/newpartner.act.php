<?

	require_once($kapenta->installPath . 'modules/home/models/partner.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Partner object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('home', 'home_partner', 'new')) {
		$page->do403('You are not authorized to create new Partners.');
	}

	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Home_Partner();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':			$model->title = $value;				break;
			case 'description':		$model->description = $value;		break;
			case 'url':				$model->url = $value;				break;
			case 'weight':			$model->weight = $value;			break;
			case 'shared':			$model->shared = $value;			break;
			case 'alias':			$model->alias = $value;				break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Partner<br/>', 'ok');
		$page->do302('/home/editpartner/' . $model->alias);
	} else {
		$session->msg('Could not create new Partner:<br/>' . $report);
		$page->do302('/home/');
	}

?>
