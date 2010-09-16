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

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (true == array_key_exists('page', $args)) { $pageNo = $args['page']; }
	if (true == array_key_exists('num', $args)) { $num = $args['num']; }
	if (true == array_key_exists('owner', $args)) { $owner = $args['owner']; }
	if (true == array_key_exists('folder', $args)) { $folder = $args['folder']; }
	//TODO: permissions check here 

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

	$range = $db->loadRange('Messages_Message', '*', $conditions, 'createdOn DESC', $num, $start);

	//	$sql = "select * from messages "
	//		 . "where owner='" . $owner . "' and folder='" . $folder . "' "
	//		 . "order by createdOn DESC " . $limit;	

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

	if (0 == $totalItems) { $html = "(you have no new messages)"; }

	$link = '%%serverPath%%messages/inbox/';

	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageNo) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	$html = $pagination . $html . $pagination;
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
