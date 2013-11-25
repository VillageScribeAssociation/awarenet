<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a single notice
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Newsletter_Notice object [string]
//opt: noticeUID - replaces UID [string]

function newsletter_shownotice($args) {
	global $db;
	global $user;
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('noticeUID', $args)) { $args['UID'] = $args['noticeUID']; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	$model = new Newsletter_Notice($args['UID']);
	if (false == $model->UID) { return '(could not load notice)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/shownotice.block.php');
	$labels = $model->extArray();

	$labels['editLinkJs'] = ''
	 . "<a"
		 . " href='javascript:void(0);'"
		 . " onClick=\"newsletter_editnotice('" . $model->UID . "');\""
	 . ">[edit]</a>";

	if ('admin' != $user->role) { $labels['editLinkJs'] = ''; } 

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
