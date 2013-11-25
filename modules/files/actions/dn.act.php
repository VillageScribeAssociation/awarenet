<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	load a file and return to browser as a download
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the file record
	//----------------------------------------------------------------------------------------------
	//TODO: check user auth

	if ('' == $kapenta->request->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('files_file');

	$model = new Files_File($UID);
	if (false == $model->loaded) { $page->do404('File not found.'); }
	if ('' == $model->fileName) { $page->do404('File nto found'); }
	if (false == file_exists($kapenta->installPath . $model->fileName)) { $page->do404(); }
	
	//----------------------------------------------------------------------------------------------
	//	return the file
	//----------------------------------------------------------------------------------------------
	header("Content-type: application/force-download"); 
	header('Content-Disposition: inline; filename="' . $model->title . '"'); 
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-length: " . filesize($kapenta->installPath . $model->fileName) ); 
	header('Content-Type: application/octet-stream'); 
	header('Content-Disposition: attachment; filename="' . $model->title . '"'); 
	
	readfile($kapenta->installPath . $model->fileName);
	
?>
