<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Package object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('code', 'code_package', 'new')) {
		$kapenta->page->do403('You are not authorized to create new Packages.');
	}

	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Code_Package();
	$model->revision = '0';

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'name':			$model->name = $value;				break;
			case 'description':		$model->description = $value;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Package<br/>', 'ok');
		$kapenta->page->do302('/code/editpackage/' . $model->alias);
	} else {
		$session->msg('Could not create new Package:<br/>' . $report);
		$kapenta->page->do302('/code/');
	}

?>
