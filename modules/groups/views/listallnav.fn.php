<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all formatted for navigation bar
//--------------------------------------------------------------------------------------------------
//opt: school - UID of a school, constrains results [string]
//opt: sameschool - UID of a group, constrains results to those of the same school [string]

function groups_listallnav($args) {
		global $kapenta;
		global $kapenta;

	$html = '';					//%	return value [string]
	$conditions = array();		//%	array of conditions to filter groups table by [array]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return '[[:users::pleaselogin:]]'; }	
	if (true == array_key_exists('school', $args)) 
		{ $conditions[] = "school='" . $kapenta->db->addMarkup($args['school']) . "'"; }

	if (true == array_key_exists('sameschool', $args)) {
		$model = new Groups_Group($args['sameschool']);
		if (false == $model->loaded) { return ''; }
		$conditions[] = "school='" . $kapenta->db->addMarkup($model->school) . "'";
	}

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('groups_group', '*', $conditions, 'name');
	foreach ($range as $row) { $html .= "[[:groups::summarynav::groupUID=". $row['UID'] .":]]\n"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
