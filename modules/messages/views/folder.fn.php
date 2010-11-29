<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a user's mail folder
//--------------------------------------------------------------------------------------------------
//arg: owner - whose folder? (default is current user) [string]
//arg: folder - folder to display [string]
//opt: page - page to display (default is 1) [string]
//opt: num - number of messages to show (default is 50) [string]

function messages_folder($args) {
	global $db, $page, $theme, $user;

	$pageNo = 1;
	$num = 50;
	$size = 'thumb';
	$html = '';
	$owner = $user->UID;
	$folder = 'inbox';
	$orderBy = 'createdOn DESC';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (true == array_key_exists('page', $args)) { $pageNo = $args['page']; }
	if (true == array_key_exists('num', $args)) { $num = $args['num']; }
	if (true == array_key_exists('owner', $args)) { $owner = $args['owner']; }
	if (true == array_key_exists('folder', $args)) { $folder = $args['folder']; }
	//TODO: permissions check here 

	// prevent HTML/SQL injection
	if (('inbox' != $folder) && ('outbox' != $folder)) { return '(unknown folder)'; }

	if (true == array_key_exists('orderBy', $args)) { 
		switch($args['orderBy']) {
			case 'title':		$orderBy = 'title ASC';			break;
			case 'fromName':	$orderBy = 'fromName ASC';		break;
			case 'toName':		$orderBy = 'toName ASC';		break;
			case 'createdOn':	$orderBy = 'createdOn DESC';	break;
			case 'status':		$orderBy = 'status DESC';		break;
		}
	}


	//----------------------------------------------------------------------------------------------
	//	count total records owned by this module
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "owner='" . $db->addMarkup($owner) . "'";
	$conditions[] = "folder='" . $db->addMarkup($folder) . "'";

	$totalItems = $db->countRange('Messages_Message', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	load page of messages
	//----------------------------------------------------------------------------------------------
	$start = (($pageNo - 1) * $num);
	$range = $db->loadRange('Messages_Message', '*', $conditions, $orderBy, $num, $start);

	//	$sql = "select * from messages "
	//		 . "where owner='" . $owner . "' and folder='" . $folder . "' "
	//		 . "order by createdOn DESC " . $limit;	

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	/*
	// deprecated, see http://code.google.com/p/awarenet/issues/detail?id=152
	$block = $theme->loadBlock('modules/messages/views/inboxrow.block.php');
	
	$html .= "<table noborder width='100%'>\n";
	$html .= "<tr><td class='title'>From</td><td class='title'>To</td><td class='title'>Subject</td></tr>";

	foreach ($range as $row) {
		$thisMsg = new Messages_Message();
		$thisMsg->loadArray($row);
		$ext = $thisMsg->extArray();
		if ('outbox' == $ext['folder']) { $ext['status'] = 'sent'; }
		$html .= $theme->replaceLabels($ext, $block);
	}
	$html .= "</table>\n";
	*/

	$arrowDown = "<img src='%%serverPath%%modules/messages/assets/arrowdown.png' border='0' />";
	$folderLink = '%%serverPath%%messages/' . $folder . '/orderBy_';

	$sortFromName = "<a href='" . $folderLink . "fromName'>$arrowDown</a>";
	$sortTitle = "<a href='" . $folderLink . "title'>$arrowDown</a>";
	$sortCreatedOn = "<a href='" . $folderLink . "createdOn'>$arrowDown</a>";
	$sortStatus = "<a href='" . $folderLink . "status'>$arrowDown</a>";
	$sortToName = "<a href='" . $folderLink . "toName'>$arrowDown</a>";

	$fromLabel = 'From';
	if ('outbox' == $folder) { $fromLabel = 'To'; $sortFromName = $sortToName; }

	$table = array();
	$table[] = array(
		$sortStatus . '&nbsp;', 
		$fromLabel . '&nbsp;' . $sortFromName, 
		'Subject' . '&nbsp;' . $sortTitle, 
		'Date' . '&nbsp;' . $sortCreatedOn
	);
	foreach($range as $row) {
		$dotImg = 'modules/messages/assets/graydot.png';
		if ('unread' == $row['status']) { $dotImg = 'modules/messages/assets/greendot.png'; }
		$status = "<img src='%%serverPath%%" . $dotImg . "' />";

		$fromUrl = '%%serverPath%%users/profile/' . $row['fromUID'];
		$fromLink = "<a href='$fromUrl'>" . $row['fromName'] . "</a>";

		if ('outbox' == $folder) {
			$fromUrl = '%%serverPath%%users/profile/' . $row['toUID'];
			$fromLink = "<a href='$fromUrl'>" . $row['toName'] . "</a>";
		}

		$msgUrl = '%%serverPath%%messages/' . $row['UID'];
		$msgLink = "<a href='$msgUrl'>" . $row['title'] . "</a>";

		$table[] = array($status, $fromLink, $msgLink, '<small>' . $row['createdOn'] . '</small>');
	}

	$listing = $theme->arrayToHtmlTable($table, true, true);

	if (0 == $totalItems) { $html = "(you have no new messages)"; }

	$link = '%%serverPath%%messages/inbox/';

	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageNo) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	$html = $pagination . $listing . $pagination;
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
