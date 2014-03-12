<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	create a link to add a new comment to something
//--------------------------------------------------------------------------------------------------
//arg: refModule - the module that will own the new comment [string]
//arg: refModel - type of object that will own the new comment [string]
//arg: refUID - the record that will own the new announcment [string]
//TODO: discover if this is used by anything and delete if not

function comments_newlink($args) {
	global $kapenta;	

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('comments', 'comment_comment', 'edit')) { return ''; }

	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make the link
	//----------------------------------------------------------------------------------------------
	$newUrl = "comments/new/"
	 . "refModule_" . $args['refModule']
	 . "refModel_" . $args['refModel']
	 . "/refUID_" . $args['refUID'] . "/";

	$html = "<a href='%%serverPath%%'>[add a new comment]</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

