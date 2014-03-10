<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//|	web shell command to manage package repositories (software sources)
//--------------------------------------------------------------------------------------------------

function packages_WebShell_sources($args) {
		global $kapenta;
		global $user;
		global $shell;
		global $utils;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $mode = 'noauth'; }

	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-a':			$mode = 'add';			break;
			case '-h':			$mode = 'help';			break;
			case '-l':			$mode = 'list';			break;
			case '-r':			$mode = 'remove';		break;
			case '--add':		$mode = 'add';			break;
			case '--help':		$mode = 'help';			break;
			case '--list':		$mode = 'list';			break;
			case '--remove':	$mode = 'remove';		break;
		}
	}

	$um = new KUpdateManager();

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'add':
			$url = '';
			if (true == array_key_exists(1, $args)) { $url = $args[1]; }
			if ('' != trim($url)) {
				$html .= "Adding adding software source: $url <br/>";
				$check = $um->addSource($url);
				if (true == $check) {
					$html .= "... Done<br/>";
				} else {
					$html .= "Warning: could not add software source.<br/>";
				}

			} else {
				$html .= "New source not given.<br/>";
			}
			break;	//..............................................................................

		case 'help':
			$html = packages_WebShell_sources_help();
			break;	//..............................................................................

		case 'list':
			$sources = $um->listSources();
			foreach($sources as $source) { $html .= $source . "<br/>"; }
			break;	//..............................................................................

		case 'remove':
			$url = '';
			if (true == array_key_exists(1, $args)) { $url = $args[1]; }
			if (('' != trim($url)) && ($um->hasSource($url))) {
				$html .= "Removing software source: $url <br/>";
				$check = $um->removeSource($url);
				if (true == $check) {
					$html .= "... Done<br/>";
				} else {
					$html .= "Warning: could not remove software source.<br/>";
				}

			} else {
				$html .= "Source not recognized.<br/>";
			}

			break;	//..............................................................................

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.cat command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function packages_WebShell_sources_help($short = false) {
	if (true == $short) { return "List, add or remove software sources."; }

	$html = "
	<b>usage: packages.sources <i>[mode] http://source.com/</i></b><br/>
	<br/>
	<b>[--add|-a] <i>http://source.url/</i></b><br/>
	Add a software source.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--list|-l]</b><br/>
	List software sources.<br/>
	<br/>
	<b>[--remove|-r] <i>http://source.url/</i></b><br/>
	Remove a software source.<br/>
	<br/>
	";

	return $html;
}

?>
