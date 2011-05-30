<?

	require_once($kapenta->installPath . 'modules/comments/events/object_updated.on.php');

//-------------------------------------------------------------------------------------------------
//	look for dead images (those with a record but not file on any peer)
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	admins only
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$args = array(
		'module' => 'comments',
		'model' => 'comments_comment',
		'UID' => '1234',
		'data' => array()
	);

	comments__cb_object_updated($args);


?>
