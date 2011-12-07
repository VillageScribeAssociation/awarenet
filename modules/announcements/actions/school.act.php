<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display announcements from the specified school
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Schools_School object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { 
		if ('public' == $user->role) { $page->do404('School nor given.'); }
		$req->ref = $user->school;
	}

	$model = new Schools_School($req->ref);
	if (false == $model->loaded) { $page->do404('Unknown school.'); }
	
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show')) {
		$page->do403("You are not authorised to view announcements from this school.");
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/announcements/actions/school.page.php');
	$page->blockArgs['schoolUID'] = $model->UID;
	$page->blockArgs['schoolRa'] = $model->alias;
	$page->render();

?>
