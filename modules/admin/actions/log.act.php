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
	if ('' == $kapenta->request->ref) { $page->do404(); }

	$fileName = 'data/log/' . $kapenta->request->ref;
	if (false == $kapenta->fs->exists($fileName)) { $page->do404('No such log file.'); }
	
	if (true == array_key_exists('format', $kapenta->request->args)) { $format = $kapenta->request->args['format']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	switch(strtolower($format)) {
		case 'xml':
			header('Content-type: application/xml');
			$raw = $kapenta->fs->get($fileName, true, true);
			$startEntries = strpos($raw, '?>') + 2;
			$raw = substr($raw, $startEntries);
			echo "<log file='" . $kapenta->request->ref . "'>\n" . $raw . "</log>";
			break;

		case 'html':
			$kapenta->page->load('modules/admin/actions/log.page.php');
			$kapenta->page->blockArgs['logFile'] = $fileName;
			$kapenta->page->render();
			break;

		default:
			$page->do403();
			break;
	}


?>
