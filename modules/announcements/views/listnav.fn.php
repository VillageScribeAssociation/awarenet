<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x announcments owned by a particular record on a given module in the nav
//--------------------------------------------------------------------------------------------------
//arg: refUID - record which owns the announcements [string]
//arg: refModule - module which owns the record [string]
//opt: num - number of records per page (default is 10) [string]

function announcements_listnav($args) {
	global $db, $theme, $user;
	$num = 10;						//%	number of items per page [int]
	$html = '';						//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check aruments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('announcements', 'Announcements_Announcement', 'show'))
		{ return ''; }

	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (false == array_key_exists('refUID', $args)) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";

	$range = $db->loadRange('Announcements_Announcement', '*', $conditions, 'createdOn DESC', $num);
	$block = $theme->loadBlock('modules/announcements/views/summarynav.block.php');

	//$sql = "select * from Announcements_Announcement "
	//	 . "where refModule='" . $db->addMarkup($args['refModule']) . "' "
	//	 . "and refUID='" . $db->addMarkup($args['refUID']) . "' "
	//	 . "order by createdOn DESC limit " . $db->addMarkup($num) . "";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (count($range) > 0) {
		foreach ($range as $row) {
			$model = new Announcements_Announcement();
			$model->loadArray($row);
			$html .= $theme->replaceLabels($model->extArray(), $block);
		}  
	} else { $html .= "(no announcements at present)"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
