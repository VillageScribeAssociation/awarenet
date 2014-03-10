<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	commands for managing kapenta modules
//--------------------------------------------------------------------------------------------------
//TODO:	re/install modules
//TODO:	get module status report

function admin_WebShell_modules($args) {
		global $kapenta;
		global $user;
		global $shell;
		global $theme;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-h':				$mode = 'help';			break;
			case '-l':				$mode = 'list';			break;
			case '-s':				$mode = 'status';		break;
			case '-i':				$mode = 'install';		break;
			case '-e':				$mode = 'everything';	break;
			case '--help':			$mode = 'help';			break;
			case '--list':			$mode = 'list';			break;
			case '--status':		$mode = 'status';		break;
			case '--everything':	$mode = 'everything';	break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'everything':
			//--------------------------------------------------------------------------------------
			//	reinstall all modules
			//--------------------------------------------------------------------------------------
			$mods = $kapenta->listModules();
			foreach ($mods as $mod) {
				$html .= "<b>Reinstalling: $mod </b><br/>";
				$model = new KModule($mod);
				if (true == $model->loaded) { 
					$html .= $model->install();		
				} else {
					$html .= "Could not load module: $mod <br/>";
				}
			}

			break;	//..............................................................................

		case 'list':
			$html = $theme->expandBlocks("[[:admin::listmodulesnav:]]", '');
			//TODO: fix this so links work
			break;	//..............................................................................

		case 'status':
			if (false == array_key_exists(1, $args)) { return live_WebShell_cd_help(); }
			if (false == $kapenta->moduleExists($args[1])) { return 'Unkown module.'; }
			$block = "[[:admin::installstatusreport::modulename=" . $args[1] . ":]]";
			$html .= $theme->expandBlocks($block, '');
			break;			

		case 'help':
			$html = live_WebShell_cd_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.aliases command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_modules_help($short = false) {
	if (true == $short) { return "List, manage and install Kapenta modules."; }

	$html = "
	<b>usage: admin.modules [-h|-l][</b><br/>
	<br/>
	<b>[--list|-l]</b><br/>
	Lists all kapenta modules present on this instance.  This is the default operation.<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--status|-s] <i>modulename</i></b><br/>
	Displays the full install status of a module.<br/>
	<br/>
	";

	return $html;
}


?>
