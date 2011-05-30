<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show current and adjacent versions of a wiki document
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$model = new Wiki_Revision($req->ref);
	if (false == $model->loaded) {$page->do404(); }
	if (false == $user->authHas('wiki', 'wiki_revision', 'show', $model->UID)) { $page->do403(); }

	$article = new Wiki_Article($model->articleUID);
	if (false == $article->loaded) {$page->do404(); };
	if (false == $user->authHas('wiki', 'wiki_article', 'show', $article->UID)) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------

	$article->title = $model->title;
	$article->content = $model->content;
	$article->nav = $model->nav;
	$article->wikicode->source = $model->content;
	$article->expandWikiCode();

	$extArray = $article->extArray();

	if ('' != trim($extArray['infobox'])) {
		$extArray['infobox'] = "[[:theme::navtitlebox::label=Infobox:]]\n" 
							 . "" . $extArray['infobox'] . "\n<br/><br/>";
	}

	//TODO: tidy this all up

	$page->load('modules/wiki/actions/showrevision.page.php');
	$page->blockArgs['currentRevision'] = $model->UID;
	$page->blockArgs['raUID'] = $model->UID;											
	foreach($extArray as $key => $value) {  $page->blockArgs[$key] = $value; }		// messy
	$page->render();

?>
