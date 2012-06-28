<?php

//--------------------------------------------------------------------------------------------------
//*	search entire codebase
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	auth
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	print search form
	//----------------------------------------------------------------------------------------------

	$searchq = '';
	$excludeq = '';

	if (true == array_key_exists('search', $_POST)) { $searchq = stripslashes($_POST['search']); }
	if (true == array_key_exists('exclude', $_POST)) { $excludeq = stripslashes($_POST['exclude']); }

	echo "
    <form name='searchCode' method='POST' action='/admin/searchcode/'>
	<b>Search: <input type='text' name='search' size='20' value='$searchq' /></b>
	<b>Exclude: <input type='text' name='exclude' size='20' value='$excludeq' /></b>
	<input type='submit' value='search' />
	</form>
	<hr/>
	";

	//----------------------------------------------------------------------------------------------
	//	do the search (if any)
	//----------------------------------------------------------------------------------------------
	if ('' != $searchq) {

		//------------------------------------------------------------------------------------------
		//	get all files
		//------------------------------------------------------------------------------------------
		$lines = scaffold_scanDirectory($kapenta->installPath);
		$files = array();
		foreach($lines as $line) {
			$ok = true;
			if (strpos($line, '.xml.php') != false) { $ok = false; }
			if (strpos($line, '.log.php') != false) { $ok = false; }
			if (strpos($line, '.page.php') != false) { $ok = false; }
			if (strpos($line, '.block.php') != false) { $ok = false; }
			if (strpos($line, '.template.php') != false) { $ok = false; }
			if (strpos($line, 'ore/removed/') != false) { $ok = false; }

			/*
			if (strpos($line, 'mysql.inc.php') != false) { $ok = false; }
			if (strpos($line, '.class.php') != false) { $ok = false; }
			if (strpos($line, '/core/') != false) { $ok = false; }
			if (strpos($line, 'upgradek2.act.php') != false) { $ok = false; }
			if (strpos($line, 'user.mod.php') != false) { $ok = false; }
			*/

			if (true == $ok) { $files[] = $line; }
		}

		//------------------------------------------------------------------------------------------
		//	find constructions needing replacements
		//------------------------------------------------------------------------------------------
		foreach($files as $file) {
		  if ('' != trim($file)) {	
			$raw = implode(file($file));
			if (strpos($raw, $searchq) != false) {
				echo "<h2>" . $file . "</h2>\n"; 
				$lines = explode("\n", $raw);
				foreach($lines as $lineNo => $line) {
					$line = " " . $line;
					if (strpos($line, $searchq) != false) {
						if ('' == $excludeq) {
							echo "line $lineNo: " . htmlentities($line) . " ($searchq)<br/>\n"; flush();
						} else {
							if (false == strpos($line, $excludeq)) {
								echo "line $lineNo: " . htmlentities($line) . " ($searchq)<br/>\n"; flush();
							}
						}
					}
				}

			}
		  }
		}
	}
	
	//---------------------------------------------------------------------------------------------
	//	depth first search
	//---------------------------------------------------------------------------------------------

	function scaffold_scanDirectory($dirName) {
		if (false != strpos($dirName, ".svn")) { return false; }
		$subDirs = array();
		$phpFiles = array();
		
		$items = scandir($dirName, 1);
		if (false == $items) { return false; }		// directory could not be read
		
		$discard = array_pop($items);	// pop '.' off the end of the array
		$discard = array_pop($items);	// pop '..' off the end of the array
		
		foreach($items as $item) {
			$fileName = $dirName . '/' . $item;		
			if (true == is_dir($fileName)) { $subDirs[] = $fileName; } 
			else { if ('php.' == substr(strrev($item), 0, 4)) { $phpFiles[] = $fileName; } }
		}
		
		foreach($subDirs as $subDir) {
			$moreFiles = scaffold_scanDirectory($subDir);
			if (true == is_array($moreFiles)) {
				foreach($moreFiles as $item) { $phpFiles[] = $item; }
			}
			//echo "Recurse $subDir\r\n";
		}
		return $phpFiles;
	}

?>
