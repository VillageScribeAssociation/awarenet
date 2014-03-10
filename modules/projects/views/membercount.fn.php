<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return number of members in project
//--------------------------------------------------------------------------------------------------
//arg: projectUID - UID pf project to count members for [string]

function projects_membercount($args) {
		global $kapenta;
		global $user;

	$num = '0';				//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }

	$model = new Projects_Project($args['raUID']);

	if (false == $model->loaded) { return ''; }

	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID))
		{ return ''; }

	$conditions = array();
	$conditions[] = "projectUID='" . $kapenta->db->addMarkup($model->UID) . "'";
	$conditions[] = "role != 'asked'";

	//$sql = "select count(UID) as memberCount from Projects_Membership "
	//	 . "where projectUID='" . $args['projectUID'] . "' and role != 'asked'";

	$num = $kapenta->db->countRange('projects_membership', $conditions) . '';

	return $num;
}

//--------------------------------------------------------------------------------------------------

?>

