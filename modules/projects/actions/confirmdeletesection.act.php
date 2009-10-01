<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a project section
//--------------------------------------------------------------------------------------------------

	if (authHas('projects', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (array_key_exists('section', $request['args']) == false) { do404(); }
	if (dbRecordExists('projects', $request['args']['uid']) == false) { do404(); }
	
	require_once($installPath . 'modules/projects/models/projects.mod.php');

	$model = new Project($request['args']['uid']);
	$thisRa = $model->data['recordAlias'];
	if (false == $model->hasEditAuth($user->data['UID'])) { do403(); }
	
	$labels = array(
					'UID' => $request['args']['uid'], 
					'section' => $request['args']['section'],
					'raUID' => $model->data['recordAlias']
					);
	
	$html .= replaceLabels($labels, loadBlock('modules/projects/views/confirmdeletesection.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('projects/editsection/section_' . $request['args']['section'] . '/' . $thisRa);

?>
