<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	look up name of a user's school
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Users_User object [string]
//opt: userUID - overrides raUID [string]
//opt: link - create link to school, default is yes (yes|no) [string]

function users_schoolname($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$html = '';			//% return value [string]
	$link = 'yes';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Users_User($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here

	if ((true == array_key_exists('link', $args)) && ('no' == $args['link'])) { $link = 'no'; }

	//----------------------------------------------------------------------------------------------
	//	make the link
	//----------------------------------------------------------------------------------------------
	$block = '[[:schools::name::link=' . $link . '::schoolUID=' . $model->school . ':]]';
	$html = $theme->expandBlocks($block, '');

	return $html;
}

?>
