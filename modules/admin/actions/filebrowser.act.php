<?

//-------------------------------------------------------------------------------------------------
//*	iframe for browsing files
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authorization
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->doXmlError('not authorized'); }

	//---------------------------------------------------------------------------------------------
	//	get path (if present)
	//---------------------------------------------------------------------------------------------
	
	$browsePath = '';
	if (true == array_key_exists('path', $kapenta->request->args)) { 
		$browsePath = base64_decode($kapenta->request->args['path']);
		$browsePath = str_replace('//', '/', $browsePath);
		$browsePath = str_replace('..', '.', $browsePath);
	}

	//---------------------------------------------------------------------------------------------
	//	work out parent directory
	//---------------------------------------------------------------------------------------------

	$dirs = explode("/", $browsePath);
	$discard = array_pop($dirs);
	$discard = array_pop($dirs);
	$parentDir = implode("/", $dirs) . '/';
	if ('/' == $parentDir) { $parentDir = ''; }
	$parentLink = '';
	if (($browsePath == $kapenta->installPath) OR ($parentDir . '/' == $kapenta->installPath)) {
		// in docRoot, or parent is docroot
		$parentUrl = "%%serverPath%%admin/filebrowser/";
		$parentLink = "<a href='" . $parentUrl . "'>[ &lt;&lt; parent directory ]</a>";
	} else {
		$parentDir = str_replace($kapenta->installPath, '', $parentDir);			
		$parentUrl = "%%serverPath%%admin/filebrowser/path_" . base64_encode($parentDir);
		$parentLink = "<a href='" . $parentUrl . "'>[ &lt;&lt; parent directory ]</a>";
	}

	//---------------------------------------------------------------------------------------------
	//	make table of items in this folder
	//---------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('', '');

	//---------------------------------------------------------------------------------------------
	//	list subfolders
	//---------------------------------------------------------------------------------------------

	$folders = $kapenta->fs->listDir($browsePath, '', true);
	foreach($folders as $folder) {
		$folder = str_replace($browsePath, '', $folder);
		$path = $browsePath . $folder . '/';
		$dirUrl = "admin/filebrowser/path_" . base64_encode($path);
		$line = "<a href='%%serverPath%%" . $dirUrl . "'>" . $folder . "</a>";
		$table[] = array('', $line);
	}

	//---------------------------------------------------------------------------------------------
	//	list files in this folder
	//---------------------------------------------------------------------------------------------
	$files = $kapenta->fs->listDir($browsePath, '', false);
	sort($files);
	foreach($files as $file) {
		$file = str_replace($browsePath, '', $file);
		$showThis = false;
		$allow = array('php', 'txt', 'xml', 'js');

		foreach($allow as $ext) {
			if (false != strpos(strtolower(' ' . $file), $ext)) { $showThis = true; }
		}

		$path = $browsePath . $file;

		$editUrl = 'admin/txtedit/file_' . base64_encode($file);	
		if ('' != $path) { $editUrl .=  '/path_' . base64_encode($browsePath); }

		$line = "<a href='%%serverPath%%" . $editUrl . "' target='_parent'>$file</a>";
		if (false == $showThis) { $line = $file; }

		$table[] = array('', $line);

	}

	//---------------------------------------------------------------------------------------------
	//	render page
	//---------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/admin/actions/filebrowser.page.php');
	$kapenta->page->blockArgs['filePath'] = '~/' . str_replace($kapenta->installPath, '', $browsePath);
	$kapenta->page->blockArgs['fileList'] = $theme->arrayToHtmlTable($table, true, true);
	$kapenta->page->blockArgs['parentLink'] = $parentLink;
	$kapenta->page->render();

?>
