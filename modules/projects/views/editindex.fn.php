<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list revisions made to a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_editindex($args) {
	if (authHas('projects', 'view', '') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	load the project
	//----------------------------------------------------------------------------------------------
	$model = new Project($args['raUID']);
	$thisRa = $model->data['recordAlias'];
	$rows = array();

	//----------------------------------------------------------------------------------------------
	//	link to abstract
	//----------------------------------------------------------------------------------------------
	$abstractUrl = "%%serverPath%%projects/editabstract/" . $thisRa;
	$abstract = array();
	$abstract[] = "<a href='" . $abstractUrl . "' target='_parent'>Title / Abstract</a>";
	$abstract[] = ''; $abstract[] = ''; 	// two blank cols
	$rows[] = $abstract;

	//----------------------------------------------------------------------------------------------
	//	links to sections
	//----------------------------------------------------------------------------------------------
	foreach($model->sections as $section) {
		$cols = array();

		$imgPath = "%%serverPath%%/themes/clockface/images/";
		$imgStyle = "height='10' border='0'";
		$upImg = "<img src='". $imgPath ."btn-up.png' $imgStyle alt='move up'>";
		$dnImg = "<img src='". $imgPath ."btn-down.png' $imgStyle alt='move down'>";
		//$rmImg = "<img src='". $imgPath ."btn-del.png' $imgStyle alt='delete'>";

		$upLink = "%%serverPath%%projects/editindex/dec_" . $section['UID'] . '/' . $thisRa;
		$dnLink = "%%serverPath%%projects/editindex/inc_" . $section['UID'] . '/' . $thisRa;
		$esLink = "%%serverPath%%projects/editsection/section_" . $section['UID'] . '/' . $thisRa;
		//$rmLink = "%%serverPath%%projects/editindex/crm_" . $section['UID'] . '/' . $thisRa;

		$cols[] = "<a href='" . $esLink . "' target='_parent'>" . $section['title'] . "</a>";
		$cols[] = "<a href='" . $upLink. "'>$upImg</a>";
		$cols[] = "<a href='" . $dnLink . "'>$dnImg</a>";
		//$cols[] = "<a href='" . $rmLink . "'>$rmImg</a>";
		$rows[] = $cols;
	}

	$html = arrayToHtmlTable($rows);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

