<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//	show recent abuse reports in formatted
//--------------------------------------------------------------------------------------------------
//opt: num - number of abuse reports to show (int) [string]

function abuse_listnav($args) {
	global $user, $db, $theme;
	$html = "[[:theme::navtitlebox::label=Abuse Reports:]]\n";		//% return value [string]
	$num = 10;

	//----------------------------------------------------------------------------------------------
	//	check user roles and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	//TODO: further permissions check here, perhaps a moderator role	

	//----------------------------------------------------------------------------------------------
	//	load a page of permissions from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "status='open'";
	$range = $db->loadRange('abuse_report', '*', $conditions, 'createdOn', $num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return ''; }
	$block = $theme->loadBlock('modules/abuse/views/summarynav.block.php');
	foreach($range as $item) {
		$model = new Abuse_Report();
		$model->loadArray($item);
		$ext = $model->extArray();
		$html .= $theme->replaceLabels($ext, $block);
	}

	return $html;
}

?>
