<?

//--------------------------------------------------------------------------------------------------
//	display a wiki article
//--------------------------------------------------------------------------------------------------
//	If no recordAlias is specified, the 'index' article is shown.  If that does not exist then
//	one will be created.

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	$raUID = $request['ref'];

	//----------------------------------------------------------------------------------------------
	//	decide which article to show
	//----------------------------------------------------------------------------------------------

	if ($request['ref'] == '') {
		//------------------------------------------------------------------------------------------
		//	no article specified
		//------------------------------------------------------------------------------------------
		$raUID = raGetOwner('Index', 'wiki');
		if ($raUID == false) {
			$model = new Wiki();
			$model->mkDefault();
			$model->save();
			$raUID = $model->data['recordAlias'];
		} else { 
			$raUID = 'Index'; 
		}

	} else {
		//------------------------------------------------------------------------------------------
		//	article has been specified
		//------------------------------------------------------------------------------------------
		$raUID = raGetOwner($request['ref'], 'wiki');	// maybe its a recordAlias
		if ($raUID == false) {							// no? maybe its a UID

			if (dbRecordExists('wiki', $request['ref']) == true) {
				$raUID = $request['ref'];			
			} else {
				$page->load($installPath . 'modules/wiki/actions/notfound.page.php');
				$page->render();
				die();
			}
		}


	}

	//----------------------------------------------------------------------------------------------
	//	load and process the article
	//----------------------------------------------------------------------------------------------

	$model = new Wiki($raUID);

	$model->expandWikiCode();
	$extArray = $model->extArray();	

	if (trim($extArray['infobox']) != '') {
		$extArray['infobox'] = "[[:theme::navtitlebox::label=Infobox:]]\n" 
							 . "" . $extArray['infobox'] . "\n<br/><br/>";
	}

	//----------------------------------------------------------------------------------------------
	//	increment hit count
	//----------------------------------------------------------------------------------------------

	$newHit = $extArray['hitcount'] + 1;
	$sql = "update wiki set hitcount='" . $newHit . "' where UID='" . $extArray['UID'] . "'";
	dbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	show it
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/wiki/actions/show.page.php');
	$page->blockArgs['raUID'] = $raUID;
	foreach($extArray as $key => $value) { $page->blockArgs[$key] = $value; }
	$page->render();

?>
