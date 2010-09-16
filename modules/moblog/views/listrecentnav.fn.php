<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list x recent posts from the same blog (ie same user) as the post UID supplied
//--------------------------------------------------------------------------------------------------
//opt: num - max number of posts to show (default is 10) [string]

function moblog_listrecentnav($args) {
	global $db, $user, $theme;
	$html = '';				//%	return value [string]
	$num = 10; 				//%	default number of posts to show [int]	

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (false == $user->authHas('moblog', 'Moblog_Post', 'show', '')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load items from database
	//----------------------------------------------------------------------------------------------
	$conditions = array("published='yes'");	
	$range = $db->loadRange('Moblog_Post', '*', $conditions, 'createdOn DESC', $num);

	//$sql = "select * from moblog"
	//	 . " where published='yes'"
	//	 . " order by createdOn DESC limit $num";	

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/summarynav.block.php');

	foreach ($range as $row) {
		$model = new Moblog_Post();
		$model->loadArray($db->rmArray($row));
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

