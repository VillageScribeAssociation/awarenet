<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list abuse reports
//--------------------------------------------------------------------------------------------------
//arg: pageNo - page number to show (int) [string]

function abuse_listreports($args) {
		global $user;
		global $kapenta;
		global $theme;

	$html = '';		//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check argumentss and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	//TODO: permissions check here to allow moderator role

	//----------------------------------------------------------------------------------------------
	//	load the reports
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions = "status='open'";
	$range = $kapenta->db->loadRange('abuse_report', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/abuse/views/summary.block.php');
	//TODO: paginate this

	foreach($range as $row) {
		$model = new Abuse_Report();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}
	return $html;
}

?>
