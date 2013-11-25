<?

//--------------------------------------------------------------------------------------------------
//*	default action on the p2p module is to show registration data
//--------------------------------------------------------------------------------------------------
	
	if ('' != $kapenta->request->ref) { $page->do404(); }
	include $kapenta->installPath . 'modules/p2p/actions/info.act.php';

?>
