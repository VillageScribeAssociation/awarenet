<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	view a school object (if none specified, default is user's own school)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication (public users no longer banned)
	//----------------------------------------------------------------------------------------------
	//if (($user->role == 'public') || ($user->role == 'banned')) { $page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	check reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $req->ref = $user->school; }
	$aliases->findRedirect('Schools_School');

	//----------------------------------------------------------------------------------------------
	//	load model
	//----------------------------------------------------------------------------------------------
	$model = new Schools_School($req->ref);
	if (false == $model->loaded) { $page->do404('could not find school: ' . $req-ref); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/schools/actions/show.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['schoolName'] = $model->name;
	$page->blockArgs['schoolRa'] = $model->alias;
	$page->blockArgs['schoolUID'] = $model->UID;
	$page->render();

?>
