<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	return a project's title
//--------------------------------------------------------------------------------------------------
// * $args['projectUID'] = overrides $raUID
// * $args['raUID'] = recordAlias or UID or projects entry
// * $args['link'] = link to this record?

function projects_title($args) {
	$link = 'no';
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args) == false) { $link = $args['link']; }
	$model = new Project(sqlMarkup($args['raUID']));	
	if ($link == 'no') {
		return $model->data['title'];
	} else {
		return "<a href='/projects/" . $model->data['recordAlias'] . "'>"
			  . $model->data['title'] . "</a>";
	}
}

//--------------------------------------------------------------------------------------------------

?>