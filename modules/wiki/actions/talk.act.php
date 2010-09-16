<?

	require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show talk page for a given article 
//--------------------------------------------------------------------------------------------------
//	NOTE! talks pages are now articles in a different namespace

	if ('' == $req->ref) { $page->do404(); } 
	$UID = $aliases->findRedirect('Wiki_Article');

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
	$page->load('modules/wiki/actions/talk.page.php');
	$page->blockArgs['raUID'] = $raUID;
	foreach($extArray as $key => $value) { $page->blockArgs[$key] = $value; }
	$page->render();

?>
