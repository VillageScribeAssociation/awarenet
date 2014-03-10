<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//*	returns a package manifest (XML document) for the specified package
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->doXmlError('Package not specified.'); }
	//TODO: add code to support private and restricted packages (requiring authentication)

	$model = new Code_Package($kapenta->request->ref);
	if (false == $model->loaded) { $page->doXmlError('Package not found.'); }

	//----------------------------------------------------------------------------------------------
	//	return the document - previous, deprecated version (could not handle very large packages)
	//----------------------------------------------------------------------------------------------
	#header('Content-type: text/xml');

	#$block = '[[:code::xmlmanifest::packageUID=' . $model->UID . ':]]';
	#$xml = $theme->expandBlocks($block, '');
	#echo $xml;

	//----------------------------------------------------------------------------------------------
	//	stream the document
	//----------------------------------------------------------------------------------------------
	header('Content-type: text/xml');

	echo ''
	 . "<package>\n"
	 . "\t<uid>" . $model->UID . "</uid>\n"
	 . "\t<name>" . $model->name . "</name>\n"
	 . "\t<description>" . $model->description . "</description>\n"
	 . "\t<version>" . $model->version . "</version>\n"
	 . "\t<revision>" . $model->revision . "</revision>\n"
	 . "\t<updated>" . $model->editedOn . "</updated>\n"
	 . "\t<installfile>" . $model->installFile . "</installfile>\n"
	 . "\t<installfn>" . $model->installFn . "</installfn>\n"
	 . "\t<files>\n";

	$sql = "select UID, hash, type, size, path from code_file where package='" . $kapenta->db->addMarkup($model->UID) . "'";
	$result = $kapenta->db->query($sql);

	while($row = $kapenta->db->fetchAssoc($result)) { 
		$item = $kapenta->db->rmArray($row);
		echo ''
		 . "\t\t<file>\n"
		 . "\t\t\t<uid>" . $item['UID'] . "</uid>\n"
		 . "\t\t\t<hash>" . $item['hash'] . "</hash>\n"
		 . "\t\t\t<type>" . $item['type'] . "</type>\n"
		 . "\t\t\t<size>" . $item['size'] . "</size>\n"
		 . "\t\t\t<path>" . $item['path'] . "</path>\n"
		 . "\t\t</file>\n";
	}

	echo ''	
	 . "\t</files>\n"
	 . "\t<dependencies>\n";

	$dependencies = '';					//	(TODO: implement package dependencies)

	$filter = '';
	$includes = $model->getIncludes();	
	foreach($includes as $match) { $filter .= "\t\t<include>" . $match . "</include>\n"; }

	$excludes = $model->getExcludes();	
	foreach($excludes as $match) { $filter .= "\t\t<exclude>" . $match . "</exclude>\n"; }

	echo ''
	 .  "\t</dependencies>\n"
	 . "\t<filter>\n"
	 . $filter
	 . "\t</filter>\n"
	 . "</package>\n";

?>
