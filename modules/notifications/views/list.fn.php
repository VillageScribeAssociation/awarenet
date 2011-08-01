<?

//--------------------------------------------------------------------------------------------------
//*	list a user's recent activity
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a Users_User object [string]
//opt: page - page number to display, default is 1 (int) [string]
//opt: num - number of records per page (default is 30) [string]
//opt: pagination - show pagination bar, default is 'yes' (yes|no) [string]

function notifications_list($args) {
	global $page, $db, $user, $theme;
	$start = 0;					//%	row position in table at which page starts [int]
	$num = 30;					//%	number of items per page [int]
	$pageNo = 1;				//%	current results page (from 1) [int]
	$pagination = 'yes';		//% display pagination bar (yes|no) [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return '(user not specified)'; }

	$userUID = $args['userUID'];
	if (false == $db->objectExists('users_user', $userUID)) { return '(no such user)'; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }
	if (true == array_key_exists('page', $args)) 
		{ $pageNo = $args['page']; $start = ($pageNo - 1) * $num; }

	//----------------------------------------------------------------------------------------------
	//	count visible notifications
	//----------------------------------------------------------------------------------------------
	//TODO: tidy so as not to count results when pagination disabled
	$conditions = array();
	$conditions[] = "userUID='" . $db->addMarkup($userUID) . "'";
	$conditions[] = "status='show'";

	$totalItems = $db->countRange('notifications_userindex', $conditions);
	$totalPages = ceil($totalItems / $num);

	if (0 == $totalItems) { 
		$block = $theme->loadBlock('modules/notifications/views/nonotifications.block.php');
		return $block;
	}

	$link = '%%serverPath%%notifications/';
	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageNo) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	//----------------------------------------------------------------------------------------------
	//	load a page worth of notifications from the database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('notifications_userindex', '*', $conditions, 'createdOn DESC', $num, $start);
	$block = $theme->loadBlock('modules/notifications/views/show.block.php');

	foreach($range as $UID => $row) { 
		$model = new Notifications_Notification($row['notificationUID']);
		$labels = $model->extArray();
		$labels['userIndexUID'] = $row['UID'];
		$html .= $theme->replaceLabels($labels, $block);
		//$html .= "[[:notifications::show::UID=" . $row['notificationUID'] . ":]]"; 
	}

	if ('yes' == $pagination) {	$html = $pagination . $html . $pagination; }	

	return $html;

}


?>
