<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	view a school object (if none specified, default is user's own school)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication (public users no longer banned)
	//----------------------------------------------------------------------------------------------
	//if (($kapenta->user->role == 'public') || ($kapenta->user->role == 'banned')) { $kapenta->page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	check reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = $kapenta->user->school; }
	$aliases->findRedirect('schools_school');

	//----------------------------------------------------------------------------------------------
	//	load model
	//----------------------------------------------------------------------------------------------
	$model = new Schools_School($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('could not find school: ' . $kapenta->request->ref); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/schools/actions/show.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['schoolName'] = $model->name;
	$kapenta->page->blockArgs['schoolRa'] = $model->alias;
	$kapenta->page->blockArgs['schoolUID'] = $model->UID;
	$kapenta->page->render();

?>
