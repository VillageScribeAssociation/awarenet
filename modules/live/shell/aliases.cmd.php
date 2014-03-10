<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	print aliases table
//--------------------------------------------------------------------------------------------------

function live_WebShell_aliases($args) {
		global $kapenta;
		global $user;

	$mode = 'list';			//%	operation [string]
	$html ='';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	foreach ($args as $idx => $arg) {
		switch($arg) {
			case '-c':	$args[$idx] = '--clear';	break;
			case '-n':	$args[$idx] = '--new';		break;
			case '-d':	$args[$idx] = '--delete';	break;
			case '-h':	$args[$idx] = '--help';		break;
			case '-r':	$args[$idx] = '--reset';	break;
		}
	}

	if (true == in_array('--clear', $args)) {
		if ('admin' == $user->role) { $mode = 'clear'; } else { $mode = 'noauth'; }
	}

	if (true == in_array('--new', $args)) {
		if ('admin' == $user->role) { $mode = 'new'; } else { $mode = 'noauth'; }
	}

	if (true == in_array('--reset', $args)) {
		if ('admin' == $user->role) { $mode = 'reset'; } else { $mode = 'noauth'; }
	}

	if (true == in_array('--help', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'list':
			$aliases = new Live_CmdAliases();
			$html = $aliases->toHtml();
			break;	//..............................................................................

		case 'clear':
			$aliases = new Live_CmdAliases();
			$aliases->clear();
			$html = "Clearing aliases table... done<br/>"
				  . "Re-adding default aliases... done<br/>";
			break;	//..............................................................................

		case 'new':
			$aliases = new Live_CmdAliases();
			$alias = '';
			$canonical = '';

			if ('--new' != $args[0]) { return '--new must be first argument'; }
			if (true == array_key_exists(1, $args)) { $alias = $args[1]; }
			if (true == array_key_exists(2, $args)) { $canonical = $args[2]; }

			if (('' == trim($alias)) || ('' == trim($canonical))) 
				{ return 'Missing arrgument.'; }

			if (false == $kapenta->shellCmdExists($canonical)) 
				{ return 'Invalid argument: canonical must be a valid command.'; }

			if (true == $kapenta->shellCmdExists($alias)) 
				{ return 'Invalid argument: alias cannot be a canonical command.'; }			

			$aliases->add($alias, $canonical);
			$html = "Added new alias: $alias := $canonical";

			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_aliases_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

		case 'reset':
			$aliases = new Live_CmdAliases();
			$html .= $aliases->loadDefault();
			$html .= $aliases->toHtml();
			break;	//..............................................................................
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.aliases command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_aliases_help($short = false) {
	if (true == $short) { return "Display, add, remove and clear command aliases."; }

	$html = "
	<b>usage: live.aliases <i>[mode] [alias] [canonical]</i></b><br/>
	<br/>
	<b>[--clear|-c]</b><br/>
	Recreates aliases table with only default items.  Only administrators can do this.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--list|-l]</b><br/>
	Displays list of all aliases.  This is the default mode.<br/>
	<br/>
	<b>[--new|-n] <i>alias canonical.command</i></b><br/>
	Creates a new alias and adds it to the list.  Only administrators can do this.<br/>
	example: <tt>live.aliases --new listcommands live.help</tt><br/>
	<br/>
	<b>[--reset|-r]</b><br/>
	Loads all default aliases from /modules/[module]/conf/aliases.txt files.<br/>
	<br/>
	";

	return $html;
}


?>
