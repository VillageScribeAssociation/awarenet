<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return a project's title
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: projectUID - overrides $raUID [string]
//opt: link - link to this record? [string]

function projects_title($args) {
	global $db;

	$link = 'no';
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == false) { $link = $args['link']; }
	$model = new Projects_Project($db->addMarkup($args['raUID']));	
	if ($link == 'no') {
		return $model->title;
	} else {
		return "<a href='/projects/" . $model->alias . "'>"
			  . $model->title . "</a>";
	}
}

//--------------------------------------------------------------------------------------------------

?>