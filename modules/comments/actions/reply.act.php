<?php

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action for saving a reply to a comment (called via AJAX)
//--------------------------------------------------------------------------------------------------
//postarg: action - set to addReply [string]
//postarg: parentUID - UID of a Comments_Comment object [string]
//postarg: reply[parentUID] - text of comment to add [string]

	//----------------------------------------------------------------------------------------------
	//	check POST arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->doXmlError('action not given'); }
	if ('addReply' != $_POST['action']) { $kapenta->page->doXmlError('action not supported'); }

	if (false == array_key_exists('parentUID', $_POST)) { $kapenta->page->doXmlError('parentUUID missing'); }

	$parent = new Comments_Comment($_POST['parentUID']);
	if (false == $parent->loaded) { $kapenta->page->doXmlError('Unknown parent comment.'); }

	if (false == array_key_exists('reply' . $_POST['parentUID'], $_POST)) {
		$kapenta->page->doXmlError('No comment sent. reply' . $_POST['parentUID']);
	}

	$commentTxt = $utils->cleanHtml($_POST['reply' . $_POST['parentUID']]);

	//----------------------------------------------------------------------------------------------
	//	save the reply
	//----------------------------------------------------------------------------------------------
	$model = new Comments_Comment();

	$model->refModule = $parent->refModule;
	$model->refModel = $parent->refModel;
	$model->refUID = $parent->refUID;
	$model->parent = $parent->UID;
	$model->comment = $commentTxt;	

	$report = $model->save();
	if ('' != $report) { $kapenta->page->doXmlError($report); }

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	echo "<ok/>";

?>
