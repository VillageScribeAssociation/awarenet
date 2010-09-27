<?

//-------------------------------------------------------------------------------------------------
//	makes a link which will make a link for moving a thread to a different forum
//-------------------------------------------------------------------------------------------------
//args: UID - UID of a forum thread

function forums_movelinkjs($args) {
	global $theme;

	global $user;
	$html = '';
	//TODO: make this better

	//--------------------------------------------------------------------------------------------
	//	check arguments and auth
	//--------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return ''; }
	if ('admin' != $user->role) { return ''; }
	//TODO: permissions check here

	//--------------------------------------------------------------------------------------------
	//	make and return the block
	//--------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/forums/views/movelinkjs.block.php');
	$labels = array('UID' => $args['UID']);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>