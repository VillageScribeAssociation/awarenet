<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	remove a file or directory
//--------------------------------------------------------------------------------------------------

function live_WebShell_rm($args) {
	global $kapenta, $user, $shell;

	if ('admin' != $user->role) { return '(403)'; }

	$mode = 'del';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'del':
			if (false == array_key_exists(0, $args)) { return live_WebShell_cat_help(); }			
			$fileName = substr($shell->get('cwd'), 1) . $args[0];
			if (false == $kapenta->fs->exists($fileName)) { return 'File not found: ' . $fileName; }
			$check = false;
			if (true == is_dir($kapenta->installPath . $fileName)) {
				$html .= "Deleting directory: $fileName<br/>";			//	is a directory
				$check = $kapenta->fileRmDir($fileName);
			} else {
				$html .= "Deleting file: $fileName<br/>";				// is a file
				$check = $kapenta->fileDelete($fileName, false);
			}

			if (false == $check) {
				$html .= "<span class='ajaxerror'>Could not remove $fileName</span>";
			} else {
				$html .= "<span class='ajaxwarn'>Deleted $fileName</span>";
			}
			

			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_rm_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.rm command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_rm_help($short = false) {
	if (true == $short) { return "List files in working directory."; }

	$html = "
	<b>usage: live.rm <i>[\"filename.ext\"]</i></b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
