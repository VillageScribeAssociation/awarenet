<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//	display a wiki article
//--------------------------------------------------------------------------------------------------
//	If no recordAlias is specified, the 'index' article is shown.  If that does not exist then
//	one will be created.

	$raUID = $req->ref;
	
	//----------------------------------------------------------------------------------------------
	//	decide which article to show
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) {
		//------------------------------------------------------------------------------------------
		//	no article specified
		//------------------------------------------------------------------------------------------
		$raUID = $aliases->getOwner('wiki', 'Wiki_Article', 'Index');
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
		$raUID = $aliases->getOwner('wiki', 'Wiki_Article', $req->ref);	// maybe its an alias
		if ($raUID == false) {							// no? maybe its a UID

			if (true == $db->objectExists('Wiki_Article', $req->ref)) {
				$raUID = $req->ref;			
			} else {
				$page->load('modules/wiki/actions/notfound.page.php');
				$page->render();
				die();
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	load and process the article
	//----------------------------------------------------------------------------------------------

	$model = new Wiki_Article($raUID);
	$model->expandWikiCode();

	$extArray = $model->extArray();	

	if ('' != trim($extArray['infobox']) ) {
		$extArray['infobox'] = "[[:theme::navtitlebox::label=Infobox:]]\n" 
							 . "" . $extArray['infobox'] . "\n<br/><br/>";
	}

	//----------------------------------------------------------------------------------------------
	//	increment hit count
	//----------------------------------------------------------------------------------------------

	//$newHit = $extArray['viewcount'] + 1;
	//$sql = "update Wiki_Article "
	//	 . "set viewcount='" . $db->addMarkup($newHit) . "' "
	//	 . "where UID='" . $db->addMarkup($model->UID) . "'";

	//$db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	show it
	//----------------------------------------------------------------------------------------------

	$page->load('modules/wiki/actions/show.page.php');
	$page->blockArgs['raUID'] = $raUID;
	foreach($extArray as $key => $value) {  $page->blockArgs[$key] = $value; }
	$page->render();

?>
