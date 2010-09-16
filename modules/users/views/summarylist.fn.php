<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all users
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 1) [string]
//opt: num - number of records per page (default is 300) [string]

function users_summarylist($args) {
	global $db, $req, $page, $user;
	$num = 300;							//%	default number of items per page [int]
	$start = 0;							//%	position in table [int]
	$pageNo = 1;						//% starts at 1 [int]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'Users_User', 'list')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('page', $req->args)) { 
		$pageNo = (int)$req->args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$list = $db->loadRange('Users_User', '*', '', 'username', $num, $start);
	foreach($list as $UID => $row) {
		$html .= "[[:users::summary::UID=" . $UID . ":]]";
	}  // TODO: process blocks directly, more effient, fewer queries

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
