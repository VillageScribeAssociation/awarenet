<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	returns whether current user is a teacher
//--------------------------------------------------------------------------------------------------
//arg: raUID - school UID or recordAlias [string]

function schools_haseditauth($args) {
	global $kapenta;
	if ('admin' == $kapenta->user->role) { return 'yes'; }
	if ($kapenta->user->role == 'teacher') { return 'yes'; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	return 'no';
}


?>

