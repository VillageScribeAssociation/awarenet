<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x announcments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refModule - module which owns the record [string]
//arg: refModel - record which owns the announcements [string]
//arg: refUID - record which owns the announcements [string]
//opt: num - number of records per page (default is 10) [string]

function announcements_list($args) {
	global $kapenta;
	global $db;
	global $theme;
	global $user;

	$pageNo = 1;
	$num = 10;
	$start = 0;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no owner)'; }

	if (false == $user->authHas($refModule, $refModel, 'announcements-show', $refUID)) {
		return '';
	}

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }

	if ($pageNo < 1) { $pageNo = 1; }

	//----------------------------------------------------------------------------------------------
	//	count announcements belonging to this item
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";

	$totalItems = $db->countRange('announcements_announcement', $conditions);

	if (0 == $totalItems) {
		return ''
		 . "<div class='outline' style='color: #bbbbbb;'>"
		 . "<small>No announcements at present.</small>"
		 . "</div>\n"
		 . "<!-- end of results -->";
	}

	//----------------------------------------------------------------------------------------------
	//	load recent announcements from the database
	//----------------------------------------------------------------------------------------------

	$start = ($pageNo - 1) * $num;

	$range = $db->loadRange(
		'announcements_announcement', '*', $conditions, 'createdOn DESC', $num, $start
	);

	//	$sql = "select * from Announcements_Annoucement "
	//		 . "where refModule='" . $db->addMarkup($args['refModule']) . "' "
	//		 . "and refUID='" . $db->addMarkup($args['refUID']) . "' "
	//		 . "order by createdOn DESC limit " . $db->addMarkup($num) . "";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$blockFile = 'modules/announcements/views/summary.block.php';

	foreach ($range as $row) {
		$html .= "[[:announcements::summary::UID=" . $row['UID'] . ":]]\n";

		//NOTE: the following was removd for brevity, consider re-adding for efficiency sake
		//$model = new Announcements_Announcement();
		//$model->loadArray($row);
		//$html .= $theme->replaceLabels($model->extArray(), $theme->loadBlock($blockFile));
	}  

	if ($totalItems <= ($start + $num)) {
		$html .= "<!-- end of results -->\n";
	}

	return $html;

}

//--------------------------------------------------------------------------------------------------

?>
