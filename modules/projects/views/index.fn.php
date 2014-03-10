<?

//--------------------------------------------------------------------------------------------------
//|	make a project index
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a project [string]
//opt: projectUID - overrides raUID if present [string]
//opt: contents - show table of contents, default is 'yes' (yes|no) [string]

function projects_index($args) {
	global $user;
	global $kapenta;
	global $theme;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }

	$project = new Projects_Project($args['raUID']);
	if (false == $project->loaded) { return '(Project not found.)'; }

	if (false == $user->authHas('projects', 'projects_project', 'show', $project->UID)) {
		return '[[:users::pleaselogin:]]';
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/showsection.block.php');
	$contents = '';

	if (false == $project->sections->loaded) { $project->sections->load(); }
	if (0 == $project->sections->count) { return ''; }

	$html .= "<h2>Index</h2>";
	$ordinal = 1;

	foreach($project->sections->members as $section) {
		if ('yes' != $section['hidden']) {
			$link = "#s" . $section['UID'];
			$html .= "<a href='$link'>" . $ordinal . '. ' . $section['title'] . "</a><br/>";
			$ordinal++;
		}
	}

	return $html;
}

?>
