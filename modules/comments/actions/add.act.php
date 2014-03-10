<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a comment to something
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check request vars 
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('refModule', $_POST)) { $kapenta->page->do404('refModule not given'); }
	if (false == array_key_exists('refModel', $_POST)) { $kapenta->page->do404('refModel not given'); }
	if (false == array_key_exists('refUID', $_POST)) { $kapenta->page->do404('refUID not given'); }
	if (false == array_key_exists('return', $_POST)) { $kapenta->page->do404('no return url'); }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];
	$replyTo = '';
	$return = $_POST['return'];

	if (true == array_key_exists('replyTo', $_POST)) {
		$replyTo = $_POST['replyTo'];
		if (false == $kapenta->db->objectExists('comments_comment', $replyTo)) {
			$kapenta->page->do404('(cannot reply to missing comment');
		}
	}

	//----------------------------------------------------------------------------------------------
	//	check permissions, valid module
	//----------------------------------------------------------------------------------------------
	//TODO: check that model exists
	if (false == in_array($refModule, $kapenta->listModules())) { $kapenta->page->do404(); }
	if (false == $user->authHas($refModule, $refModel, 'comments-add', $refUID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	dont save blank comments
	//----------------------------------------------------------------------------------------------
	if ('' == trim($_POST['comment'])) { 
		$session->msg('No comment entered', 'bad');
		$kapenta->page->do302($return); 
	}

	//----------------------------------------------------------------------------------------------
	//	save the record
	//----------------------------------------------------------------------------------------------

	$model = new Comments_Comment();
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->refUID = $refUID;
	$model->comment = $utils->cleanHtml($_POST['comment']);
	$ext = $model->extArray();
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	raise 'comment_added' event on refModule
	//----------------------------------------------------------------------------------------------
	$args = array(
		'refModule' => $refModule, 
		'refModel' => $refModel, 
		'refUID' => $refUID, 
		'commentUID' => $ext['UID'], 
		'comment' => $ext['comment']
	);

	$kapenta->raiseEvent($refModule, 'comments_added', $args);
	
	//----------------------------------------------------------------------------------------------
	//	return to whence the comment came
	//----------------------------------------------------------------------------------------------
	
	if ('none' == $return) { echo '#COMMENT ADDED\n'; }	//TODO: XML option
	else { $kapenta->page->do302($return); }

?>
