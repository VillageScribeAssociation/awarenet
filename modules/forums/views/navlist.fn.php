<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|+	list all forums for the nav //TODO: fix this up
//--------------------------------------------------------------------------------------------------
//arg: school - UID of school to show forums for [string]

function forums_navlist($args) {
		global $kapenta;
		global $kapenta;
		global $theme;


	if (false == array_key_exists('school', $args)) { return ''; }
	$html = '';

	$sql = "select * from forums_board "
		 . "where school='" . $kapenta->db->addMarkup($args['school']) . "' "
		 . "order by weight"; 

	$block = $theme->loadBlock('modules/forums/views/summarynav.block.php');

	$result = $kapenta->db->query($sql);
	if ($kapenta->db->numRows($result) > 0) {
		while ($row = $kapenta->db->fetchAssoc($result)) {
			$row = $kapenta->db->rmArray($row);

			$model = new forums();
			$model->loadArray($row);
			$labels = $model->extArray();
			$labels['forumsUID'] = $row['UID'];

			$html .= $theme->replaceLabels($labels, $block);
		}
	} else { $html = "(no forums)<br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
