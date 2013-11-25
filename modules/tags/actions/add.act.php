<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	require_once($kapenta->installPath . 'modules/tags/models/index.mod.php');

//--------------------------------------------------------------------------------------------------
//*	tag an existing object (ie, create in index between a tag and and object)
//--------------------------------------------------------------------------------------------------

	$refModule = '';
	$refModel = '';
	$refUID = '';
	$tagName = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified', true); }
	//if ('addTag' != $_POST['action']) { $page->do404('action not specified', true); }

	if (true == array_key_exists('refModule', $_POST)) { $refModule = $_POST['refModule']; }
	if (true == array_key_exists('refModel', $_POST)) { $refModel = $_POST['refModel']; }
	if (true == array_key_exists('refUID', $_POST)) { $refUID = $_POST['refUID']; }
	if (true == array_key_exists('tagName', $_POST)) { $tagName = $_POST['tagName']; }

	if (true == array_key_exists('refModule', $kapenta->request->args)) { $refModule = $kapenta->request->args['refModule']; }
	if (true == array_key_exists('refModel', $kapenta->request->args)) { $refModel = $kapenta->request->args['refModel']; }
	if (true == array_key_exists('refUID', $kapenta->request->args)) { $refUID = $kapenta->request->args['refUID']; }
	if (true == array_key_exists('tagName', $kapenta->request->args)) { $tagName = $kapenta->request->args['tagName']; }

	if ('' == $refModule) { $page->do403('No refModule specified.'); }
	if ('' == $refModel) { $page->do403('No refModel specified.'); }
	if ('' == $refUID) { $page->do403('No refUID specified.'); }
	if ('' == $tagName) { $page->do403('Tag Name not specified.'); }

	$tagName = $aliases->stringToAlias($tagName);

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('No such module.', true); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('No such owner.', true); }

	if (false == $user->authHas($refModule, $refModel, 'tags-manage', $refUID))
		{ $page->do403('Not authorized to edit tags.', true); }
	
	$return = "tags/edittags/refModule_$refModule/refModel_$refModel/refUID_$refUID/";

	if ('' == trim($tagName)) {
		$session->msg("No tag given.", 'bad');
		$page->do302($return);
	}

	/*

	//----------------------------------------------------------------------------------------------
	//	check if this tag exists, create it if it does not
	//----------------------------------------------------------------------------------------------

	$tag = new Tags_Tag($tagName, true);
	if (false == $tag->loaded) {
		$tag->name = $tagName;
		$report = $tag->save();
		if ('' == $report) { $session->msg('Started new tag: ' . $tagName); }
		else {
			$session->msg("Could not create tag: " . $report, 'bad');
			$page->do302($return);
		}
	}

	//----------------------------------------------------------------------------------------------
	//	check that this tag has not already been added
	//----------------------------------------------------------------------------------------------

	$model = new Tags_Index();
	$tagUID = $model->getTagIndexUID($refModule, $refModel, $refUID, $tag->UID);
	if (false != $tagUID) {
		$session->msg("Tag already added: " . $tagName, 'info');
		$page->do302($return);
	}

	//----------------------------------------------------------------------------------------------
	//	add the tag and redirect back to /edittags/ iframe
	//----------------------------------------------------------------------------------------------
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->refUID = $refUID;
	$model->tagUID = $tag->UID;
	$report = $model->save();

	if ('' == $report) { $session->msg("Added tag: " . $tagName, 'ok'); }
	else { $session->msg("Could not add tag:<br/>\n" . $report, 'bad'); }

	$tag->updateObjectCount();
	$tag->save();

	*/

	$args = array(
		'refModule' => $refModule,
		'refModel' => $refModel,
		'refUID' => $refUID,
		'tagName' => $tagName
	);

	$kapenta->raiseEvent('*', 'tags_add', $args);

	$page->do302($return);

?>
