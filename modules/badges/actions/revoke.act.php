<?

	require_once($kapenta->installPath . 'modules/badges/models/userindex.mod.php');

//--------------------------------------------------------------------------------------------------
//*	strip a user of a badge
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }		//TODO: use a permission for this
	if ('' == $kapenta->request->ref) { $page->do404('Badge award not specified.'); }
	
	$model = new Badges_UserIndex($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Award not found.'); }

	$model->delete();
	$userLink = '[[:users::namelink::userUID=' . $model->userUID . ':]]';
	$session->msg('Removed award of this badge from ' . $userLink);

	//----------------------------------------------------------------------------------------------
	//	delete the award and redirect back to badge page
	//----------------------------------------------------------------------------------------------
	$page->do302('badges/show/' . $model->badgeUID);

?>
