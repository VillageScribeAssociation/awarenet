<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the group's logo/picture (300px) or a blank image
//--------------------------------------------------------------------------------------------------
//opt: raUID - UID or alias of a Users_User object [string]
//opt: userUID - overrides raUID [string]
//opt: size - width100, width200, width300, width570, thumb, etc (default width300) [string]
//opt: link - link to larger image (yes|no) (default is yes) [string]

function users_avatar($args) {
	global $db;
	global $kapenta;
	global $user;

	$size = 'width300';				//%	image width [string]
	$link = 'yes';					//%	link to full size image [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (false == array_key_exists('raUID', $args)) { $args['raUID'] = $user->UID; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }
	if (true == array_key_exists('size', $args)) {	$size = $args['size']; }

	$model = new Users_User($args['raUID']);
	if (false == $model->loaded) { return '(unknown user)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html = ''
	 . '[[:images::default'
	 . '::refModule=users'
	 . '::refModel=users_user'
	 . '::refUID=' . $model->UID
	 . '::size=' . $size
	 . '::link=no'
	 . '::display=inline'
	 . ':]]';

	if ('yes' == $link) { 
		$html = "<a href='%%serverPath%%users/profile/" . $model->alias . "'>$html</a>";		
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
