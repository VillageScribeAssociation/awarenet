<?

//--------------------------------------------------------------------------------------------------
//*	images API (DEPRECATED)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check that user is logged in and refmodule, refuid supplied
	//----------------------------------------------------------------------------------------------

	if ($user->role == 'public') { $page->doXmlError('not logged in'); }

	if (false == array_key_exists('refuid', $req->args)) { $page->doXmlError('refUID not given'); }
	if (false == array_key_exists('refmodule', $req->args)) { $page->doXmlError('module not specified'); }

	//----------------------------------------------------------------------------------------------
	//	list images 
	//----------------------------------------------------------------------------------------------

	if ('list' == $req->ref) {

		$conditions = array();
		if (array_key_exists('refuid', $req->args) == true) 
			{ $conditions[] = "refUID='" . $db->addMarkup($req->args['refuid']) . "'"; }

		if (array_key_exists('refmodule', $req->args) == true) 
			{ $conditions[] = "refModule='" . $db->addMarkup($req->args['refmodule']) . "'"; }

		$range = $db->loadRange('Images_Image', '*', $conditions);

		echo "<?xml version=\"1.0\"?>\n";
		echo "<imageset>\n";	

		foreach ($range as $row) {
			$model = new Images_Image();
			$model->loadArray($row);
			echo $model->toXml('    ');
		}

		echo "</imageset>\n";

	}

?>
