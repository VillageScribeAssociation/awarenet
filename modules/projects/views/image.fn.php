<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the project's logo/picture (300px) or a blank image
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: projectUID - overrides raUID [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 [string]
//opt: link - link to larger image (yes|no) [string]

function projects_image($args) {
	global $kapenta;
	$size = 'width300';
	$link = 'yes';
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == 'no') { $link = 'no'; }
	if (array_key_exists('size', $args)) {
		if ($args['size'] == 'thumb') { $size = 'thumb'; }
		if ($args['size'] == 'thumbsm') { $size = 'thumbsm'; }
		if ($args['size'] == 'thumb90') { $size = 'thumb90'; }
		if ($args['size'] == 'width100') { $size = 'width100'; }
		if ($args['size'] == 'width200') { $size = 'width200'; }
		if ($args['size'] == 'width300') { $size = 'width300'; }
		if ($args['size'] == 'width570') { $size = 'width570'; }
	}
	
	$row = imgGetDefault('projects', $args['raUID']);

	$model = new Projects_Project($kapenta->db->addMarkup($args['raUID']));	
	    
	if ($row == false) {
		// no images found for this project
		return "<img src='%%serverPath%%themes/%%defaultTheme%%/unavailable/" . $size . ".jpg' border='0'>"; 

	} else {
		if ($link == 'yes') {
			return "<a href='%%serverPath%%images/show/" . $row['alias'] . "'>" 
				. "<img src='%%serverPath%%images/" . $size . "/" . $imgUID 
				. "' border='0' alt='" . $model->name . "'></a>";
		} else {
			return "<img src='%%serverPath%%images/" . $size . "/" . $imgUID 
				. "' border='0' alt='" . $model->name . "'>";
		}
	}	
}

//--------------------------------------------------------------------------------------------------

?>
