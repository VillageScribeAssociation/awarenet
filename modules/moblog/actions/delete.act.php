<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a moblog post
//--------------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('sction not specified'); }
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('action not supported'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID of post not given'); }

	//----------------------------------------------------------------------------------------------
	//	load the post in question
	//----------------------------------------------------------------------------------------------
	$model = new Moblog_Post($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Post not found.'); }
	if (false == $kapenta->user->authHas('moblog', 'moblog_post', 'delete', $model->UID)) { $kapenta->page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	delete it
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$kapenta->session->msg("Deleted moblog post: " . $model->title, 'ok');
	$kapenta->page->do302('moblog/blog/' . $kapenta->user->alias); 

?>
