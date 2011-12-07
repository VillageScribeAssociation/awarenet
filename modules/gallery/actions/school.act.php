<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list all galleries created by members of the given school
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Schools_School object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('school not given'); }

	$model = new Schools_School($req->ref);
	if (false == $model->loaded) { $page->do404('Unknown school.'); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	render the pages
	//----------------------------------------------------------------------------------------------
	$page->load('modules/gallery/actions/school.page.php');
	$page->blockArgs['schoolUID'] = $model->UID;
	$page->blockArgs['schoolName'] = $model->name;
	// ... etc
	$page->render();

?>
