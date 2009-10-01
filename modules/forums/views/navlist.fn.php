<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all forums for the nav 
//--------------------------------------------------------------------------------------------------
// * $args['school'] = school to show forums for

function forums_navlist($args) {
	if (array_key_exists('school', $args) == false) { return false; }
	$html = '';

	$sql = "select * from forums "
		 . "where school='" . sqlMarkup($args['school']) . "' "
		 . "order by weight"; 

	$block = loadBlock('modules/forums/views/summarynav.block.php');

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);

			$model = new forums();
			$model->loadArray($row);
			$labels = $model->extArray();
			$labels['forumsUID'] = $row['UID'];

			$html .= replaceLabels($labels, $block);
		}
	} else { $html = "(no forums)<br/>\n"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>