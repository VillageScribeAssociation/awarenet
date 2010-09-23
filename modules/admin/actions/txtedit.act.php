<?

//-------------------------------------------------------------------------------------------------
//	for editing text files on the server
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authorization
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	handle submissions
	//---------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('saveFile' == $_POST['action'])) {
		$fileName = stripslashes($_POST['fileName']);
		$fileName = str_replace('..', '', $fileName);
		$fileName = str_replace('//', '/', $fileName);
		$contents = stripslashes($_POST['fileContents']);
		$kapenta->filePutContents($fileName, $contents, false, false);
	}

	//---------------------------------------------------------------------------------------------
	//	working directory
	//---------------------------------------------------------------------------------------------
	$browsePath = '';
	if (true == array_key_exists('path', $req->args)) {
		$browsePath = 'path_' . $req->args['path'];
	}

	//---------------------------------------------------------------------------------------------
	//	load file (if specified)
	//---------------------------------------------------------------------------------------------

	$editFile = '';
	if (true == array_key_exists('file', $req->args)) { 
		$editFile = base64_decode($req->args['file']);	
		if (false == $kapenta->fileExists($editFile)) { 
			$_SESSION['sMessage'] .= "file '" . $editFile . "' does not exist.<br/>\n";
			$editFile = ''; 
		}
	}

	//---------------------------------------------------------------------------------------------
	//	make the edit form
	//---------------------------------------------------------------------------------------------

	$editorForm = '';
	if ($editFile != '') {

		$editorFormAction =  $serverPath . 'admin/txtedit/file_' . base64_encode($editFile) 
						  . '/' . $browsePath;
		
		$raw = $kapenta->fileGetContents($editFile);	// TODO: use $kapenta
		$rawJs = $utils->base64EncodeJs('contentJs', $raw, false);

		$editorForm = "<form name='editTxtFile' method='POST' action='" . $editorFormAction . "'>
			<input type='hidden' name='action' value='saveFile' />
			<b>File:</b>
			<input type='text' name='fileName' size='63' value='" . $editFile . "' /><br/>
			<textarea name='fileContents' id='taFileContents' rows='30' cols='78'></textarea>
			<input type='submit' value='save' />
			</form><br/>
			<script language='javascript'>
			$rawJs
			base64_loadTextArea('taFileContents', contentJs);
			</script>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------

	$page->load('modules/admin/actions/txtedit.page.php');
	$page->blockArgs['editFile'] = $editFile;
	$page->blockArgs['editorForm'] = $editorForm;
	$page->blockArgs['editorList'] = $editorList;
	$page->blockArgs['browsePath'] = $browsePath;
	$page->render();

?>
