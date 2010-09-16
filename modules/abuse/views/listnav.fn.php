<?

	require_once($installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//	show recent abuse reports in formatted
//--------------------------------------------------------------------------------------------------
//opt: num - number of abuse reports to show (int) [string]

function abuse_listnav($args) {
	global $user; //, $db;
	$html = "[[:theme::navtitlebox::label=Abuse Reports:]]\n";		//% return value [string]
	$num = 10;
	
	if ('admin' != $user->data['ofGroup']) { return ''; }
	//if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	
	$conditions = array();
	$conditions[] = "status='open'";
	$range = dbLoadRange('Abuse_Report', '*', $conditions, 'createdOn', $num);

	if (0 == count($range)) { return ''; }

	$model = new Abuse_Report();
	$block = loadBlock('modules/abuse/views/summarynav.block.php');
	foreach($range as $item) {
		$model->loadArray($item);
		$ext = $model->extArray();
		$html .= replaceLabels($ext, $block);
	}

	return $html;
}

?>
