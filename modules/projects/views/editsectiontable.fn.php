<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for adding new sections
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: projectUID - overrides raUID [string]

function projects_editsectiontable($args) {
	global $user;
	if ($user->authHas('projects', 'projects_project', 'edit', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	$model = new Projects_Project(|$args['raUID']);
	if ($model->isMember($user->UID) == false) { return false; }

	$html = "<table noborder>";
	foreach($model->sections as $sUID => $section) {

		$rA = $model->alias;

		$sectionUrl = '/projects/edit/' . $rA . '#s' . $sUID;
		$editUrl = '/projects/editsection/section_' . $sUID . '/' . $rA;
		$delUrl = '/projects/delsection/section_' . $sUID . '/' . $rA;
		$moveUpUrl = '/projects/movesection/move_up/section_' . $sUID . '/' . $rA;
		$moveDnUrl = '/projects/movesection/move_down/section_' . $sUID . '/' . $rA;

		$html .= "\t<tr>\n";
		$html .= "\t\t<td><a href='" . $sectionUrl . "'>" . $section['weight'] . "</a></td>";
		$html .= "\t\t<td><a href='" . $sectionUrl . "'>" . $section['title'] . "</a></td>";
		$html .= "\t\t<td><a href='" . $editUrl . "'>[edit]</a></td>";
		$html .= "\t\t<td><a href='" . $delUrl . "'>[delete]</a></td>";
		$html .= "\t\t<td><a href='" . $moveUpUrl . "'>[move up]</a></td>";
		$html .= "\t\t<td><a href='" . $moveDnUrl . "'>[move down]</a></td>";
		$html .= "\t</tr>\n";
	}
	$html .= "</table>";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

