<?

//--------------------------------------------------------------------------------------------------
//*	list a user's recent activity
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a Users_User object [string]
//opt: page - page number to display, default is 1 (int) [string]
//opt: num - number of records per page (default is 30) [string]
//opt: pagination - show pagination bar, default is 'yes' (yes|no) [string]

function notifications_list($args) {
	global $page;
	global $db;
	global $user;
	global $theme;
	global $session;

	$start = 0;					//%	row position in table at which page starts [int]
	$num = 30;					//%	number of items per page [int]
	$pageNo = 1;				//%	current results page (from 1) [int]
	$pagination = 'yes';		//% display pagination bar (yes|no) [string]
	$by = 'createdOn DESC';		//%	list order [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return '(user not specified)'; }

	$userUID = $args['userUID'];
	
	if (('teachers' == $userUID) || ('everyone' == $userUID)) {
		// this is an aggregate feed, TODO: disable hiding of notifications
	} else {
		// this is a single user's feed
		if (false == $db->objectExists('users_user', $userUID)) { return '(no such user)'; }
		if (($userUID != $user->UID) && (('admin' != $user->role) and ('teacher' != $user->role))) { 
			return "<div class='inlinequote'>Your session has ezxpired, please log in.</div>"; 
		}
	}

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }

	if (true == array_key_exists('page', $args)) {
		$pageNo = $args['page'];
		$start = ($pageNo - 1) * $num;
	}

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
	$range = $db->loadRange('notifications_userindex', '*', $conditions, $by, $num, $start);
	$block = $theme->loadBlock('modules/notifications/views/show.block.php');

	if (0 == count($range)) { $html .= "<!-- end of results -->"; }

	foreach($range as $UID => $row) { 
		$model = new Notifications_Notification($row['notificationUID']);
		if (
			(true == $model->loaded) &&										//	notification exists
			(true == $db->objectExists($model->refModel, $model->refUID))	//	subject exists
		) {
			$labels = $model->extArray();

			$labels['userIndexUID'] = $row['UID'];
			$labels['hideJsLink'] = ''
			 	. "<a href='javascript:void(0);' "
					. "onClick=\"notifications_hide('" . $row['UID'] . "');\""
					. ">[hide]</a>";

			$html .= $theme->replaceLabels($labels, $block);

			//	temporary fix, remove when data is migrated to new image block arguments
			//	strix 2012-06-27

			$html = str_replace(
				'[[:images::width100::',
				'[[:images::width100::display=inline::',
				$html
			);

			$html = str_replace(
				'::size=width100::',
				'::size=width100::display=inline::',
				$html
			);


			//$html .= "[[:notifications::show::UID=" . $row['notificationUID'] . ":]]"; 

		} else {
			//TODO: add a notice to the feed explaining missing notifications.
		}
	}

	if ('yes' == $pagination) {	$html = $pagination . $html . $pagination; }	

	if (true == $session->get('mobile')) {
		$html = $theme->expandBlocks($html, 'mobileindent');
	} else {
		$html = $theme->expandBlocks($html, 'indent');
	}

	// fix image sizes
	$html = str_replace('widthcontent', 'widthindent', $html);
	$html = str_replace('widtheditor', 'widthindent', $html);
	$html = str_replace('width570', 'widthindent', $html);
	$html = str_replace('s_slide', 's_slideindent', $html);

	return $html;

}

?>
