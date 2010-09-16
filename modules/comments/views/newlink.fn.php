<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	create a link to add a new comment to something
//--------------------------------------------------------------------------------------------------
//arg: refModule - the module that will own the new comment [string]
//arg: refUID - the record that will own the new announcment [string]

function comments_newlink($args) {
	if ($user->authHas('comments', 'Comment_Comment', 'edit', $args) == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$html = "<a href='/comments/new/refModule_" . $args['refModule']
			 . "/refUID_" . $args['refUID'] . "/'>[add a new comment]</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

