<?

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	all images of a group (300px wide)
//--------------------------------------------------------------------------------------------------
// * $args['groupUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or groups entry

function groups_allfaces($args) {
	global $serverPath;
	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	
	$model = new Group(sqlMarkup($args['raUID']));	
	$sql = "select * from images where refModule='groups' and refUID='" . $model->data['UID'] 
	     . "' order by weight";
	
	$html = '';
	
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$html .= "[[:theme::navtitlebox::label=Face:]]";
			$html .= "<a href='/images/show/" . $row['recordAlias'] . "'>" 
				. "<img src='/images/width300/" . $row['recordAlias'] 
				. "' border='0' alt='" . $model->data['name'] . "'></a>";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>