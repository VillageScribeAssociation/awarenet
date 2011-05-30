<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list x recent posts from the same blog (ie same user) as the post UID supplied
//--------------------------------------------------------------------------------------------------
//opt: num - max number of posts to show (default is 10) [string]

function moblog_listpopularnav($args) {
	global $theme, $db, $user;
	$num = 10;
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('moblog', 'moblog_post', 'show')) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	load a set of popular posts from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "published='yes'";
	$range = $db->loadRange('moblog_post', '*', $conditions, 'viewcount DESC', $num);

	//----------------------------------------------------------------------------------------------
	//	make the block and return
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/summarynav.block.php');
	foreach($range as $row) {
		$model = new Moblog_Post();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
