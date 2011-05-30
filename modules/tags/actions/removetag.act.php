<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	require_once($kapenta->installPath . 'modules/tags/models/index.mod.php');

//--------------------------------------------------------------------------------------------------
//*	untag an object, given the tag index UID as reference
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('Reference UID not given.', 'true'); }

	$model = new Tags_Index($req->ref);
	if (false == $model->loaded) { $page->do404('Not Tagged.', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'tags-manage', $model->refUID))
		{ $page->do403('Not authorized to edit tags.', true); }

	$return = 'tags/edittags'
		 . '/refModule_' . $model->refModule
		 . '/refModel_' . $model->refModel
		 . '/refUID_' . $model->refUID. '/';

	//----------------------------------------------------------------------------------------------
	//	remove the tag and redirect back to /tags/edittags/ iframe
	//----------------------------------------------------------------------------------------------
	$tag = new Tags_Tag($model->tagUID);
	if (true == $model->loaded) {

		// delete the index object
		$check = $model->delete();
		if (true == $check) { $session->msg('Removed tag: ' . $tag->name, 'ok'); }
		else { $session->msg('Could not remove tag: ' . $tag->name, 'bad'); }

		// update the tag object with new count
		$tag->updateObjectCount();
		$tag->save();

	} else { $session->msgAdmin('Could not update tag object: ' . $model->tagUID, 'bad'); }

	$page->do302($return);

?>
