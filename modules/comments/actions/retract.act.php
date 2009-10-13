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
	//	blank the comment body
	//----------------------------------------------------------------------------------------------

	$model->data['comment'] = '<small>This comment has been retracted by the poster. '
							. mysql_datetime() . '</small>';
	$model->save();

	$_SESSION['sMessage'] .= "Your comment has been retracted.<br/>\n";

	//----------------------------------------------------------------------------------------------
	//	return to page comment was retected from, or user profile if none supplied
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('HTTP_REFERER', $_SERVER) == true) {
		// TODO, conside the security implications of this
		$referer = $_SERVER['HTTP_REFERER'];
		$referer = str_replace($serverPath, '', $referer);
		$referer = str_replace('//', '/', $referer);
		if (substr($referer, 0, 1) == '/') { $referer = substr($referer, 1); }
		do302($referer);

	} else {
		do302('users/profile/');
	}

?>
