<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a wiki article
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }			// check for ref
	$UID = $aliases->findRedirect('wiki_article');		// check correct ref

	$model = new Wiki_Article($UID);
	if (false == $user->authHas('wiki', 'wiki_article', 'edit', $model->UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	// create talk page if it does not exist
	//----------------------------------------------------------------------------------------------
	$talkUID = $model->getTalk($model->UID);
	if ('' == $talkUID) {
		$brief = 'This space is for general discussion of the article.  Comments, questions, '
				. 'TODOs, corrections and the like.';

		$talk = new Wiki_Article();						// <-- talk pages are complete articles
		$talk->namespace = 'talk';						// <-- this is what differnetiates them
		$talk->title = 'Talk: ' . $model->title;
		$talk->content = $brief;
		$talk->talkFor = $model->UID;					// <-- bind to the article
		$report = $talk->save();

		if ('' == $report) { $session->msg("Created talk page for: " . $model->alias, 'ok'); }
		else {  
			$msg = "Could not create talk page for " . $model->alias . ":<br/>" . $report;
			$kapenta->session->msg($msg, 'ok');
			$kapenta->page->doXmlError();
		}

		$talkUID = $talk->UID;
	}

	//----------------------------------------------------------------------------------------------
	// check permissions and render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/wiki/actions/edittalk.page.php');
	$kapenta->page->blockArgs['UID'] = $talkUID;
	$kapenta->page->blockArgs['raUID'] = $talkUID;
	$kapenta->page->blockArgs['parentUID'] = $model->UID;
	$kapenta->page->render();

?>
