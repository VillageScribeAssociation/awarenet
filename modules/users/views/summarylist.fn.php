<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all users
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 1) [string]
//opt: num - number of records per page (default is 300) [string]

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
