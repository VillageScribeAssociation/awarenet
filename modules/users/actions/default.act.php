<?

//--------------------------------------------------------------------------------------------------
//*	default action for the users module (list users, or show profile if alias is given)
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { 
		include $kapenta->installPath . 'modules/users/actions/list.act.php'; 
	} else {
		include $kapenta->installPath . 'modules/users/actions/profile.act.php'; 
	}
?>
