<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	all images of a school (300px wide)		//TODO: move to images module or discard
//--------------------------------------------------------------------------------------------------
//arg: raUID  - recordAlias or UID or schools entry [string]
//opt: schoolUID - overrides raUID [string]

function schools_allfaces($args) {
		global $kapenta;
		global $kapenta;

	$html = '';

	if (true == array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	
	$model = new Schools_School($kapenta->db->addMarkup($args['raUID']));
	if (false == $model->loaded) { return ''; }

	$sql = "select * from images_image where refModule='schools' and refUID='" . $model->UID 
	     . "' order by weight";
	
	//TODO: $db->loadRange
	
	$result = $kapenta->db->query($sql);
	if ($kapenta->db->numRows($result) > 0) {
		while ($row = $kapenta->db->fetchAssoc($result)) {
			$row = $kapenta->db->rmArray($row);
			$html .= "[[:theme::navtitlebox::label=Face:]]";
			$html .= "<a href='%%serverPath%%images/show/" . $row['alias'] . "'>" 
				. "<img src='%%serverPath%%images/width300/" . $row['alias'] 
				. "' border='0' alt='" . $model->name . "'></a>";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
