<?

//--------------------------------------------------------------------------------------------------
//*	default action for chatserver is to show the management console to admins, or info 
//--------------------------------------------------------------------------------------------------

	if ('admin' == $user->role) {
		include $kapenta->installPath . 'modules/chatserver/actions/console.act.php';
	} else {
		include $kapenta->installPath . 'modules/chatserver/actions/info.act.php';
	}

?>
