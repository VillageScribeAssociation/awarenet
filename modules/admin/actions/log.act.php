<?

//--------------------------------------------------------------------------------------------------
//*	display a single log file
//--------------------------------------------------------------------------------------------------

	$fileName = '';
	$format = 'html';

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404(); }

	$fileName = 'data/log/' . $req->ref;
	if (false == $kapenta->fileExists($fileName)) { $page->do404('No such log file.'); }
	
	if (true == array_key_exists('format', $req->args)) { $format = $req->args['format']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	switch(strtolower($format)) {
		case 'xml':
			header('Content-type: application/xml');
			$raw = $kapenta->fileGetContents($fileName, true, true);
			$startEntries = strpos($raw, '?>') + 2;
			$raw = substr($raw, $startEntries);
			echo "<log file='" . $req->ref . "'>\n" . $raw . "</log>";
			break;

		case 'html':
			$page->load('modules/admin/actions/log.page.php');
			$page->render();
			break;

		default:
			$page->do403();
			break;
	}


?>
