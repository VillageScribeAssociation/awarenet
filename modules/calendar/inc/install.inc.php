<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/calendar/models/calendar.mod.php');

function install_calendar_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new Calendar();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
