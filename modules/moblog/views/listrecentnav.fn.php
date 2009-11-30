<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	list x recent posts from the same blog (ie same user) as the post UID supplied
//--------------------------------------------------------------------------------------------------
//opt: num - max number of posts to show (default is 10)

function moblog_listrecentnav($args) {
	$num = 10; $html = '';
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	$model = new Moblog($args['raUID']);
	if ($model->data['UID'] == '') { return false; }

	$sql = "select * from moblog"
		 . " where published='yes'"
		 . " order by createdOn DESC limit $num";	

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
