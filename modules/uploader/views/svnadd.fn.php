<?

//-------------------------------------------------------------------------------------------------
//|	find all files in this project which should be tracked by subversion
//-------------------------------------------------------------------------------------------------

function admin_svnadd($args) {
	echo "admin > svnadd<br/>\n";
	global $user;
	if ($user->ofGroup != 'admin') { return ''; }

	//---------------------------------------------------------------------------------------------
	//	define which files should not be tracked by SVN
	//---------------------------------------------------------------------------------------------
	$exemptions = array(
						'setup.inc.php', 
						'uploader/',
						'data/images/'
						'data/files/'
						'data/log/'
						'.log.php'
						'/drawcache/'
						'~'
						'.svn/'
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
		foreach($exemptions as $ex) { if (strpos(' ' . $line, $ex) != false) { $skip = true; } }
		if (false == $skip) { $html .= $line . "\n"; }
	}

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------
	$html = "<textarea rows='10' cols='80'>" . $html . "</textarea>";
	return $html
}

//-------------------------------------------------------------------------------------------------

?>
