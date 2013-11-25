<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x announcments owned by a particular record on a given module in the nav
//--------------------------------------------------------------------------------------------------
//opt: refModule - kapenta module to which announcements appply [string]
//opt: refModel - type of object which may own announcments [string]
//opt: refUID - record which owns the announcements [string]
//opt: num - number of records per page (default is 10) [string]

function announcements_listnav($args) {
	global $db, $theme, $user, $page;

	$refModule = '';				//%	kapenta module [string]
	$refModel = '';					//%	type of object which may own announcements [string]
	$refUID = '';					//%	UID of single object which may own announcements [string]
	$num = 10;						//%	number of items per page [int]
	$html = '';						//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check aruments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show'))
		{ return ''; }

	if (true == array_key_exists('refModule', $args)) { $refModule = $args['refModule']; }
	if (true == array_key_exists('refModel', $args)) { $refModel = $args['refModel']; }
	if (true == array_key_exists('refUID', $args)) { $refUID = $args['refUID']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	if ('' != $refUID) { $conditions[] = "refUID='" . $db->addMarkup($refUID) . "'"; }
	if ('' != $refModel) { $conditions[] = "refModel='" . $db->addMarkup($refModel) . "'"; }
	if ('' != $refModule) { $conditions[] = "refModule='" . $db->addMarkup($refModule) . "'"; }

	$range = $db->loadRange('announcements_announcement', '*', $conditions, 'createdOn DESC', $num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/announcements/views/summarynav.block.php');
	if (0 == count($range)) { $html .= "(no announcements at present)"; }

	foreach ($range as $row) {
		$model = new Announcements_Announcement($row['UID']);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
