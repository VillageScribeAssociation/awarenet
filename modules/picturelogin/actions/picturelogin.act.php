<?
	require_once($kapenta->installPath . 'modules/picturelogin/inc/picturelogin.php');

//--------------------------------------------------------------------------------------------------
//*	show icons, drag & drop into iconpassword fields (one per letter), generate password field, password field (user copies text) and login button 
//--------------------------------------------------------------------------------------------------

	if ('' != $kapenta->request->ref) { $page->do404(); }							// check ref

	$style = getPictureLoginStyle();
	$script = getPictureLoginScript();

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/picturelogin/actions/picturelogin.page.php');
	$kapenta->page->blockArgs['head'] = '<style>' . $style . '</style>' . $script;
	$kapenta->page->render();

?>
