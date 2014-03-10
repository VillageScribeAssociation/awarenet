<?

//-------------------------------------------------------------------------------------------------
//*	for editing text files on the server
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authorization
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	handle submissions
	//---------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('saveFile' == $_POST['action'])) {
		$fileName = stripslashes($_POST['fileName']);
		$fileName = str_replace('..', '', $fileName);
		$fileName = str_replace('//', '/', $fileName);
		$contents = stripslashes($_POST['fileContents']);
		$kapenta->fs->put($fileName, $contents, false, false);
		$kapenta->request->args['path'] = '';
	}

	//---------------------------------------------------------------------------------------------
	//	working directory
	//---------------------------------------------------------------------------------------------
	$browsePath = '';
	if (true == array_key_exists('path', $kapenta->request->args)) {
		$browsePath = 'path_' . $kapenta->request->args['path'];
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

			if (true == $kapenta->fs->exists($fileName)) {
				unlink($kapenta->installPath . $fileName);
				if (false == $kapenta->fs->exists($fileName)) {
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
	if (true == array_key_exists('file', $kapenta->request->args)) { 
		$editFile = base64_decode($kapenta->request->args['file']);
		if (true == array_key_exists('path', $kapenta->request->args)) {
			$editFile = base64_decode($kapenta->request->args['path']) . $editFile;
		}

		if (false == $kapenta->fs->exists($editFile)) { 
			$session->msg("file does not exist.<br/>" . $editFile, 'bad');
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
		
		$raw = $kapenta->fs->get($editFile);	// TODO: use $kapenta
		$rawJs = $utils->base64EncodeJs('contentJs', $raw, false);

		$editorForm = "<form name='editTxtFile' method='POST' action='" . $editorFormAction . "'>
			<input type='hidden' name='action' value='saveFile' />
			<b>File: $editFile</b>
			<input type='text' name='fileName' size='40' value='" . $editFile . "' style='width: 100%;' /><br/>
			<textarea name='fileContents' id='taFileContents' rows='30' cols='50' style='width: 100%;'></textarea>
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

	$kapenta->page->load('modules/admin/actions/txtedit.page.php');
	$kapenta->page->blockArgs['editFile'] = $editFile;
	$kapenta->page->blockArgs['editorForm'] = $editorForm;
	//$kapenta->page->blockArgs['editorList'] = $editorList;
	$kapenta->page->blockArgs['browsePath'] = $browsePath;
	$kapenta->page->render();

?>
