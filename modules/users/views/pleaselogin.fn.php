<?

//--------------------------------------------------------------------------------------------------
//|	ask public user to log in
//--------------------------------------------------------------------------------------------------
//arg: school - UID of school [string]

function users_pleaselogin($args) {
	global $user;
	if ('public' == $user->role) {
		$msg = "<div class='inlinequote'>"
			 . "<img src='%%serverPath%%themes/%%defaultTheme%%/images/info.png' " 
			 . "class='infobutton' width='18' height='18' />"
			 . "&nbsp;&nbsp;"
			 . "Please <a href='%%serverPath%%users/login/'>log in</a> to view this section.</div>";
		return $msg;

	} else {
		// TODO: deal with banned users, etc
		return '';
	}
}

?>
