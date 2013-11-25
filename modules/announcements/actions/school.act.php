<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display announcements from the specified school
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Schools_School object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { 
		if ('public' == $user->role) { $page->do404('School nor given.'); }
		$kapenta->request->ref = $user->school;
	}

	$model = new Schools_School($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Unknown school.'); }
	
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show')) {
		$page->do403("You are not authorised to view announcements from this school.");
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/announcements/actions/school.page.php');
	$kapenta->page->blockArgs['schoolUID'] = $model->UID;
	$kapenta->page->blockArgs['schoolRa'] = $model->alias;
	$kapenta->page->render();

?>
