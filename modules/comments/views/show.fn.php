<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]
//opt: UID - overrides raUID if present [string]
//opt: commentUID - overrides raUID if present [string]

function comments_show($args) {
	global $theme;
	global $user;
	global $session;
	global $page;
	
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('commentUID', $args)) { $args['raUID'] = $args['commentUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }

	$model = new Comments_Comment($args['raUID']);
	if (false == $model->loaded) { return '(comment not found: ' . $args['raUID'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$page->requireJs('%%serverPath%%modules/comments/js/comments.js');

	$block = $theme->loadBlock('modules/comments/views/show.block.php');

	if ('' != $model->parent) { 
		$block = $theme->loadBlock('modules/comments/views/reply.block.php');
	}

	$labels = $model->extArray();
	$html = $theme->replaceLabels($labels, $block);

	if (false == $session->get('mobile')) { $html = $theme->expandBlocks($html, 'indent'); }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
