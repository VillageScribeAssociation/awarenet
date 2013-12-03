<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add new comments, submitted by xmlHTTPrequest
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to which comment is exported, required [string]
//arg: refModel - type of object which owns the comment, required [string]
//arg: refUID - UID of object which owns the comment, required [string]
//arg: return - page to return to, required [string]
//opt: invitation - text encouraging someone to leave a comment, optional [string]

function comments_addcommentformnavjs($args) {
	global $theme, $user;
	$invitation = 'Add a comment';
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }
	if (false == array_key_exists('return', $args)) { return '(no return destination)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (true == array_key_exists('invitation', $args)) { $invitation = $args['invitiation']; }	
	if (false == $user->authHas($refModule, $refModel, 'comments-add', $refUID)) 
		{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array();
	$labels['invitation'] = $invitation;
	$labels['refModule'] = $refModule;
	$labels['refModel'] = $refModel;
	$labels['refUID'] = $refUID;
	$labels['return'] = $args['return'];

	$block =$theme->loadBlock('modules/comments/views/addcommentnavjs.block.php');
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
