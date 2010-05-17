<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a post [string]

function moblog_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new moblog($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/moblog/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

