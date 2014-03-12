<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add new comments, narrow version for the nav
//--------------------------------------------------------------------------------------------------
//arg: refModule - owner module to which this is exported, required [string]
//arg: refUID - type of object which owns the comment, required [string]
//arg: refUID - object which owns the comment, required [string]
//arg: return - page to return to, required [string]
//opt: invitation - text encouraging someone to leave a comment, optional [string]

function comments_addcommentformnav($args) {
		global $theme;
		global $kapenta;

	$html = '';							//%	return value [string]
	$invitation = 'Add a comment';

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }
	if (false == array_key_exists('return', $args)) { return '(no return)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (true == array_key_exists('invitation', $args)) { $invitation = $args['invitiation']; }	
	if (false == $kapenta->user->authHas($refModule, $refModel, 'comments-add', $refUID))
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

	$block = $theme->loadBlock('modules/comments/views/addcommentnav.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
