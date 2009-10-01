<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all users
//--------------------------------------------------------------------------------------------------
// * $args['page'] = page no to display
// * $args['num'] = number of records per page

function users_summarylist($args) {
	if (authHas('users', 'summarylist', '') == false) { return ''; }
	global $request;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	arguments
	//----------------------------------------------------------------------------------------------
	$start = 0; $num = 300; $page = 1;

	if (array_key_exists('num', $args)) { $num = $args['num']; }
	if (array_key_exists('page', $request['args'])) { 
		$page = $request['args']['page']; 
		$start = ($page - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$list = dbLoadRange('users', '*', '', 'username', $num, $start);
	foreach($list as $UID => $row) {
		$html .= "[[:users::summary::UID=" . $UID . ":]]";
	}  // TODO: process blocks directly, more effient, fewer queries

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>