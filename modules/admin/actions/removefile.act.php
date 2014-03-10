<?

//--------------------------------------------------------------------------------------------------
//*	deletes a local file which is not present in the repository
//--------------------------------------------------------------------------------------------------
//postarg: fileName - location relative to installPath [string]
//postarg: return - location to return to on success [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not specified'); }
	if ('removeFile' != $_POST['action']) { $kapenta->page->do404('action not recognized'); }
	if (false == array_key_exists('fileName', $_POST)) { $kapenta->page->do404('fileName not given'); }
	if (false == array_key_exists('return', $_POST)) { $kapenta->page->do404('Package not specified'); }

	$fileName = $kapenta->fileCheckName($_POST['fileName']);
	if (false == $fileName) { $kapenta->page->do404('No such file.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the file
	//----------------------------------------------------------------------------------------------
	$check = @unlink($kapenta->installPath . $fileName);
	if (true == $check) { $session->msg('Deleted file: ' . $_POST['fileName'], 'ok'); }
	else { $session->msg('Could not delete file: ' . $_POST['fileName'], 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to package listing
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302($_POST['return']);

?>
