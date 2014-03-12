<?
	
	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save an edit to a wiki page
//--------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not supplied'); }
	if ('savePage' != $_POST['action']) { $kapenta->page->do404('action not supported'); }

	$model = new Wiki_Article($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('no such article'); }
	if (false == $kapenta->user->authHas('wiki', 'wiki_article', 'edit', $model->UID)) { $kapenta->page->do403(); }

	//------------------------------------------------------------------------------------------
	//	save any changes
	//------------------------------------------------------------------------------------------
	//TODO: sanitizing here

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':	$model->title = $utils->cleanString($value);	break;
			case 'content':	$model->content = $value;	break;
			case 'nav':		$model->nav = $value;	break;
		}
	}

	$report = $model->save();
	$retUrl = 'wiki/' . $model->alias;
	if ('article' == $model->namespace) { 	$retUrl = 'wiki/' . $model->alias; }
	if ('talk' == $model->namespace) { 	$retUrl = 'wiki/talk/' . $model->talkFor; }

	if ('' == $report) { 
		//------------------------------------------------------------------------------------------
		//	everything went better and expetion /dolan
		//------------------------------------------------------------------------------------------
		switch($model->namespace) {
			case 'article': 	$kapenta->session->msg("Saved edit to wiki article.", 'ok');			break;
			case 'talk': 		$kapenta->session->msg("Saved edit to wiki talk page.", 'ok');		break;
		}

	} else {
		//------------------------------------------------------------------------------------------
		//	couldn't save it, bail here and don't save a revision
		//------------------------------------------------------------------------------------------
		$kapenta->session->msg("Could not save changes to wiki article:<br/> $report", 'bad');
		$kapenta->page->do302($retUrl);
	}

	//--------------------------------------------------------------------------------------
	//	save new content in revisions table
	//--------------------------------------------------------------------------------------
	
	$reason = '';
	if (true == array_key_exists('reason', $_POST)) {
		$reason = $utils->cleanString($_POST['reason']);
	}

	$revision = new Wiki_Revision();
	$revision->articleUID = $model->UID;
	$revision->content = $model->content;
	$revision->nav = $model->nav;
	$revision->title = $model->title;
	$revision->reason = $reason;
	$report = $revision->save();

	if ('' == $report) { $kapenta->session->msg("Saved revision.", 'ok'); }
	else { $kapenta->session->msg("Could not save revision:<br/>$report", 'bad');  }
	
	//--------------------------------------------------------------------------------------
	//	done, 302 back to the article
	//--------------------------------------------------------------------------------------
	$kapenta->page->do302($retUrl);			

?>
