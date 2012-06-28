<?

//--------------------------------------------------------------------------------------------------
//*	temporary / development action to show next report to be sent
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$date = substr($kapenta->datetime(), 0, 10);		//	 default report for today
	if (true == array_key_exists('date', $req->args)) { $date = $req->args['date']; }

	$report = $theme->expandBlocks('[[:twitter::daily::date=' . $date . ':]]', '');
	if ('' == trim($report)) { $report = "Nothing to report."; }

	//----------------------------------------------------------------------------------------------
	//	show the report
	//----------------------------------------------------------------------------------------------
	header("Content-type: text/plain");
	echo $date . "\n";
	echo "$date - " . $report;

?>
