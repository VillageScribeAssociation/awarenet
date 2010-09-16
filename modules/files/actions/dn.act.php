<?

//--------------------------------------------------------------------------------------------------
//	load a file and return to browser as a download
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the file record
	//----------------------------------------------------------------------------------------------
	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('Files_File');
	$f = new Files_File($UID);
	if ($f->fileName == '') { $page->do404(); }
	if (file_exists($installPath . $f->fileName) == false) { $page->do404(); }
	
	//----------------------------------------------------------------------------------------------
	//	return the file
	//----------------------------------------------------------------------------------------------
	
	header("Content-type: application/force-download"); 
	header('Content-Disposition: inline; filename="' . $f->title . '"'); 
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-length: " . filesize($installpath . $f->fileName) ); 
	header('Content-Type: application/octet-stream'); 
	header('Content-Disposition: attachment; filename="' . $f->title . '"'); 
	
	readfile($installPath . $f->fileName);
	
?>
