<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	all images of a project (default 300px wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: projectUID - overrides raUID [string]
//opt: size - size to display images (default is width300) [string]

function projects_allimages($args) {
		global $kapenta;
		global $kapenta;
		global $user;
		global $theme;

	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$size = 'width300';
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	
	$model = new Projects_Project(|$kapenta->db->addMarkup($args['raUID']));	
	$sql = "select * from images_image where refModule='projects' and refUID='" . $model->UID 
	     . "' order by weight";
	
	$html = '';
	
	$result = $kapenta->db->query($sql);
	if ($kapenta->db->numRows($result) > 0) {
		while ($row = $kapenta->db->fetchAssoc($result)) {
			$row = $kapenta->db->rmArray($row);
			$html .= "<a href='/images/show/" . $row['alias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['alias'] 
				. "' border='0' alt='" . $model->name . "'></a>";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
