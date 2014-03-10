<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a static page (Home_Static object)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteStaticPage' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Page not specified (UID).'); }
    
	$model = new Home_Static($_POST['UID']);
	if (false == $user->authHas('home', 'home_static', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorzed to delete this page.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the page and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted static page: " . $model->title);
	$kapenta->page->do302('/');

?>
