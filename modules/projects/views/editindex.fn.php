<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list revisions made to a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_editindex($args) {
		global $user;
		global $theme;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($args['raUID']);
	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) { return ''; }
	$rows = array();

	//----------------------------------------------------------------------------------------------
	//	link to abstract
	//----------------------------------------------------------------------------------------------
	$abstractUrl = "%%serverPath%%projects/editabstract/" . $model->alias;
	$abstract = array();
	$abstract[] = "<a href='" . $abstractUrl . "' target='_parent'>Title / Abstract</a>";
	$abstract[] = ''; $abstract[] = ''; 	// two blank cols
	$rows[] = $abstract;

	//----------------------------------------------------------------------------------------------
	//	links to sections
	//----------------------------------------------------------------------------------------------
	foreach($model->sections as $section) {
		$cols = array();

		$imgPath = "%%serverPath%%/themes/%%defaultTheme%%/images/";
		$imgStyle = "height='10' border='0'";
		$upImg = "<img src='". $imgPath ."btn-up.png' $imgStyle alt='move up'>";
		$dnImg = "<img src='". $imgPath ."btn-down.png' $imgStyle alt='move down'>";
		//$rmImg = "<img src='". $imgPath ."btn-del.png' $imgStyle alt='delete'>";

		$upLink = "%%serverPath%%projects/editindex/dec_" . $section['UID'] . '/' . $model->alias;
		$dnLink = "%%serverPath%%projects/editindex/inc_" . $section['UID'] . '/' . $model->alias;
		$esLink = "%%serverPath%%projects/editsection/section_" . $section['UID'] . '/' . $model->alias;
		//$rmLink = "%%serverPath%%projects/editindex/crm_" . $section['UID'] . '/' . $model->alias;

		$cols[] = "<a href='" . $esLink . "' target='_parent'>" . $section['title'] . "</a>";
		$cols[] = "<a href='" . $upLink. "'>$upImg</a>";
		$cols[] = "<a href='" . $dnLink . "'>$dnImg</a>";
		//$cols[] = "<a href='" . $rmLink . "'>$rmImg</a>";
		$rows[] = $cols;
	}

	$html = $theme->arrayToHtmlTable($rows);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

