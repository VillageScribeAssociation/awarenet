<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list all galleries created by members of the given school
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Schools_School object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('school not given'); }

	$model = new Schools_School($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Unknown school.'); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	render the pages
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/gallery/actions/school.page.php');
	$kapenta->page->blockArgs['schoolUID'] = $model->UID;
	$kapenta->page->blockArgs['schoolName'] = $model->name;
	// ... etc
	$kapenta->page->render();

?>
