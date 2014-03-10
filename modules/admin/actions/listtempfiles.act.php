<?

//--------------------------------------------------------------------------------------------------
//*	find temporary/devlopment/editor files and remove them
//--------------------------------------------------------------------------------------------------
//+	for example tilde files left by gedit, windows thumbs database, svn files, etc

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	$fileList = '';										//%	page content [string:html]
	$postUrl = '%%serverPath%%admin/listtempfiles/';	//%	post back to self [string]
	$foundAny = false;									//%	have any junk files been found [bool]

	//----------------------------------------------------------------------------------------------
	//	handle any POST requests
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('deleteAll' == $_POST['action'])) {
		$raw = '';
		if (true == array_key_exists('junkList', $_POST)) { $raw = $_POST['junkList']; }
		$raw = str_replace("\r", "\n", $raw);
		$list = explode("\n", $raw);
		
		foreach($list as $item) {
			$item = trim($item);
			if (strlen($item) > 1) {
				if (true == $kapenta->fs->exists($item)) {
					// check this isn't a system file
					$check = false;
					if (false != strpos($item, '~')) { $check = true; }
					if (false != strpos($item, '/.svn/')) { $check = true; }
					if (false != strpos($item, 'thumbs.db')) { $check = true; }

					if (true == $check) {
						$result = unlink($kapenta->installPath . $item);
						if (true == $result) {
							$session->msg('<b>deleted:</b> ' . $item, 'ok');
						} else {
							$session->msg('<b>could not delete:</b> ' . $item, 'bad');
						}
					} else {
						$session->msg('<b>refusing to delete:</b> ' . $item, 'bad');
					}
				}
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//	find gedit temp files
	//----------------------------------------------------------------------------------------------
	$list = $kapenta->fs->search('modules/', '~');
	
	if (count($list) > 0) {
		$foundAny = true;
		$fileList .= "<h2>gedit undo files</h2>\n";
		$taValue = '';
		foreach($list as $item) { 
			$fileList .= $item . "<br/>\n"; 
			$taValue = $taValue . $item . "\n";
		}
		$fileList .= "
			<br/><b>Clear:</b><br/>\n
			<form name='frmDelEditorFiles' method='POST' action='$postUrl'>
			<input type='hidden' name='action' value='deleteAll' />
			<textarea rows='10' cols='80' name='junkList'>$taValue</textarea>
			<input type='submit' value='Delete All &gt;&gt;' />
			</form><hr/>
		";
	}

	//----------------------------------------------------------------------------------------------
	//	find svn files
	//----------------------------------------------------------------------------------------------

	$allFiles = $kapenta->fs->search('', '');
	$list = array();
	foreach($allFiles as $item) {
		if (false != strpos($item, '/.svn/')) { $list[] = $item; }
	}

	if (count($list) > 0) {
		$foundAny = true;
		$fileList .= "<h2>SVN files</h2>\n";
		$taValue = '';
		foreach($list as $item) { 
			$fileList .= $item . "<br/>\n"; 
			$taValue = $taValue . $item . "\n";
		}
		$fileList .= "
			<br/><b>Clear:</b><br/>\n
			<form name='frmDelSvnFiles' method='POST' action='$postUrl'>
			<input type='hidden' name='action' value='deleteAll' />
			<textarea rows='10' cols='80' name='junkList'>$taValue</textarea>
			<input type='submit' value='Delete All &gt;&gt;' />
			</form><hr/>
		";
	}

	//----------------------------------------------------------------------------------------------
	//	find thumbs.db files
	//----------------------------------------------------------------------------------------------

	$list = array();
	foreach($allFiles as $item) {
		if (false != strpos($item, '/thumbs.db')) { $list[] = $item; }
	}

	if (count($list) > 0) {
		$foundAny = true;
		$fileList .= "<h2>thumbs.db files</h2>\n";
		$taValue = '';
		foreach($list as $item) { 
			$fileList .= $item . "<br/>\n"; 
			$taValue = $taValue . $item . "\n";
		}
		$fileList .= "
			<br/><b>Clear:</b><br/>\n
			<form name='frmDelSvnFiles' method='POST' action='$postUrl'>
			<input type='hidden' name='action' value='deleteAll' />
			<textarea rows='10' cols='80' name='junkList'>$taValue</textarea>
			<input type='submit' value='Delete All &gt;&gt;' />
			</form><hr/>
		";
	}	

	//----------------------------------------------------------------------------------------------
	//	'nothing found' notice
	//----------------------------------------------------------------------------------------------
	
	if (false == $foundAny) {
		$fileList .= "<h1>Nothing Found</h1>"
			 . "<p>Your kapenta installation appears clean of common junk :-)</p>";
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/admin/actions/listtempfiles.page.php');
	$kapenta->page->blockArgs['fileList'] = $fileList;
	$kapenta->page->render();

?>
