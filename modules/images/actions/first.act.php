<?

//--------------------------------------------------------------------------------------------------
//*	display the first (default) image for a given object
//--------------------------------------------------------------------------------------------------
//reqarg: module - name of a kapenta module [string]
//reqarg: model - type of object [string]
//reqarg: uid - UID of an object which may own images [string]
//reqopt: s - size of to display [string]

	//---------------------------------------------------------------------------------------------- 
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('module', $req->args)) { $page->doXmlError('Module not given'); }
	if (false == array_key_exists('model', $req->args)) { $page->doXmlError('Model not given'); }
	if (false == array_key_exists('uid', $req->args)) { $page->doXmlError('UID not given'); }

	$size = 'width300';			// default
	if (true == array_key_exists('s', $req->args)) { $size = $req->args['s']; }

	//---------------------------------------------------------------------------------------------- 
	//	look up default image and redirect
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($req->args['module']) ."'";
	$conditions[] = "refModel='" . $db->addMarkup($req->args['model']) ."'";
	$conditions[] = "refUID='" . $db->addMarkup($req->args['uid']) ."'";

	$range = $db->loadRange('images_image', '*', $conditions, 'weight', '1');

	if (0 == count($range)) {
		// display 'not found' image
		//TODO: fix this up
		$page->do302("images/s_" . $size . "/" . $kapenta->createUID()); 

	} else {
		$item = array_pop($range);
		$page->do302("images/s_" . $size . "/" . $item['alias']); 
	}


?>
