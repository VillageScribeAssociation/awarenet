<?

//--------------------------------------------------------------------------------------------------
//	delete a user record
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }

	$model = new Users($request['ref']);
	$_SESSION['sMessage'] .= "Deleted user: " . $model->getName() . "<br/>";
	$model->delete();

	do302('users/');

?>
