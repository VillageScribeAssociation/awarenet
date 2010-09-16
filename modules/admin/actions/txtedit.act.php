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

	if ((array_key_exists('action', $_POST) == true) && ($_POST['action'] == 'saveFile')) {
		$fileName = $installPath . stripslashes($_POST['fileName']);
		$fileName = str_replace('..', '', $fileName);
		$fileName = str_replace('//', '/', $fileName);
		$contents = stripslashes($_POST['fileContents']);
		filePutContents($fileName, $contents, 'w+');
	}

	//---------------------------------------------------------------------------------------------
	//	working directory
	//---------------------------------------------------------------------------------------------
	$browsePath = '';
	if (array_key_exists('path', $req->args) == true) {
		$browsePath = 'path_' . $req->args['path'];
	}

	//---------------------------------------------------------------------------------------------
	//	load file (if specified)
	//---------------------------------------------------------------------------------------------

	$editFile = '';
	if (array_key_exists('file', $req->args) == true) { 
		$editFile = base64_decode($req->args['file']);	
		if (file_exists($installPath . $editFile) == false) { 
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
		
		$raw = implode(file($installPath . $editFile));
		$rawJs = base64EncodeJs('contentJs', $raw, false);

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
