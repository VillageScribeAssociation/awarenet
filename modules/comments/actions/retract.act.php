<?

//--------------------------------------------------------------------------------------------------
//	retract a comment (users can retract their own comments, admins can just blast away)
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/comments/models/comments.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check reference, permissions
	//----------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	if (dbRecordExists('comments', $request['ref']) == false) { do404(); }

	$model = new Comment($request['ref']);
	$authorised = false;

	if ($model->data['createdBy'] == $user->data['UID']) { $authorised = true; }
	if (authHas('comments', 'retractall', '') == true) { $authorised = true; }

	if ($authorised == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete the comment
	//----------------------------------------------------------------------------------------------

	$model->delete();
	$_SESSION['sMessage'] .= "Your comment have been retracted.<br/>\n";

	do302('users/profile/')

?>
