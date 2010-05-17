<?

	require_once($installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a user's mail folder
//--------------------------------------------------------------------------------------------------
//arg: owner - whose folder? (default is current user) [string]
//arg: folder - folder to display [string]
//opt: page - page to display (default is 1) [string]
//opt: num - number of messages to show (default is 50) [string]

function messages_folder($args) {
	global $user;
	$page = 1; $num = 50; $size = 'thumb'; $html = '';
	$owner = $user->data['UID']; $folder = 'inbox';
	if ('public' == $user->data['ofGroup']) { return false; }
	if (array_key_exists('page', $args) == true) { $page = $args['page']; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	if (array_key_exists('owner', $args) == true) { $owner = sqlMarkup($args['owner']); }
	if (array_key_exists('folder', $args) == true) { $folder = sqlMarkup($args['folder']); }

	//----------------------------------------------------------------------------------------------
	//	count total records owned by this module
	//----------------------------------------------------------------------------------------------
	$userUID = $user->data['UID'];
	$sql = "select count(UID) as numRecords from messages "
		 . "where owner='" . $owner . "' and folder='" . $folder . "' "
		 . "order by createdOn DESC";	

	$result = dbQuery($sql);
	$row = sqlRMArray(dbFetchAssoc($result));
	$total = ceil($row['numRecords'] / $num);

	//----------------------------------------------------------------------------------------------
	//	load page of messages
	//----------------------------------------------------------------------------------------------
	$limit = "limit " . (($page - 1) * $num) . ", ". sqlMarkup($num);
	$sql = "select * from messages "
		 . "where owner='" . $owner . "' and folder='" . $folder . "' "
		 . "order by createdOn DESC " . $limit;	

	$result = dbQuery($sql);
	$block = loadBlock('modules/messages/views/inboxrow.block.php');
	
	$html .= "<table noborder width='100%'>\n";
	$html .= "<tr><td class='title'>From</td><td class='title'>To</td><td class='title'>Subject</td></tr>";
	while ($row = dbFetchAssoc($result)) {
		$thisMsg = new Message();
		$thisMsg->loadArray(sqlRMArray($row));
		$ext = $thisMsg->extArray();
		if ('outbox' == $ext['folder']) { $ext['status'] = 'sent'; }
		$html .= replaceLabels($ext, $block);
	}
	$html .= "</table>\n";

	if (0 == $total) { $html = "(you have no new messages)"; }

	$link = '%%serverPath%%messages/inbox/';

	$pagination .= "[[:theme::pagination::page=" . sqlMarkup($page) 
				. "::total=" . $total . "::link=" . $link . ":]]\n";

	return $pagination . $html . $pagination;
}


//--------------------------------------------------------------------------------------------------

?>

