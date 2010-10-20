<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a wiki article
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }			// check for ref
	$UID = $aliases->findRedirect('Wiki_Article');		// check correct ref

	$model = new Wiki_Article($UID);
	if (false == $user->authHas('wiki', 'Wiki_Article', 'edit', $model->UID)) { $page->do403(); }

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
			$session->msg($msg, 'ok');
			$page->doError();
		}

		$talkUID = $talk->UID;
	}

	//----------------------------------------------------------------------------------------------
	// check permissions and render the page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/wiki/actions/edittalk.page.php');
	$page->blockArgs['UID'] = $talkUID;
	$page->blockArgs['raUID'] = $talkUID;
	$page->blockArgs['parentUID'] = $model->UID;
	$page->render();

?>
