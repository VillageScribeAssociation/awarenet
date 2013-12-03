<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	shell interface to the database wrapper
//--------------------------------------------------------------------------------------------------

function images_WebShell_listfiles($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $db;

	$mode = 'list';		//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-h':			$mode = 'help';		break;
			case '--help':		$mode = 'help';		break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'list':
			//TODO: implement paging
			$block = '[[:images::listfiles::format=csv:]]';
			$list = $theme->expandBlocks($block, '');
			$html .= "<pre>$list</pre>";
			break;	//..............................................................................

		case 'help':
			$html = images_WebShell_listfiles_help();
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

function images_WebShell_listfiles_help($short = false) {
	if (true == $short) { return "List images files managed by this module."; }

	$html = "
	<b>usage: images.listfiles <i>query</i></b><br/>
	Lists files maintaine by this module.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
