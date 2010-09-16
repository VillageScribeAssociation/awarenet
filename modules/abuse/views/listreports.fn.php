<?

	require_once($installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list abuse reports
//--------------------------------------------------------------------------------------------------
//arg: pageNo - page number to show (int) [string]

function abuse_listreports($args) {
	global $user;
	if ('admin' != $user->data['ofGroup']) { return ''; }
	$html = '';		//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	load the reports
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions = "status='open'";
	$range = dbLoadRange('Abuse_Report', '*', $conditions);

	$model = new Abuse_Report();
	$block = loadBlock('modules/abuse/views/summary.block.php');

	foreach($range as $row) {
		$model->loadArray($row);
		$html .= replaceLabels($model->extArray(), $block);

	}
	return $html;
}

?>
