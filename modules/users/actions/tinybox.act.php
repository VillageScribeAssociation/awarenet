<?

//--------------------------------------------------------------------------------------------------
//	tiny little iframe to show a users details in chat
//--------------------------------------------------------------------------------------------------

	$model = new Users($request['ref']);

	$page->load($installPath . 'modules/users/actions/tinybox.if.page.php');
	$page->blockArgs['userUID'] = $model->data['UID'];
	$page->render();

?>
