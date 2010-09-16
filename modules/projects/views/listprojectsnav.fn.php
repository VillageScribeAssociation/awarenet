<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list recent projects in the nav
//--------------------------------------------------------------------------------------------------
//opt: num - max number to display (default is 20) [string]

function projects_listprojectsnav($args) {
	global $db, $theme;
	$num = 20; 
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	permissions and arguments
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('num', $args)) { $num = $args['num'];}	

	//$sql = "select * from Projects_Project order by createdOn DESC limit " . $db->addMarkup($num);

	$range = $db->loadRange('Projects_Project', '*', '', 'createdOn DESC', $db->addMarkup($num));

	$block = $theme->loadBlock('modules/projects/views/summarynav.block.php');

	foreach ($range as $row) {
		$model = new Projects_Project();	
		$model->loadArray($row);
		$labels = $model->extArray();
		$html .= $theme->replaceLabels($labels, $block);
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

