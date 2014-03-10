<?

//--------------------------------------------------------------------------------------------------
//*	temporary action to replace %%siteName%% in page templates
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$shellCmd = "find " . $kapenta->installPath . " -name \"*.page.php\"";
	$result = shell_exec($shellCmd);

	$lines = explode("\n", $result);
	foreach($lines as $line) { 
		$line = str_replace($kapenta->installPath, '', $line);
		$kapenta->page->load($line);
		if (false != strpos(' ' . $kapenta->page->title, 'awareNet')) {
			echo "fixing: $line <br/>\n";
			echo "from: " . htmlentities($kapenta->page->title) . "<br/>\n";
			$kapenta->page->title = str_replace('awareNet', '%%websiteName%%', $kapenta->page->title);
			echo "to: " . htmlentities($kapenta->page->title) . "<br/>\n";
			$kapenta->page->save();
		}

	}

?>
