<?

//-------------------------------------------------------------------------------------------------
//	for browsing files
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authorization
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->doXmlError('not authorized'); }

	//---------------------------------------------------------------------------------------------
	//	get path (if present)
	//---------------------------------------------------------------------------------------------
	
	$browsePath = $installPath;
	if (array_key_exists('path', $req->args) == true) { 
		$browsePath = $installPath . base64_decode($req->args['path']);
		$browsePath = str_replace('//', '/', $browsePath);
		$browsePath = str_replace('..', '.', $browsePath);
	}

	//---------------------------------------------------------------------------------------------
	//	work out parent directory
	//---------------------------------------------------------------------------------------------

	$dirs = explode("/", $browsePath);
	$discard = array_pop($dirs);
	$parentDir = implode("/", $dirs);
	$parentLink = '';
	if (($browsePath == $installPath) OR ($parentDir . '/' == $installPath)) {
		// in docRoot, or parent is docroot
		$parentUrl = "%%serverPath%%admin/filebrowser/";
		$parentLink = "<a href='" . $parentUrl . "'>[ &lt;&lt; parent directory ]</a>";
	} else {
		$parentDir = str_replace($installPath, '', $parentDir);			
		$parentUrl = "%%serverPath%%admin/filebrowser/path_" . base64_encode($parentDir);
		$parentLink = "<a href='" . $parentUrl . "'>[ &lt;&lt; parent directory ]</a>";
	}

	//---------------------------------------------------------------------------------------------
	//	make list of items in this folder
	//---------------------------------------------------------------------------------------------

	$fileList = '';
	$raw = shell_exec('ls ' . $browsePath);
	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		$showThis = false;

		if (is_dir($browsePath . '/' . $line) == true) {
			//-------------------------------------------------------------------------------------
			//	directories
			//-------------------------------------------------------------------------------------
			$relPath = str_replace($installPath , '', $browsePath . '/' . $line);
			$dirUrl = "admin/filebrowser/path_" . base64_encode($relPath);
			$line = "<a href='%%serverPath%%" . $dirUrl . "'>$line</a>";
			$showThis = true;

		} else {
			//-------------------------------------------------------------------------------------
			//	files
			//-------------------------------------------------------------------------------------
			$allow = array('php', 'txt', 'xml');
			foreach($allow as $ext) 
				{ if (strpos(strtolower(' ' . $line), $ext) == true) { $showThis = true; } }

			$relFile = str_replace($installPath , '', $browsePath . '/' . $line);
			$relPath = str_replace($installPath , '', dirname($browsePath . '/' . $line));
		
			if ($relPath . '/' == $installPath) {
				$editUrl = 'admin/txtedit/file_' . base64_encode($relFile);
			} else {
				$editUrl = 'admin/txtedit/file_' . base64_encode($relFile) 
						 . '/path_' . base64_encode($relPath);
			}

			if (true == $showThis) {
				$line = "<a href='%%serverPath%%" . $editUrl . "' target='_parent'>$line</a>";
			} else {
				$showThis = true;	// display, but no link			
			}

		}

		if (true == $showThis) { $fileList .= $line . "<br/>\n"; }
	}

	//---------------------------------------------------------------------------------------------
	//	render page
	//---------------------------------------------------------------------------------------------

	$page->load('modules/admin/actions/filebrowser.page.php');
	$page->blockArgs['filePath'] = '~/' . str_replace($installPath, '', $browsePath);
	$page->blockArgs['fileList'] = $fileList;
	$page->blockArgs['parentLink'] = $parentLink;
	$page->render();

?>
