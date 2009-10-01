<?

	require_once($installPath . 'modules/comments/models/comments.mod.php');

//--------------------------------------------------------------------------------------------------
//	create a link to add a new comment to something
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = the module that will own the new comment
// * $args['refUID'] = the record that will own the new announcment

function comments_newlink($args) {
	if (authHas('comments', 'edit', $args) == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$html = "<a href='/comments/new/refModule_" . $args['refModule']
			 . "/refUID_" . $args['refUID'] . "/'>[add a new comment]</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>