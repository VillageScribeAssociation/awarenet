<?

//-------------------------------------------------------------------------------------------------
//	find all files in this project which should be tracked by subversion
//-------------------------------------------------------------------------------------------------

function admin_svnadd($args) {
	global $user;
	global $installPath;

	if ($user->data['ofGroup'] != 'admin') { return false; }

	//---------------------------------------------------------------------------------------------
	//	define which files should not be tracked by SVN
	//---------------------------------------------------------------------------------------------
	$exemptions = array(
						'setup.inc.php', 
						'uploader/',
						'data/images/',
						'data/files/',
						'data/log/',
						'.log.php',
						'svnadd.sh',
						'/drawcache/',
						'~',
						'.svn',
						'install/'
						);

	//---------------------------------------------------------------------------------------------
	//	find all files in this project
	//---------------------------------------------------------------------------------------------
	$html = '';
	$raw = shell_exec("find $installPath");
	$lines = explode("\n", $raw);
	foreach($lines as $line) {		
		$skip = false;
		$relLine = str_replace($installPath, '', $line);
		foreach($exemptions as $ex) { if (strpos(' ' . $line, $ex) != false) { $skip = true; } }
		if (trim($relLine) == '') { $skip = true; }
		if (false == $skip) { $html .= 'svn add ' . $relLine . "\n"; }
	}

	//---------------------------------------------------------------------------------------------
	//	save to installPath
	//---------------------------------------------------------------------------------------------
	$scriptFile = $installPath . 'svnadd.sh';
	$fh = fopen($scriptFile, 'w+');
	fwrite($fh, $html);
	fclose($fh);

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------
	$html = "<textarea rows='10' cols='80'>" . $html . "</textarea>";
	$html .= "This list has been saved to $scriptFile.<br/>";
	return $html;

}

//-------------------------------------------------------------------------------------------------
?>
