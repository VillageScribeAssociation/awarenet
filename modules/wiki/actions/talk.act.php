<?

//--------------------------------------------------------------------------------------------------
//	show talk page for a given article
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	$raUID = $request['ref'];

	if ($request['ref'] == '') { do404(); } 

	$UID = raFindRedirect('wiki', 'talk', 'wiki', $request['ref']);

	//----------------------------------------------------------------------------------------------
	//	load and process the article and talk page
	//----------------------------------------------------------------------------------------------

	$model = new Wiki($raUID);
	$model->expandWikiCode();
	$extArray = $model->extArray();	

	if (trim($extArray['talkHtml']) == '') { 
		$extArray['talkHtml'] = "<p>There isn't a conversation yet, but you can start one :-)</p>"; 
	}

	//----------------------------------------------------------------------------------------------
	//	show it
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/wiki/actions/talk.page.php');
	$page->blockArgs['raUID'] = $raUID;
	foreach($extArray as $key => $value) { $page->blockArgs[$key] = $value; }
	$page->render();

?>
