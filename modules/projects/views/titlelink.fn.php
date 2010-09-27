<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//	project title link
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a project [string]
//opt: projectUID - replaces raUID if present [string]

function projects_titlelink($args) {
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and auth
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	load the project and return link
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($args['raUID']);
	$labels = $model->extArray();
	$block = "<a href='%%viewUrl%%'>%%title%%</a>";
	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

?>