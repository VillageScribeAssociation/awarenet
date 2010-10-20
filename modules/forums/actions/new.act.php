<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new forum (by default, bound to the admin's school)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check that user is authorized to create new forums
	//----------------------------------------------------------------------------------------------
	//if (false == $user->authHas('forums', 'Forums_Board', 'new')) { $page->do403(); }
	if ('admin' != $user->role) { $page->do403('Only administrators can create new boards.'); }

	//----------------------------------------------------------------------------------------------
	//	make a new board and redirect to edit form (TODO: use stanard generated code here)
	//----------------------------------------------------------------------------------------------
	$model = new Forums_Board();
	$model->UID = $kapenta->createUID();
	$model->school = $user->school;
	$model->save();

	$page->do302('forums/edit/' . $model->alias);

?>
