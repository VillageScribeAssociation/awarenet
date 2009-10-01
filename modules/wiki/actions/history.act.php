<?

//--------------------------------------------------------------------------------------------------
//	display complete revision history for a given wiki article
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	$UID = raFindRedirect('wiki', 'history', 'wiki', $request['ref']);

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	$model = new Wiki($UID);

	$page->load($installPath . 'modules/wiki/actions/history.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['articleTitle'] = $model->data['title'];
	$page->render();

?>
