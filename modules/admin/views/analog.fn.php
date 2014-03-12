<?

	require_once($kapenta->installPath . 'modules/admin/inc/logfile.class.php');

//--------------------------------------------------------------------------------------------------
//|	run analog against a pageview log file
//--------------------------------------------------------------------------------------------------
//arg: logFile - pageview log file to use [string]

function admin_analog($args) {
	global $kapenta;
	global $kapenta;
	$html = '';					//%	return value [string]
	$logFile = '';				//%	pageview log to read [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return '(not authorized)'; }
	if (false == array_key_exists('logFile', $args)) { return '(log file not given)'; }

	//----------------------------------------------------------------------------------------------
	//	convert log to apache format
	//----------------------------------------------------------------------------------------------
	$lfReader = new Admin_LogFile($args['logFile']);
	//$apache = $kapenta->fs->get($lfReader->outFile);

	$html = $lfReader->analog();
	if ('' == trim($html)) { 
		$html .= ''
			. "<div class=inlinequote'><b>Notice:</b> The report could not be generated. "
			. "Please ensure that <a href='http://www.analog.cx/'>analog</a> is installed "
			. "and that the web server is permitted to run it.</div>"; 
	}

	$html = "<small>$html</small>";

	return $html;
}

?>
