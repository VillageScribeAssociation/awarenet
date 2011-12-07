<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show iframe block for editing tags
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Projects_Project object [string]
//opt: projectUID - overrides raUID if present [string]

function projects_edittags($args) {
	$html = '';						//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return '(project not found)'; }
	//TODO: permissions check here

	if ('open' != $model->status) { return ''; }	// project is locked or closed

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = ''
	 . '[[:tags::edittags'
	 . '::refModule=projects'
	 . '::refModel=projects_project'
	 . '::refUID=' . $model->UID
	 . ':]]';

	return $html;
}

?>
