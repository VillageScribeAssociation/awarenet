<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Forums_Board object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permssions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); } 
	if ('deleteRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Board not specified (UID).'); }
	  
	$model = new Forums_Board($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Unknown board.'); }
	if (false == $user->authHas('forums', 'Forums_Board', 'edit', $model->UID)) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete the board
	//----------------------------------------------------------------------------------------------
	$session->msg("Deleted forum: " . $model->title, 'ok');
	$model->delete();
	$page->do302('forums/');

?>
