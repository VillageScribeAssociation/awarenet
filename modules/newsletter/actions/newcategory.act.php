<?

	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Category object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('newsletter', 'newsletter_category', 'new')) {
		$page->do403('You are not authorized to create new Categorys.');
	}


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Category();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'name':		$model->name = $value;					break;
			case 'description':		$model->description = $value;		break;
			case 'weight':		$model->weight = $value;				break;
			case 'shared':		$model->shared = $value;				break;
			case 'alias':		$model->alias = $value;					break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Category<br/>', 'ok');
		//$page->do302('/newsletter/editcategory/' . $model->alias);
		$page->do302('newslettter/listcategories/');
	} else {
		$session->msg('Could not create new Category:<br/>' . $report);
		$page->do302('newsletter/');
	}

?>
