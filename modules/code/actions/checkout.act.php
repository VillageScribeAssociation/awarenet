<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	download a file from the repository
//--------------------------------------------------------------------------------------------------
//+	note that this is only used for small files (less than 1MB), a separate interface will be 
//+	developed for sending large files in pieces.  Files are sent base64 encoded, as they are stored.

	//----------------------------------------------------------------------------------------------
	//	check reference and any permissions
	//----------------------------------------------------------------------------------------------
	//TODO: implement permissions and authentication system for private / restricted packages

	if ('' == $kapenta->request->ref) { echo "<error>No reference given.</error>\n"; die(); }

	$model = new Code_File($kapenta->request->ref);
	if (false == $model->loaded) { echo "<error>Unkown file.</error>"; die(); }

	//----------------------------------------------------------------------------------------------
	//	send the file
	//----------------------------------------------------------------------------------------------
	$raw = '';
	if ('yes' == $model->isBinary) { $raw = $kapenta->fs->get($model->fileName); }
	else { $raw = $model->content; }

	header('Content-type: application/binary');
	header('Content-length: ' . strlen($raw));
	echo $raw;

?>
