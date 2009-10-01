<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	all images of a school (300px wide)
//--------------------------------------------------------------------------------------------------
// * $args['schoolUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or schools entry

function schools_allfaces($args) {
	global $serverPath;
	if (array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	
	$model = new School(sqlMarkup($args['raUID']));	
	$sql = "select * from images where refModule='schools' and refUID='" . $model->data['UID'] 
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