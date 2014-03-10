<?php

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	Show replies to a comment
//--------------------------------------------------------------------------------------------------
//arg: parentUID - UID fo a Comments_Comment object [string]

function comments_replies($args) {
	global $kapenta;
	global $user;
	global $theme;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('parentUID', $args)) { return '(parentUID not given)'; }

	$model = new Comments_Comment($args['parentUID']);
	if (false == $model->loaded) { return '(no such parent comment)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("parent='" . $kapenta->db->addMarkup($model->UID) . "'");
	$totalReplies = $kapenta->db->countRange('comments_comment', $conditions);

	if (0 == $totalReplies) { return ''; }

	$range = $kapenta->db->loadRange('comments_comment', '*', $conditions, 'createdOn ASC');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/views/reply.block.php');

	foreach($range as $item) {
		$html .= "[[:comments::show::commentUID=" . $item['UID'] . ":]]";
	}

	$html = $theme->expandBlocks($html, 'nav1');

	return $html;
}
