<?

//--------------------------------------------------------------------------------------------------
//|	display admin controls for all modules which provide them
//--------------------------------------------------------------------------------------------------
//+	NOTE: modules may implement an 'adminconsole' block, but it's not compulsory

//role: admin - only administrators may use this
//opt: tb - title box (yes|nav|no) [string]
//opt: title - title of nav box, if present [string]

function admin_moduleconsole($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	$tb = 'yes';							//%	wrap in nav box [string]
	$title = 'Administration Console';		//%	default title [string]
	$html = '';								//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('tb', $args)) { $ntb = $args['tb']; }
	if (true == array_key_exists('title', $args)) { $title = $args['title']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$mods = $kapenta->listModules();
	foreach ($mods as $moduleName) {
		$block = $theme->expandBlocks("[[:" . $moduleName . "::adminconsole:]]", '');
		if ($block != '') {	$html .= "<h2>$moduleName</h2>\n$block\n<hr/>\n"; }
	}

	if ('yes' == $tb) { $html = $theme->tb($html, $title, 'divAdminConsole', 'show'); }
	if ('nav' == $tb) { $html = $theme->ntb($html, $title, 'divAdminConsoleNav', 'show'); }

	return $html;
}

?>

