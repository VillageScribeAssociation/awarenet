<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	submenu
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = UID of a user
// * $args['userRef'] = recordAlias of a user

function users_menu($args) {
	global $user;

	$labels = array('login' => '', 'signup' => '', 'profile' => '', 'grade' => '', 'blog' => '', 
					'list' => '', 'account' => '', 'pictures' => '', 'files' => '', 
					'friends' => ''	);

	if (array_key_exists('userUID', $args) == false) { $args['userUID'] = $user->data['UID']; }
	if (array_key_exists('userRa', $args) == false) { $args['userRa'] = $user->data['recordAlias'];}

	//----------------------------------------------------------------------------------------------
	//	public vs user options
	//----------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] == 'public') {
		$labels['login'] = "[[:theme::submenu::label=Log In::link=/users/login/::alt=:]]\n";
		$labels['signup'] = "[[:theme::submenu::label=Sign Up::link=/users/signup/::alt=:]]\n";

	} else {

		$labels['profile'] = "[[:theme::submenu::label=Profile::link=/users/profile/%%userRa%%::alt=:]]\n";
		$labels['blog'] = "[[:theme::submenu::label=Blog::link=/moblog/blog/%%userRa%%::alt=:]]\n";
		$labels['grade'] = "[[:theme::submenu::label=Classmates::link=/schools/grade/user_%%userRa%%::alt=:]]\n";
		$labels['friends'] = "[[:theme::submenu::label=Friends::link=/users/friends/%%userRa%%::alt=:]]\n";
		$labels['pictures'] = "[[:theme::submenu::label=Pictures::link=/gallery/list/%%userRa%%::alt=:]]\n";

		//--------------------------------------------------------------------------------------
		//	user is viewing own record
		//--------------------------------------------------------------------------------------

		if ($args['userUID'] == $user->data['UID']) { 
			$labels['account'] = "[[:theme::submenu::label=My Account::link=/users/myaccount/::alt=:]]\n";
		}

	}

	//----------------------------------------------------------------------------------------------
	//	admin options
	//----------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] == 'admin') {
		$labels['list'] = "[[:theme::submenu::label=Profile::link=/users/list/::alt=:]]\n";
	}

	return replaceLabels($labels, loadBlock('modules/users/views/menu.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>