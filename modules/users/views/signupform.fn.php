<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show signup form 
//--------------------------------------------------------------------------------------------------
//opt: clear - do not pre-fill form values (yes|no), no is default [string]

function users_signupform($args) {
	global $kapenta, $db, $theme;
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and any permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('clear', $args)) { $args['clear'] = 'no'; }
	//TODO: check public permission to signup

	//----------------------------------------------------------------------------------------------
	//	set defaults
	//----------------------------------------------------------------------------------------------
	$formvars = array(
		'UID' => $kapenta->createUID(),		'school' => '261390791197222710',
		'grade' => '12',					'firstname' => '',	
		'surname' => '',					'username' => '',	
		'password' => '',					'lang' => 'en',	
		'profile' => '',					'permissions' => '',	
		'lastOnline' => $db->datetime(),	'createdOn' => $db->datetime(),	
		'createdBy' => 'admin',				'pass1' => '',
		'pass2' => ''	);

	if  ('yes' == $args['clear']) 
		{ foreach($formvars as $field => $value) { $args[$field] = $value; } }


	//----------------------------------------------------------------------------------------------
	//	make and return the form
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/signupform.block.php');
	$html = $theme->replaceLabels($args, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
