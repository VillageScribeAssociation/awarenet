<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Forums_Board object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permssions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); } 
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Board not specified (UID).'); }
	  
	$model = new Forums_Board($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Unknown board.'); }
	if (false == $kapenta->user->authHas('forums', 'forums_board', 'edit', $model->UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete the board
	//----------------------------------------------------------------------------------------------
	$kapenta->session->msg("Deleted forum: " . $model->title, 'ok');
	$model->delete();
	$kapenta->page->do302('forums/');

?>
