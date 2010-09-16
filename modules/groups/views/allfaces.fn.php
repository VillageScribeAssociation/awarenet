<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	all images of a group (300px wide)
//--------------------------------------------------------------------------------------------------
// * $args['groupUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or groups entry

function groups_allfaces($args) {
	global $db;

	global $serverPath;
	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	
	$model = new Groups_Group($db->addMarkup($args['raUID']));	
	$sql = "select * from images where refModule='groups' and refUID='" . $model->UID 
	     . "' order by weight";
	
	$html = '';
	
	$result = $db->query($sql);
	if ($db->numRows($result) > 0) {
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$html .= "[[:theme::navtitlebox::label=Face:]]";
			$html .= "<a href='/images/show/" . $row['alias'] . "'>" 
				. "<img src='/images/width300/" . $row['alias'] 
				. "' border='0' alt='" . $model->name . "'></a>";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>