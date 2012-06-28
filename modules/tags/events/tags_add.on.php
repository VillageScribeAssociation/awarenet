<?php

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	require_once($kapenta->installPath . 'modules/tags/models/index.mod.php');

//--------------------------------------------------------------------------------------------------
//|	event fired by other modules requesting an object be tagged
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have tags [string]
//arg: refUID - UID of object which may have tags [string]
//arg: tagName - plaintext to tag the item  with [string]

function tags__cb_tags_add($args) {
	global $kapenta;
	global $user;
	global $db;
	global $session;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) {
		$session->msgAdmin('tasg_add: refModule not given.', 'bug');
		return false;
	}
	if (false == array_key_exists('refModel', $args)) {
		$session->msgAdmin('tags_add: refModel not given.', 'bug');
		return false;
	}

	if (false == array_key_exists('refUID', $args)) {
		$session->msgAdmin('tags_add: refUID not given.', 'bug');
		return false;
	}
	if (false == array_key_exists('tagName', $args)) {
		$session->msgAdmin('tags_add: tagName not given.', 'bug');
		return false;
	}

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	$tagName = $args['tagName'];

	if (false == $kapenta->moduleExists($refModule)) {
		$session->msgAdmin('tags_add: unknown module.');
		return false;
	}
	if (false == $db->objectExists($refModel, $refUID)) {
		$session->msgAdmin('tags_add: unknown owner object.');
		return false;
	}

	if (strlen(trim($tagName)) <= 2) {
		$session->msgAdmin('tags_add: tag too short.');
		return false;
	}

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
			return false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	check that this tag has not already been added
	//----------------------------------------------------------------------------------------------
	$model = new Tags_Index();
	$tagUID = $model->getTagIndexUID($refModule, $refModel, $refUID, $tag->UID);
	if (false != $tagUID) {
		$session->msg("Tag already added: " . $tagName, 'info');
		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//	link the tag and the object
	//----------------------------------------------------------------------------------------------
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->refUID = $refUID;
	$model->tagUID = $tag->UID;
	$report = $model->save();

	if ('' == $report) { $session->msg("Added tag: " . $tagName, 'ok'); }
	else { $session->msg("Could not add tag:<br/>\n" . $report, 'bad'); }

	$tag->updateObjectCount();
	$report = $tag->save();

	if ('' == $report) { return true; }
	return false;
}

?>
