<?

//--------------------------------------------------------------------------------------------------
//	load a file and return to browser as a download
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the file record
	//----------------------------------------------------------------------------------------------
	require_once($installPath . 'modules/files/models/files.mod.php');
	if ($request['ref'] == '') { do404(); }
	raFindRedirect('files', 'dn', 'files', $request['ref']);
	$f = new File($request['ref']);
	if ($f->data['fileName'] == '') { do404(); }
	if (file_exists($installPath . $f->data['fileName']) == false) { do404(); }
	
	//----------------------------------------------------------------------------------------------
	//	return the file
	//----------------------------------------------------------------------------------------------
	
	header("Content-type: application/force-download"); 
	header('Content-Disposition: inline; filename="' . $f->data['title'] . '"'); 
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-length: " . filesize($installpath . $f->data['fileName']) ); 
	header('Content-Type: application/octet-stream'); 
	header('Content-Disposition: attachment; filename="' . $f->data['title'] . '"'); 
	
	readfile($installPath . $f->data['fileName']);
	
?>
