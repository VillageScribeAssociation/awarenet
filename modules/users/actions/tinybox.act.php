<?

//--------------------------------------------------------------------------------------------------
//*	tiny little iframe to show a users details in chat
//--------------------------------------------------------------------------------------------------
//DEPRECATED:  TODO: check this is no longer used by anything and remove

	$model = new Users_User($req->ref);

	$page->load('modules/users/actions/tinybox.if.page.php');
	$page->blockArgs['userUID'] = $model->UID;
	$page->render();

?>
