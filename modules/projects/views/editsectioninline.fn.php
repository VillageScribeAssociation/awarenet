<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//|	creates an iframe for editing a section on the project's page
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Section object [string]

function projects_editsectioninline($args) {
	global $kapenta;
	
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('sectionUID', $args)) { $args['UID'] = $args['sectionUID']; }
	if (false == array_key_exists('UID', $args)) { return '(UID not specified)'; }

	$model = new Projects_Section($args['UID']);
	if (false == $model->loaded) { return '(section not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html .= '<br/><br/>'
	 . "<iframe class='consoleif' "
	 . " name='editSection" . $model->UID . "'"
	 . " id='ifEditSection" . $model->UID . "'"
	 . " src='%%serverPath%%projects/editsection/" . $model->UID . "'"
	 . " width='100%' height='300' frameborder='0' "
	 . "></iframe>";

	return $html;
}

?>
