<?

//--------------------------------------------------------------------------------------------------
//|	ask public user to log in
//--------------------------------------------------------------------------------------------------
//arg: school - UID of school [string]

function users_pleaselogin($args) {
	global $user;
	global $kapenta;

	if ('public' == $user->role) {
		$blockUID = $kapenta->createUID();

		$msg = "<div class='inlinequote'>"
			 . "<img src='%%serverPath%%themes/%%defaultTheme%%/images/info.png' " 
			 . "class='infobutton' width='18' height='18' />"
			 . "<a name='$blockUID'></a>&nbsp;&nbsp;"
			 . "Please <a href='#$blockUID' onClick=\"kutils.show('divLogin$blockUID');\">"
			 . "log in</a> to view this section."
			 . "<div id='divLogin$blockUID' style='visibility: hidden; display: none;'>"
			 . "[[:users::loginform::redirectSelf=yes:]]"
			 . "</div>"
			 . "</div>";

		$block = "[%%delme%%[:users::loginform::return=" . $_SERVER['REQUEST_URI'] . ":]]";

		$msg = "<div class='inlinequote'>\n"
			 . "<img src='%%serverPath%%themes/%%defaultTheme%%/images/info.png' " 
			 . "class='infobutton' width='18' height='18' />\n"
			 . "<a name='$blockUID'></a>\n&nbsp;&nbsp;"
			 . "Please "
			 . "<a"
			 . " href='#$blockUID'"
			 . " onClick=\"kutils.loadBlock('divLogin$blockUID', '$block', true);\""
			 . ">"
			 . "log in</a> to view this section.\n"
			 . "<div id='divLogin$blockUID'></div>\n"
			 . "</div>\n";

		return $msg;

	} else {
		// TODO: deal with banned users, etc
		return '';
	}
}

?>
