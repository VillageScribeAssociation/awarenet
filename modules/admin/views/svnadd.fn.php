<?

//-------------------------------------------------------------------------------------------------
//|	find all files in this project which should be tracked by subversion
//-------------------------------------------------------------------------------------------------

function admin_svnadd($args) {
	global $user;
	global $installPath;

	if ('admin' != $user->role) { return false; }

	//---------------------------------------------------------------------------------------------
	//	define which files should not be tracked by SVN
	//---------------------------------------------------------------------------------------------
	$exemptions = array(
						'setup.inc.php', 
						'uploader/',
						'data/images/',
						'data/files/',
						'data/log/',
						'data/temp/',
						'.log.php',
						'svnadd.sh',
						'svndelete.sh',
						'/drawcache/',
						'~',
						'.svn',
						'install/',
						'tmp.xml'
						);

	//---------------------------------------------------------------------------------------------
	//	find all files in this project
	//---------------------------------------------------------------------------------------------
	$html = '';
	$svnfiles = '';
	$skipfiles = '';

	$raw = shell_exec("find $installPath");
	$lines = explode("\n", $raw);
	foreach($lines as $line) {		
		$skip = false;
		$relLine = str_replace($installPath, '', $line);
		foreach($exemptions as $ex) { if (strpos(' ' . $line, $ex) != false) { $skip = true; } }
		if (trim($relLine) == '') { $skip = true; }
		if (false == $skip) { 
			$svnfiles .= 'svn add ' . $relLine . "\n"; 
		} else {
			if ((trim($relLine) != '') && (strpos($line, '.svn') == false)) { 
				$skipfiles .= 'svn delete ' . $relLine . "\n"; 
			}
		}
	}

	//---------------------------------------------------------------------------------------------
	//	save to svnadd installPath
	//---------------------------------------------------------------------------------------------
	$scriptFile = $installPath . 'svnadd.sh';
	$fh = fopen($scriptFile, 'w+');
	fwrite($fh, $svnfiles);
	fclose($fh);

	$html .= "The following files should be managed by subversion:<br/>";
	$html .= "<small>This list has been saved to $scriptFile</small><br/>";
	$html .= "<textarea rows='17' cols='80'>" . $svnfiles . "</textarea>";
	$html .= "<br/><br/>";

	//---------------------------------------------------------------------------------------------
	//	make explicit list of files which should not be tacked by svn
	//---------------------------------------------------------------------------------------------
	$scriptFile = $installPath . 'svndelete.sh';
	$fh = fopen($scriptFile, 'w+');
	fwrite($fh, $skipfiles);
	fclose($fh);

	$html .= "The following files should not be managed by SVN:<br/>";
	$html .= "<small>This list has been saved to $scriptFile</small><br/>";
	$html .= "<textarea rows='17' cols='80'>" . $skipfiles . "</textarea>";
	$html .= "<br/><br/>";

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------
	return $html;

}

//-------------------------------------------------------------------------------------------------
?>

