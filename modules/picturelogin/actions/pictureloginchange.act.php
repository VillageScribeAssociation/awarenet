<?
	require_once($kapenta->installPath . 'modules/picturelogin/inc/picturelogin.php');

//--------------------------------------------------------------------------------------------------
//*	show icons, drag & drop into iconpassword fields, generate password field, password fields (user copies text) and change password 
//* button 
//--------------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('Pictures' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.'); }

	// users may only change their own password
	if (('admin' != $user->role) AND ($user->UID != $_POST['UID'])) { $page->do403(); }

	$style = getPictureLoginStyle();
	$script = getPictureLoginScript();
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/picturelogin/actions/pictureloginchange.page.php');
	$kapenta->page->blockArgs['head'] = '<style>' . $style . '</style>' . $script;
	$kapenta->page->blockArgs['username'] = $user->username;
	$kapenta->page->blockArgs['UID'] = $_POST['UID'];
	$kapenta->page->render();

?>
