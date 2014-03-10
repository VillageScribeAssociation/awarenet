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
	if (false == array_key_exists('module', $kapenta->request->args)) { $page->doXmlError('Module not given'); }
	if (false == array_key_exists('model', $kapenta->request->args)) { $page->doXmlError('Model not given'); }
	if (false == array_key_exists('uid', $kapenta->request->args)) { $page->doXmlError('UID not given'); }

	$size = 'width300';			// default
	if (true == array_key_exists('s', $kapenta->request->args)) { $size = $kapenta->request->args['s']; }

	//---------------------------------------------------------------------------------------------- 
	//	look up default image and redirect
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($kapenta->request->args['module']) ."'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($kapenta->request->args['model']) ."'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($kapenta->request->args['uid']) ."'";

	$range = $kapenta->db->loadRange('images_image', '*', $conditions, 'weight', '1');

	if (0 == count($range)) {
		// display 'not found' image
		//TODO: fix this up
		$page->do302("images/s_" . $size . "/" . $kapenta->createUID()); 

	} else {
		$item = array_pop($range);
		$page->do302("images/s_" . $size . "/" . $item['alias']); 
	}


?>
