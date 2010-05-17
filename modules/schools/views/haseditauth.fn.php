<?

	require_once($installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	returns whether current user is a teacher
//--------------------------------------------------------------------------------------------------
//arg: raUID - school UID or recordAlias [string]

function schools_haseditauth($args) {
	global $user;
	if ($user->data['ofGroup'] == 'admin') { return 'yes'; }
	if ($user->data['ofGroup'] == 'teacher') { return 'yes'; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	return 'no';
}


?>

