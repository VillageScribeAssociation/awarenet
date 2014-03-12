<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing an article section
//--------------------------------------------------------------------------------------------------
//arg: UID - alias or UID of a Projects_Section object [string]
//opt: sectionUID - overrides raUID if present [string]

function projects_editsectionform($args) {
		global $theme;
		global $kapenta;
		global $utils;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('sectionUID', $args)) { $args['UID'] = $args['sectionUID']; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	$model = new Projects_Section($args['UID']);
	if (false == $model->loaded) { return '(unkown section)'; }
	if (false == $kapenta->user->authHas('projects', 'projects_section', 'edit', $model->UID)) { return ''; }
	if ('yes' == $model->hidden) { return "<span class='ajaxwarn'>Removed</span>"; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['content64'] = $utils->b64wrap($labels['content']);
	$block = $theme->loadBlock('modules/projects/views/editsectionform.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
