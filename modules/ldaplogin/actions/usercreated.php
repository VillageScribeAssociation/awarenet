<?
//--------------------------------------------------------------------------------------------------
//*	page for signing up users
//--------------------------------------------------------------------------------------------------
//TODO: tidy this up, add settings and permissions appropriate to this


	//----------------------------------------------------------------------------------------------
	//	form variables
	//----------------------------------------------------------------------------------------------

	$formvars = array(
		'username' => $kapenta->user->username,	
		'password' => $kapenta->user->password,
		'redirect' => $kapenta->serverPath . '/users/profile/'
	);

	//----------------------------------------------------------------------------------------------
	//	show page
	//----------------------------------------------------------------------------------------------

	if ($showPage == true) {
		$kapenta->page->load('modules/ldaplogin/actions/usercreated.page.php');
		foreach($formvars as $field => $value) { $kapenta->page->blockArgs[$field] = $value; }
		$kapenta->page->render();
	}

?>
