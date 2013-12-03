<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all formatted for navigation bar
//--------------------------------------------------------------------------------------------------
//opt: school - UID of a school, constrains results [string]
//opt: sameschool - UID of a group, constrains results to those of the same school [string]

function groups_listallnav($args) {
	global $db, $user;
	$html = '';					//%	return value [string]
	$conditions = array();		//%	array of conditions to filter groups table by [array]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }	
	if (true == array_key_exists('school', $args)) 
		{ $conditions[] = "school='" . $db->addMarkup($args['school']) . "'"; }

	if (true == array_key_exists('sameschool', $args)) {
		$model = new Groups_Group($args['sameschool']);
		if (false == $model->loaded) { return ''; }
		$conditions[] = "school='" . $db->addMarkup($model->school) . "'";
	}

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('groups_group', '*', $conditions, 'name');
	foreach ($range as $row) { $html .= "[[:groups::summarynav::groupUID=". $row['UID'] .":]]\n"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
