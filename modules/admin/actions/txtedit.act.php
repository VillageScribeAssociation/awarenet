<?

//-------------------------------------------------------------------------------------------------
//*	for editing text files on the server
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
	//	confirm file deletion (if specified)
	//---------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('confirmDeleteFile' == $_POST['action'])) {
		if (true == array_key_exists('delfile', $_POST)) {

			$msg = "<b>Confirm: you wish to delete " . $_POST['delfile'] . "?</b><br/>
					<p>Note that this action cannot be undone and may affect the functioning of this website.</p>
					<table noborder>
					  <tr>
					    <td valign='top'>
					    <form name='confirmDelete' method='POST' action='%%serverPath%%admin/txtedit/'>
					    <input type='hidden' name='action' value='deleteFile' />
					    <input type='hidden' name='delfile' value='" . $_POST['delfile']  . "' />
					    <input type='submit' value='Yes: Delete it' />
					    </form>
					    </td>
					    <td valign='top'>
					    <form name='cancelDelete' method='POST' action='%%serverPath%%admin/txtedit/'>
					    <input type='submit' value='No: Cancel' />
					    </form>
					    </td>
					  </tr>
					</table>\n";

			$session->msg($msg, 'warn');
		}	
	}

	//---------------------------------------------------------------------------------------------
	//	delete a file (if specified)
	//---------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('deleteFile' == $_POST['action'])) {
		if (true == array_key_exists('delfile', $_POST)) {
			$fileName = $_POST['delfile'];
			if (substr($fileName,0, 1) == '/') { $fileName = substr($fileName, 1); }

			if (true == $kapenta->fileExists($fileName)) {
				unlink($kapenta->installPath . $fileName);
				if (false == $kapenta->fileExists($fileName)) {
					$session->msg("Deleted: " . $fileName, 'ok');				
				} else {
					$session->msg("Could not delete: " . $fileName, 'bad');				
				}

			} else { $session->msg("Could not delete: " . $fileName, 'bad');	}
		}	
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

		$editorFormAction = $kapenta->serverPath 
			 . 'admin/txtedit/'
			 . '/file_' . base64_encode($editFile) 
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
			</script>\n

			<hr/>
			<form name='delTxtFile' method='POST' action='" . $editorFormAction . "'>
			<input type='hidden' name='action' value='confirmDeleteFile' />
			<input type='hidden' name='delfile' value='" . $editFile . "' />
			<input type='submit' value='Delete this file' />
			</form>
			";
	}

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------

	$page->load('modules/admin/actions/txtedit.page.php');
	$page->blockArgs['editFile'] = $editFile;
	$page->blockArgs['editorForm'] = $editorForm;
	//$page->blockArgs['editorList'] = $editorList;
	$page->blockArgs['browsePath'] = $browsePath;
	$page->render();

?>
