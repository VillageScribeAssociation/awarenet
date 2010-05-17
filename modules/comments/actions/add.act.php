<?

//--------------------------------------------------------------------------------------------------
//	add a comment to something
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check request vars 
	//----------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/comments/models/comment.mod.php');

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
	//	raise 'comment_added' event on refModule
	//----------------------------------------------------------------------------------------------

	$args = array(	'refModule' => $refModule, 
					'refUID' => $refUID, 
					'commentUID' => $commentUID, 
					'comment' => $ext['comment']    );

	eventSendSingle($refModule, 'comments_added', $args);

	//----------------------------------------------------------------------------------------------
	//	send out page notifications
	//----------------------------------------------------------------------------------------------

	$channelID = 'comments-' . $refModule . '-' . $refUID;
	$blockHtml = expandBlocks('[[:comments::summary::UID=' . $ext['UID'] . ':]]', '');
	$data = base64_encode($ext['UID'] . '|' . base64_encode($blockHtml));
	notifyChannel($channelID, 'add', $data);

	$channelID = 'comments-' . $refModule . '-' . $refUID . '-nav';
	$blockHtml = expandBlocks('[[:comments::summarynav::UID=' . $ext['UID'] . ':]]', '');
	$data = base64_encode($ext['UID'] . '|' . base64_encode($blockHtml));
	notifyChannel($channelID, 'add', $data);

	//----------------------------------------------------------------------------------------------
	//	return to whence the comment came
	//----------------------------------------------------------------------------------------------
	
	if ($return == 'none') { echo '#COMMENT ADDED\n'; }
	else { do302($return); }

?>
