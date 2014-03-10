<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list x recent posts from the same blog (ie same user) as the post UID supplied
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of moblog post [string]
//opt: postUID - overrides raUID [string]
//opt: num - max number of posts to show (default 10) [string]

function moblog_listrecentsamenav($args) {
		global $db;
		global $theme;
		global $user;

	$html = '';						//%	return value [string]
	$num = 10;						//% default max number of items to show [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }

	if (true == array_key_exists('postUID', $args)) { $args['raUID'] = $args['postUID']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Moblog_Post($args['raUID']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load items from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "createdBy='" . $db->addMarkup($model->createdBy) . "'";
	$conditions[] = "(published='yes' or createdBy='" . $db->addMarkup($user->UID) . "')";

	$range = $db->loadRange('moblog_post', '*', $conditions, "createdOn DESC", $num); 

	//$sql = "select * from moblog"
	//	 . " where createdBy='" . $model->createdBy . "'"
	//	 . " and (published='yes' or createdBy='" . $user->UID . "')"
	//	 . " order by createdOn DESC limit $num ";	

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/summarynav.block.php');
	foreach ($range as $row) {
		$model = new Moblog_Post();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
