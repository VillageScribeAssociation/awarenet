<?

//--------------------------------------------------------------------------------------------------
//	show current and previous versions of a wiki document
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------

	if (authHas('wiki', 'show', '') == false) { do403(); }
	if ($request['ref'] == '') { do404(); }
	if (dbRecordExists('wikirevisions', $request['ref']) == false) { do404(); }

	//----------------------------------------------------------------------------------------------
	//	revision exists, load it
	//----------------------------------------------------------------------------------------------
	$model = new WikiRevision($request['ref']);

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$page->load($installPath . 'modules/wiki/showrevision.page.php');
	$page->blockArgs['currentRevision'] = $model->data['UID'];
	$page->blockArgs['previousRevision'] = $model->getPrevious();

?>
