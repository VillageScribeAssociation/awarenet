<?

	require_once($kapenta->installPath . 'modules/admin/inc/logfile.class.php');

//--------------------------------------------------------------------------------------------------
//*	test of logfile class
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$parser = new Admin_LogFile('data/log/11-05-05-pageview.log.php');

	

?>
