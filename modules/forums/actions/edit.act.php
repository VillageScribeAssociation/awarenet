<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a forum
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->do404(); }
	$model = new Forums_Board($req->ref);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('forums', 'Forums_Board', 'edit', $model->UID)) { $page->do403(); }
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
	$page->load('modules/forums/actions/edit.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->alias;
	$page->render();

?>
