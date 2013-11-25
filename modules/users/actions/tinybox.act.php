<?

//--------------------------------------------------------------------------------------------------
//*	tiny little iframe to show a users details in chat
//--------------------------------------------------------------------------------------------------
//DEPRECATED:  TODO: check this is no longer used by anything and remove

	$model = new Users_User($kapenta->request->ref);

	$kapenta->page->load('modules/users/actions/tinybox.if.page.php');
	$kapenta->page->blockArgs['userUID'] = $model->UID;
	$kapenta->page->render();

?>
