<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	set an image to be the current user's theme background image
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of an Images_Image object [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and reference
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { $page->do403('Please log in.'); }

	if ('' == $kapenta->request->ref) { $page->do404('Image not specified.'); }
	
	$model = new Images_Image($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Image not found.'); }
	
	$user->set('ut.i.background', 'images/full/' . $model->alias);

	//----------------------------------------------------------------------------------------------
	//	redirect to theme settings
	//----------------------------------------------------------------------------------------------
	$page->do302('users/myaccount/');

?>
