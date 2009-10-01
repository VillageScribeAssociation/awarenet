<?

//--------------------------------------------------------------------------------------------------
//	edit a server record
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }

	if (trim($request['ref'] == '')) { do404(); }
	if (dbRecordExists('servers', $request['ref']) == false) { do404(); }

	$page->load($installPath . 'modules/sync/actions/editserver.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();

?>
