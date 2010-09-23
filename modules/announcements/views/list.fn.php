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
	global $kapenta, $db, $theme, $user;
	$num = 10;
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
	if (false == $user->authHas($refModule, $refModel, 'announcements-show', $refUID))
		{ return ''; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	load recent announcements from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";

	$range = $db->loadRange('Announcements_Announcement', '*', $conditions, 'createdOn DESC', $num);

	//	$sql = "select * from Announcements_Annoucement "
	//		 . "where refModule='" . $db->addMarkup($args['refModule']) . "' "
	//		 . "and refUID='" . $db->addMarkup($args['refUID']) . "' "
	//		 . "order by createdOn DESC limit " . $db->addMarkup($num) . "";

	$blockFile = 'modules/announcements/views/summary.block.php';

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == count($range)) { return "(no announcements at present)"; }

	foreach ($range as $row) {
		$model = new Announcements_Announcement();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $theme->loadBlock($blockFile));
	}  

	return $html;

}

//--------------------------------------------------------------------------------------------------

?>
