<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	show signup form 
//--------------------------------------------------------------------------------------------------
// * $args['clear'] = do not pre-fill form values

function users_signupform($args) {

	$formvars = array(
		'UID' => createUID(),		'school' => '261390791197222710',
		'grade' => '12',			'firstname' => '',	
		'surname' => '',			'username' => '',	
		'password' => '',			'lang' => 'en',	
		'profile' => '',			'permissions' => '',	
		'lastOnline' => mysql_datetime(),
		'createdOn' => mysql_datetime(),	
		'createdBy' => 'admin',		'pass1' => '',
		'pass2' => ''	);

	if ($args['clear'] == 'yes') 
		{ foreach($formvars as $field => $value) { $args[$field] = $value; } }

	return replaceLabels($args, loadBlock('modules/users/views/signupform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>