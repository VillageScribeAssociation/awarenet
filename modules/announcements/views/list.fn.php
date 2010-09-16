<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x announcments owned by a particular record on a given module
//--------------------------------------------------------------------------------------------------
//arg: refUID - record which owns the announcements [string]
//arg: refModule - module which owns the record [string]
//opt: num - number of records per page (default is 10) [string]

function announcements_list($args) {
	global $db, $theme, $user;
	$num = 10;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (false == array_key_exists('refUID', $args)) { return ''; }
	if (false == $user->authHas('announcements', 'Announcements_Announcement', 'list')) { return ''; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	//TODO: $db->loadRange
	$sql = "select * from Announcements_Annoucement "
		 . "where refModule='" . $db->addMarkup($args['refModule']) . "' "
		 . "and refUID='" . $db->addMarkup($args['refUID']) . "' "
		 . "order by createdOn DESC limit " . $db->addMarkup($num) . "";

	$blockFile = 'modules/announcements/views/summary.block.php';

	$result = $db->query($sql);
	if ($db->numRows($result) > 0) {
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$model = new Announcements_Announcement();
			$model->loadArray($row);
			$html .= $theme->replaceLabels($model->extArray(), $theme->loadBlock($blockFile));
		}  
	} else {
		$html .= "(no announcements at present)";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
