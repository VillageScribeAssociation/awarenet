<?

//--------------------------------------------------------------------------------------------------
//	add a new moblog post
//--------------------------------------------------------------------------------------------------

	if (authHas('moblog', 'edit', '') == false) { do403(); }	// authorised to have a blog?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');

	$post = new Moblog();
	$post->data['title'] = 'New Post ' . $post->data['UID'];
	$post->save();
	
	do302('moblog/edit/' . $post->data['UID']);

?>
