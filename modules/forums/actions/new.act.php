<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new board to the forums (by default, bound to the user's school)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check that user is authorized to create new forums
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('forums', 'forums_board', 'new')) { $kapenta->page->do403(); }
	//if ('admin' != $user->role) { $kapenta->page->do403('Only administrators can create new boards.'); }

	//----------------------------------------------------------------------------------------------
	//	make a new board and redirect to edit form (TODO: use stanard generated code here)
	//----------------------------------------------------------------------------------------------
	$model = new Forums_Board();
	$model->UID = $kapenta->createUID();
	$model->school = $user->school;
	$model->save();

	$kapenta->page->do302('forums/edit/' . $model->alias);

?>
