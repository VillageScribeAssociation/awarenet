<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Forums_Board object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $req->args)) { $page->do404('UID not given.'); }

	$model = new Forums_Board($req->args['UID']);
	if (false == $model->loaded) { $page->do404('No such forum.'); }

	if (false == $user->authHas('forums', 'Forums_Board', 'delete', $model->UID))
		{ $page->do403('You cannot delete this forum (insufficient privilege).'); }
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation form
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/forums/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');

	//----------------------------------------------------------------------------------------------
	//	show confirmation for above item to be deleted
	//----------------------------------------------------------------------------------------------
	$page->do302('forums/' . $model->alias);

?>
