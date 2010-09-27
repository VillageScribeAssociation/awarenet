<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Badge object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//*	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('badges', 'Badges_Badge', 'new'))
		{ $page->do403('You are not authorized to create new Badges.'); }

	//----------------------------------------------------------------------------------------------
	//*	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Badges_Badge();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':	$model->name = $utils->cleanString($value); break;
			case 'description':	$model->description = $utils->cleanString($value); break;
			case 'alias':	$model->alias = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//*	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created New Badge<br/>');
		$page->do302('/badges/edit/' . $model->alias);
	} else {
		$session->msg('Could not create new Badge:<br/>' . $report);
		$page->do302('badges/');
	}



?>
