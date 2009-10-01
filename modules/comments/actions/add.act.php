<?

//--------------------------------------------------------------------------------------------------
//	add a comment to something
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check request vars 
	//----------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/comments/models/comments.mod.php');

	if (array_key_exists('refModule', $_POST) == false) { do404(); }
	if (array_key_exists('refUID', $_POST) == false) { do404(); }
	if (array_key_exists('return', $_POST) == false) { do404(); }

	$refModule = $_POST['refModule'];
	$refUID = $_POST['refUID'];
	$return = $_POST['return'];

	//----------------------------------------------------------------------------------------------
	//	check permissions, valid module
	//----------------------------------------------------------------------------------------------

	if (authHas($refModule, 'comment', '') == false) { do403(); }
	if (in_array($refModule, listModules()) == false) { do404(); }

	//----------------------------------------------------------------------------------------------
	//	dont save blank comments
	//----------------------------------------------------------------------------------------------

	if (trim($_POST['comment']) == '') { do302($return); }

	//----------------------------------------------------------------------------------------------
	//	save the record
	//----------------------------------------------------------------------------------------------

	$model = new Comment();
	$model->data['UID'] = createUID();
	$model->data['refModule'] = $refModule;
	$model->data['refUID'] = $refUID;
	$model->data['comment'] = strip_tags($_POST['comment']);
	$model->data['createdBy'] = $user->data['UID'];
	$model->data['createdOn'] = mysql_datetime();
	$ext = $model->extArray();
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	check if a comment_add callback can be sent to this refModule
	//----------------------------------------------------------------------------------------------

	$callBackFile = $installPath . 'modules/' . $refModule . '/callbacks.inc.php';
	$callBackFn = $refModule . '__cb_comments_add';
	if (file_exists($callBackFile) == true) {
		require_once($callBackFile);
		if (function_exists($callBackFn) == true) {
			
			//--------------------------------------------------------------------------------------
			//	send the callback
			//--------------------------------------------------------------------------------------

			$callBackFn($refUID, $ext['UID'], $ext['comment']);

		}
	}

	//----------------------------------------------------------------------------------------------
	//	return to whence the comment came
	//----------------------------------------------------------------------------------------------

	do302($return);

?>
