<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all users
//--------------------------------------------------------------------------------------------------
//opt: pageNo - page no to display, from 1, default is 1 (int) [string]
//opt: num - number of records per page (default is 300) [string]

function users_summarylist($args) {
		global $kapenta;
		global $req;
		global $theme;
		global $user;
		global $kapenta;


    global $kapenta;

	$num = 300;							//%	default number of items per page [int]
	$start = 0;							//%	position in table [int]
	$pageNo = 1;						//% starts at 1 [int]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'users_user', 'list')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }

	if ($num < 1) { $num = 1; }
	if ($pageNo < 1) { $num = 1; }

	//----------------------------------------------------------------------------------------------
	//	count users
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "role != 'public'";			// we don't show public users
	$conditions[] = "role != 'banned'";			// we don't show banned users (for now)
	$conditions[] = "role != 'inactive'";		// we don't show inactive users (for now)

	$totalItems = $kapenta->db->countRange('users_user', $conditions);
	$totalPages = ceil($totalItems / $num);
	$start = ($pageNo - 1) * $num;

	if ($pageNo > $totalPages) { $pageNo = $totalPages; }

	//----------------------------------------------------------------------------------------------
	//	load a page of results from the database and make a list
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('users_user', '*', $conditions, 'username', $num, $start);
	$block = $theme->loadBlock('modules/users/views/summary.block.php');

	foreach($range as $row) {
		$model = new Users_User();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}  

	$link = '%%serverPath%%users/list/';
	$pagination = "[[:theme::pagination::page=" . $kapenta->db->addMarkup($pageNo) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	$html = $pagination . $html . $pagination;

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
