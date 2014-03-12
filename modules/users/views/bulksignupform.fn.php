<?

//--------------------------------------------------------------------------------------------------
//|	form for adding batches of users to the system
//--------------------------------------------------------------------------------------------------
//opt: school - school to which users belong, UID of schools_school object [string]
//opt: grade - greade to which users belong [string]

function users_bulksignupform($args) {
	global $kapenta;
	global $theme;

	$html = '';					//%	return value [string]
	$school = '';				//%	UID of school to which users will belong [string]
	$grade = '';				//%	grade of which users will be members [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions / role and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('school', $args)) { $school = $args['school']; }
	if (true == array_key_exists('grade', $args)) { $grade = $args['grade']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/bulksignupform.block.php');
	$labels = array('school' => $school, 'grade' => $grade);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
