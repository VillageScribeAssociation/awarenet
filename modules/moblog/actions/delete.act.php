<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a moblog post
//--------------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('sction not specified'); }
	if ('deleteRecord' != $_POST['action']) { $page->do404('action not supported'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID of post not given'); }

	//----------------------------------------------------------------------------------------------
	//	load the post in question
	//----------------------------------------------------------------------------------------------
	$model = new Moblog_Post($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Post not found.'); }
	if (false == $user->authHas('moblog', 'Moblog_Post', 'delete', $model->UID)) { $page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	delete it
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted moblog post: " . $model->title, 'ok');
	$page->do302('moblog/blog/' . $user->alias); 

?>
