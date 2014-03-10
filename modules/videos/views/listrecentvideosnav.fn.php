<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list x recent videos
//--------------------------------------------------------------------------------------------------
//opt: num - max number of posts to show (default is 10) [string]

function videos_listrecentvideosnav($args) {
	global $kapenta;
	global $user;
	global $theme;

	$html = '';				//%	return value [string]
	$num = 10; 				//%	default number of posts to show [int]	

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (false == $user->authHas('videos', 'videos_video', 'show', '')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load items from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$range = $kapenta->db->loadRange('videos_video', '*', $conditions, 'createdOn DESC', $num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/videosummarynav.block.php');

	foreach ($range as $row) {
		$model = new Videos_Video();
		$model->loadArray($kapenta->db->rmArray($row));
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

