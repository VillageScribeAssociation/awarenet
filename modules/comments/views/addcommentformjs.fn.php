<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add new comments, submitted by xmlHTTPrequest
//--------------------------------------------------------------------------------------------------
//arg: refModule - module which owns the object which may have comments, required [string]
//arg: refModel - type of object, required [string]
//arg: refUID - UID of object which owns the comment, required [string]
//arg: return - page to return to, required [string]
//opt: invitation - text encouraging someone to leave a comment, optional [string]

function comments_addcommentformjs($args) {
		global $theme;
		global $user;

	$invitation = 'Add a comment';		//%	default call to action [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (false == array_key_exists('refModel', $args)) { return ''; }
	if (false == array_key_exists('refUID', $args)) { return ''; }
	if (false == array_key_exists('return', $args)) { return ''; }

	if (true == array_key_exists('invitation', $args)) { $invitation = $args['invitiation']; }	
	if (false == $user->authHas($args['refModule'], $args['refModel'], 'comments-add', $args['refUID']))
		{ return ''; }


	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array();
	$labels['invitation'] = $invitation;
	$labels['refModule'] = $args['refModule'];
	$labels['refModel'] = $args['refModel'];
	$labels['refUID'] = $args['refUID'];
	$labels['return'] = $args['return'];

	$block = $theme->loadBlock('modules/comments/views/addcommentjs.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
