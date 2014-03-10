<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a wiki article
//--------------------------------------------------------------------------------------------------
//	If no alias is specified, the 'index' article is shown.

	$raUID = $kapenta->request->ref;
	
	//----------------------------------------------------------------------------------------------
	//	decide which article to show
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) {
		//------------------------------------------------------------------------------------------
		//	no article specified
		//------------------------------------------------------------------------------------------
		$raUID = $aliases->getOwner('wiki', 'wiki_article', 'Index');
		if (false == $raUID) {
			$model = new Wiki_Article();
			$model->mkDefault();
			$model->save();
			$raUID = $model->alias;
		} else { 
			$raUID = 'Index'; 
		}

	} else {
		//------------------------------------------------------------------------------------------
		//	article has been specified
		//------------------------------------------------------------------------------------------
		$raUID = $aliases->getOwner('wiki', 'wiki_article', $kapenta->request->ref);	// maybe its an alias
		if ($raUID == false) {							// no? maybe its a UID

			if (true == $kapenta->db->objectExists('wiki_article', $kapenta->request->ref)) {
				$raUID = $kapenta->request->ref;			
			} else {
				$kapenta->page->load('modules/wiki/actions/notfound.page.php');
				$kapenta->page->render();
				die();
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	load and process the article
	//----------------------------------------------------------------------------------------------

	$model = new Wiki_Article($raUID);
	if ('talk' == $model->namespace) { $kapenta->page->do302('wiki/talk/' . $model->talkFor); }

	//----------------------------------------------------------------------------------------------
	//	increment hit count
	//----------------------------------------------------------------------------------------------

	//$newHit = $extArray['viewcount'] + 1;
	//$sql = "update Wiki_Article "
	//	 . "set viewcount='" . $kapenta->db->addMarkup($newHit) . "' "
	//	 . "where UID='" . $kapenta->db->addMarkup($model->UID) . "'";

	//$kapenta->db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	show it
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/wiki/actions/show.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['articleTitle'] = $model->title;
	$kapenta->page->render();

?>
