<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a forum
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $page->do404(); }
	$model = new Forums_Board($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('forums', 'forums_board', 'edit', $model->UID)) { $page->do403(); }
	//----------------------------------------------------------------------------------------------
	//	check permissions (must be site admin or forum moderator)
	//----------------------------------------------------------------------------------------------
	//$auth = false;
	//if ('admin' == $user->role) { $auth = true; }
	//foreach($model->moderators as $modUID) { if ($modUID == $user->UID) { $auth = true; } }
	// possibly more to come here...
	//if ($auth == false) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/forums/actions/edit.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->render();

?>
