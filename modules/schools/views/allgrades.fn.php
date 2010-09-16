<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all grades/years/forms/standards at a school
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]
//opt: schoolUID - overrides raUID [string]

function schools_allgrades($args) {
	global $db,	$user;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	if ($user->role == 'public') { return '[[:users::pleaselogin:]]'; }
	if (array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new Schools_School($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions checks here

	//----------------------------------------------------------------------------------------------
	//	query database and make the block
	//----------------------------------------------------------------------------------------------

	$sql = "select grade, count(UID) as members from Users_User "
		 . "where school='" . $model->UID . "' group by grade";

	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$link = '%%serverPath%%schools/grade/grade_' . base64_encode($row['grade'])
			  . '/' . $model->alias;

		$html .= "<a href='" . $link . "'>" . $row['grade']
			  . " (". $row['members'] ." people)</a><br/>";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
