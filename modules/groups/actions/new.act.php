<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Group object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('groups', 'groups_group', 'new')) {
		$page->do403('You are not authorized to create new Groups.');
	}

	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Groups_Group();

	foreach($_POST as $key => $value) {				//TODO: more input sanitization here
		switch($key) {
			case 'school':			$model->school = $value;								break;
			case 'name':			$model->name = $utils->cleanTitle($value);				break;
			case 'type':			$model->type = $utils->cleanTitle($value);				break;
			case 'description':		$model->description = $utils->cleanHtml($value);		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Group: ' . $model->name . '<br/>', 'ok');
		$page->do302('groups/edit/' . $model->alias);
	} else {
		$session->msg('Could not create new Group:<br/>' . $report);
		$page->do302('groups/');
	}

?>
