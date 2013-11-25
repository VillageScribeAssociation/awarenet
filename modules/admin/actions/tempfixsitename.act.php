<?

//--------------------------------------------------------------------------------------------------
//*	temporary action to replace %%siteName%% in page templates
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$shellCmd = "find " . $kapenta->installPath . " -name \"*.page.php\"";
	$result = shell_exec($shellCmd);

	$lines = explode("\n", $result);
	foreach($lines as $line) { 
		$line = str_replace($kapenta->installPath, '', $line);
		$kapenta->page->load($line);
		if (false != strpos(' ' . $page->title, 'awareNet')) {
			echo "fixing: $line <br/>\n";
			echo "from: " . htmlentities($page->title) . "<br/>\n";
			$page->title = str_replace('awareNet', '%%websiteName%%', $page->title);
			echo "to: " . htmlentities($page->title) . "<br/>\n";
			$page->save();
		}

	}

?>
