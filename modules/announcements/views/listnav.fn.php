<?

	require_once($installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x announcments owned by a particular record on a given module in the nav
//--------------------------------------------------------------------------------------------------
//arg: refUID - record which owns the announcements [string]
//arg: refModule - module which owns the record [string]
//opt: num - number of records per page (default is 10) [string]

function announcements_listnav($args) {
	if (authHas('announcements', 'show', '') == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }

	$num = 10;
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = "select * from announcements "
		 . "where refModule='" . sqlMarkup($args['refModule']) . "' "
		 . "and refUID='" . sqlMarkup($args['refUID']) . "' "
		 . "order by createdOn DESC limit " . sqlMarkup($num) . "";

	$blockFile = 'modules/announcements/views/summarynav.block.php';

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$model = new Announcement();
			$model->loadArray($row);
			$html .= replaceLabels($model->extArray(), loadBlock($blockFile));
		}  
	} else {
		$html .= "(no announcements at present)";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

