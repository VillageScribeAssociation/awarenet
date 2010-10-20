<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show talk page for a given article 
//--------------------------------------------------------------------------------------------------
//+	NOTE! talks pages are now articles in a different namespace
//+	The refrence that is givne should be to the prant (main) atricle, to which the talk page 
//+	belongs, not to the talk page itself.

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); } 
	$UID = $aliases->findRedirect('Wiki_Article');

	$model = new Wiki_Article($UID);
	if (false == $model->loaded) { $page->do404('Could not load parent article.'); }
	if (false == $user->authHas('wiki', 'Wiki_Article', 'show', $model->UID)) { $page->do403(); }

	if ('talk' == $model->namespace) { $page->do302('wiki/talk/' . $model->talkFor); }

	$talkUID = $model->getTalk($model->UID);

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	if ('' == $talkUID) { 
		//------------------------------------------------------------------------------------------
		//	talk page does not yet exist
		//------------------------------------------------------------------------------------------
		$pdx = "<p>There isn't a conversation yet, but you can start one :-)</p>";
		$extArray = $model->extArray();

		$page->load('modules/wiki/actions/talk.page.php');
		$page->blockArgs['raUID'] = $model->UID;
		foreach($extArray as $key => $value) { $page->blockArgs[$key] = $value; }
		$page->blockArgs['talkHtml'] = $pdx;
		$page->blockArgs['parentTitle'] = $model->title;
		$page->render();


	} else {
		//------------------------------------------------------------------------------------------
		//	talk page does exist, show it
		//------------------------------------------------------------------------------------------
		$talk = new Wiki_Article($talkUID);
		if (false == $talk->loaded) { $page->do404('Missing talk page.'); }
		$talk->expandWikiCode();
		$extArray = $talk->extArray();	

		$page->load('modules/wiki/actions/talk.page.php');
		$page->blockArgs['raUID'] = $talkUID;
		$page->blockArgs['parentTitle'] = $model->title;
		$page->blockArgs['parentUID'] = $model->UID;
		foreach($extArray as $key => $value) { $page->blockArgs[$key] = $value; }
		$page->render();

	}

?>
