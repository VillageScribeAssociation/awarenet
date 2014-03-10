<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	users submenu
//--------------------------------------------------------------------------------------------------
//opt: userRa - recordAlias of a user [string]
//opt: userUID - UID of a user [string]

function users_menu($args) {
		global $user;
		global $theme;


	$labels = array('login' => '', 'signup' => '', 'profile' => '', 'grade' => '', 'blog' => '', 
					'list' => '', 'account' => '', 'pictures' => '', 'files' => '', 
					'friends' => ''	);

	if (array_key_exists('userUID', $args) == false) { $args['userUID'] = $user->UID; }
	if (array_key_exists('userRa', $args) == false) { $args['userRa'] = $user->alias;}

	//----------------------------------------------------------------------------------------------
	//	public vs user options
	//----------------------------------------------------------------------------------------------

	if ($user->role == 'public') {
		$labels['login'] = "[[:theme::submenu::label=Log In::link=/users/login/::alt=:]]\n";
		$labels['signup'] = "[[:theme::submenu::label=Sign Up::link=/users/signup/::alt=:]]\n";

	} else {

		$labels['profile'] = "[[:theme::submenu::label=Profile::link=/users/profile/%%userRa%%::alt=:]]\n";
		$labels['blog'] = "[[:theme::submenu::label=Blog::link=/moblog/blog/%%userRa%%::alt=:]]\n";
		$labels['grade'] = "[[:theme::submenu::label=Class::link=/users/grade/%%userRa%%::alt=:]]\n";
		$labels['friends'] = "[[:theme::submenu::label=Friends::link=/users/friends/%%userRa%%::alt=:]]\n";
		$labels['pictures'] = "[[:theme::submenu::label=Pictures::link=/gallery/list/%%userRa%%::alt=:]]\n";

		//--------------------------------------------------------------------------------------
		//	user is viewing own record
		//--------------------------------------------------------------------------------------

		if ($args['userUID'] == $user->UID) { 
			$labels['account'] = "[[:theme::submenu::label=My Account::link=/users/myaccount/::alt=:]]\n";
		}

	}

	//----------------------------------------------------------------------------------------------
	//	admin options
	//----------------------------------------------------------------------------------------------

	if ('admin' == $user->role) {
		$labels['list'] = "[[:theme::submenu::label=Profile::link=/users/list/::alt=:]]\n";
	}

	return $theme->replaceLabels($labels, $theme->loadBlock('modules/users/views/menu.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
