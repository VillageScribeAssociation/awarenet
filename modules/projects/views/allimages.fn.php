<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	all images of a project (default 300px wide)
//--------------------------------------------------------------------------------------------------
// * $args['projectUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or projects entry
// * $args['size'] = sinze to display images

function projects_allimages($args) {
	global $serverPath;
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$size = 'width300';
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	
	$model = new project(sqlMarkup($args['raUID']));	
	$sql = "select * from images where refModule='projects' and refUID='" . $model->data['UID'] 
	     . "' order by weight";
	
	$html = '';
	
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$html .= "<a href='/images/show/" . $row['recordAlias'] . "'>" 
				. "<img src='/images/" . $size . "/" . $row['recordAlias'] 
				. "' border='0' alt='" . $model->data['name'] . "'></a>";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>