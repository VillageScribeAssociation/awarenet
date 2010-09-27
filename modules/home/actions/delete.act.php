<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a static page (Home_Static object)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteStaticPage' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Page not specified (UID).'); }
    
	$model = new Home_Static($_POST['UID']);
	if (false == $user->authHas('home', 'Home_Static', 'delete', $model->UID))
		{ $page->do403('You are not authorzed to delete this page.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the page and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted static page: " . $model->title);
	$page->do302('/');

?>
