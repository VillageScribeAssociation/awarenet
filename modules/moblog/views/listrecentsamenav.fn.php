<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	list x recent posts from the same blog (ie same user) as the post UID supplied
//--------------------------------------------------------------------------------------------------
// * $args['postUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID of moblog post
// * $args['num'] = max number of posts to show

function moblog_listrecentsamenav($args) {
	global $user;
	$num = 10; $html = '';
	if (array_key_exists('postUID', $args) == true) { $args['raUID'] = $args['postUID']; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new Moblog($args['raUID']);
	if ($model->data['UID'] == '') { return false; }

	$sql = "select * from moblog"
		 . " where createdBy='" . $model->data['createdBy'] . "'"
		 . " and (published='yes' or createdBy='" . $user->data['UID'] . "')"
		 . " order by createdOn DESC limit $num ";	

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$model = new Moblog();
		$model->loadArray(sqlRMArray($row));
		$html .= replaceLabels($model->extArray(), loadBlock('modules/moblog/views/summarynav.block.php'));
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>