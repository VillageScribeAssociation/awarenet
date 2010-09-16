<?

//-------------------------------------------------------------------------------------------------
//*	list all files on this server which can be synced
//-------------------------------------------------------------------------------------------------
//	status might be all, missing or present
//	format may be xml, csv or html

	//---------------------------------------------------------------------------------------------
	//	authorization
	//---------------------------------------------------------------------------------------------
	//TODO

	//---------------------------------------------------------------------------------------------
	//	get request arguments
	//---------------------------------------------------------------------------------------------
	$format = 'xml';
	$status = 'present';	

	if (array_key_exists('status', $req->args)) { $status = $req->args['status']; }
	if (array_key_exists('format', $req->args)) { $format = $req->args['format']; }

	//---------------------------------------------------------------------------------------------
	//	get file lists from all modules which implement listfiles
	//---------------------------------------------------------------------------------------------

	$output = '';

	$mods = $kapenta->listModules();
	foreach($mods as $mod) {
		$block = "[[:" . $mod . "::listfiles::status=" . $status . "::format=" . $format . ":]]";
		$output .= $theme->expandBlocks($block, '');
	}

	if ('xml' == $format) { $output = "<?xml version=\"1.0\"?>\n<files>\n$output</files>\n"; }

	echo $output;

?>
