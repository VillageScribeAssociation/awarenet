<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show all sections of a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a project [string]
//opt: projectUID - overrides raUID if present [string]
//opt: contents - show table of contents, default is 'yes' (yes|no) [string]

function projects_allsections($args) {
	global $user;
	global $db;
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

	foreach($project->sections->members as $sUID => $section) {
		$model = new Projects_Section();
		$model->loadArray($section);
		if ('yes' != $model->hidden) {
			$html .= $theme->replaceLabels($model->extArray(), $block);
		}
	}

	return $html;
}

?>
