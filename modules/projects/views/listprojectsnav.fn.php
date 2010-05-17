<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list recent projects in the nav
//--------------------------------------------------------------------------------------------------
//opt: num - max number to display (default is 20) [string]

function projects_listprojectsnav($args) {
	$num = 20; $html = '';
	if (array_key_exists('num', $args)) { $num = $args['num'];}	

	$sql = "select * from projects order by createdOn DESC limit " . sqlMarkup($num);
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$model = new Project();	
		$model->loadArray(sqlRMArray($row));
		$labels = $model->extArray();
		$html .= replaceLabels($labels, loadBlock('modules/projects/views/summarynav.block.php'));
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

