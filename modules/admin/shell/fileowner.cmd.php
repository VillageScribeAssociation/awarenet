<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	command to look up the owner of a shared file
//--------------------------------------------------------------------------------------------------

function admin_WebShell_fileowner($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $db;

	$mode = 'lookup';			//%	operation [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-h':				$mode = 'help';			break;
			case '--help':			$mode = 'help';			break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {

		case 'help':
			$html = admin_WebShell_fileowner_help();
			break;			

		case 'lookup':
			if (0 == count($args)) { return admin_WebShell_fileowner_help(); }
			$fileName = $args[0];

			if ($kapenta->installPath == substr($fileName, 0, strlen($kapenta->installPath))) {
				$fileName = substr($fileName, strlen($kapenta->installPath));
			}

			if ('/' == substr($fileName, 0, 1)) {
				$fileName = substr($fileName, 1);
			}

			$owner = $kapenta->fileOwner($fileName);
			if (0 == count($owner)) {
				$html .= "<span class='ajaxwarn'>Owner could not be identified.</span>";
			} else {

				$obj = $db->getObject($owner['model'], $owner['UID']);
				
				$creatorBlock = '[[:users::name::userUID=' . $obj['createdBy'] . ':]]';
				$creatorName = $theme->expandBlocks($creatorBlock);

				$present = $kapenta->fs->exists($fileName) ? 'yes' : 'no';
				$shared = $db->isShared($owner['model'], $owner['UID']) ? 'yes' : 'no';

				$fileHash = $kapenta->fs->exists($fileName) ? $kapenta->fileSha1($fileName) : 'na';
				$fileSize = $kapenta->fs->exists($fileName) ? $kapenta->fs->size($fileName) : 'na';

				$html .= ''
				 . '<pre>'
				 . 'File:    ' . $fileName . "\n"
				 . 'Module:  ' . $owner['module'] . "\n"
				 . 'Model:   ' . $owner['model'] . "\n"
				 . 'UID:     ' . $owner['UID'] . "\n"
				 . 'Created: ' . $obj['createdOn'] . "\n"
				 . 'Creator: ' . $obj['createdBy'] . " ($creatorName)\n"
				 . 'SHA1:    ' . $fileHash . "\n"
				 . 'Size:    ' . $fileSize . " bytes\n"
				 . 'Present: ' . $present . "\n"
				 . 'Shared:  ' . $shared . "\n"
				 . '</pre>';
			}
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

function admin_WebShell_fileowner_help($short = false) {
	if (true == $short) { return "Find which object owns a file."; }

	$html = "
	<b>usage: admin.fileowner [-h] <i>fileName</i></b><br/>
	<br/>
	Lists ownership information of user files.<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>Note that fileName should be relative to the installPath of this instance, and is case
	sensitive.<br/>
	<br/>
	";

	return $html;
}


?>
