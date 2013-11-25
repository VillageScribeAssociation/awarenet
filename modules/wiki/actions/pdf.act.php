<?

	require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a wiki article
//--------------------------------------------------------------------------------------------------
//+	If no recordAlias is specified, the 'index' article is shown.  If that does not exist then
//+	one will be created.

	
	$raUID = $kapenta->request->ref;

	//----------------------------------------------------------------------------------------------
	//	decide which article to show
	//----------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		//------------------------------------------------------------------------------------------
		//	no article specified
		//------------------------------------------------------------------------------------------

		$raUID = $aliases->getOwner('wiki', 'wiki_article', 'Index');
		if (false == $raUID) { $raUID = 'Index'; }

	} else {
		//------------------------------------------------------------------------------------------
		//	article has been specified
		//------------------------------------------------------------------------------------------
		$raUID = $aliases->getOwner('wiki', 'wiki_article', $kapenta->request->ref);	// maybe its an alias
		if (false == $raUID) {							// no? maybe its a UID

			if ($db->objectExists('wiki_article', $kapenta->request->ref) == true) {
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

	$model = new Wiki($raUID);
	$model->expandWikiText();
	$extArray = $model->extArray();	

	if (trim($extArray['infobox']) != '') {
		$extArray['infobox'] = "[[:theme::navtitlebox::label=Infobox:]]\n" 
							 . "<small>\n" . $extArray['infobox'] . "\n<br/><br/></small>";
	}

	//----------------------------------------------------------------------------------------------
	//	increment hit count
	//----------------------------------------------------------------------------------------------

	$newHit = $extArray['viewcount'] + 1;
	//$sql = "update wiki set viewcount='" . $newHit . "' where UID='" . $extArray['UID'] . "'";
	//$db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	show it
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/wiki/actions/pdf.page.php');
	$kapenta->page->blockArgs['raUID'] = $raUID;
	foreach($extArray as $key => $value) { $kapenta->page->blockArgs[$key] = $value; }
	$page->renderPdf();

?>
