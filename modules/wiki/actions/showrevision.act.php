<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show current and adjacent versions of a wiki document
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$model = new Wiki_Revision($kapenta->request->ref);
	if (false == $model->loaded) {$kapenta->page->do404(); }
	if (false == $kapenta->user->authHas('wiki', 'wiki_revision', 'show', $model->UID)) { $kapenta->page->do403(); }

	$article = new Wiki_Article($model->articleUID);
	if (false == $article->loaded) {$kapenta->page->do404(); };
	if (false == $kapenta->user->authHas('wiki', 'wiki_article', 'show', $article->UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------

	$article->title = $model->title;
	$article->content = $model->content;
	$article->nav = $model->nav;
	$article->wikicode->source = $model->content;
	$article->expandWikiCode();

	$extArray = $article->extArray();

	//TODO: tidy this all up

	$kapenta->page->load('modules/wiki/actions/showrevision.page.php');
	$kapenta->page->blockArgs['currentRevision'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['articleTitle'] = $model->title;
	$kapenta->page->blockArgs['contentHtml'] = "[[:wiki::revisioncontent::raUID=" . $model->UID . ":]]";
	//foreach($extArray as $key => $value) {  $kapenta->page->blockArgs[$key] = $value; }		// messy
	$kapenta->page->render();

?>
