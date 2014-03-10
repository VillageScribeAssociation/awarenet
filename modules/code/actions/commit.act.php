<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/code/inc/code.inc.php');

//--------------------------------------------------------------------------------------------------
//*	this action accpets a file or other item for storage in the repository
//--------------------------------------------------------------------------------------------------
//post: action - action to take, 'commit' is accepted for now [string]
//post: username - username of a kapenta user with commit privileges on this package [string]
//post: password - username of a kapenta user with commit privileges on this package [string]
//post: packageUID - UID of a package on this repository [string]
//post: fileUID - location of file or directory in tree [string]
//post: path - location of file or directory in tree [string]
//post: content - file contents, base 64 encoded [string]
//post: message - commit message [string]
//post: isBinary - binary file attached? (yes|no) [string]

//+	Items are recognized by their project and relative file path.  When an item is updated its 
//+	revision field is incremented.  Posting an unrecognized item will cause it to be created.

//TODO: add a better authentication system
//TODO:	handle large binary file uploads (1MB pieces)

	$mode = 'basic';			//%	authentication mode [string]
	$return = 'xml';			//%	response type to send [string]
	$username = '';				//%	name of a kapenta user [string]
	$password = '';				//%	user's password [string]
	$privilege = 'commit';		//%	privilege required [string]
	$packageUID = '';			//%	UID of a Code_Package [string]

	$fileUID = '';				//%	UID of a Code_File object [string]
	$path = '';					//%	file name and location relative to install path [string]
	$type = '';					//%	MIME type of file [string]
	$title = '';				//%	basename of file [string]
	$content = '';				//%	file contents, base64 encoded [string]
	$description = '';			//%	short summary of file [string]
	$isBinary = 'no';			//%	set to 'yes' if binary file attached [string]
	$message = '';				//%	commit message [string]
	$size = '0';				//%	file size in bytes [string]
	$hash = '';					//%	sha1 hash of file [string]

	echo "<fail>aborted by server</fail>\n";
	die();

	//----------------------------------------------------------------------------------------------
	//	check POST vars		TODO: input sanitization here
	//----------------------------------------------------------------------------------------------
	//
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'username':		$username = $value;			break;
			case 'password':		$password = $value;			break;
			case 'privilege':		$privilege = $value;		break;
			case 'packageUID':		$packageUID = $value;		break;

			case 'fileUID':			$fileUID = $value;			break;
			case 'path':			$path = $value;				break;
			case 'title':			$title = $value;			break;
			case 'content':			$content = $value;			break;
			case 'description':		$description = $value;		break;
			case 'isBinary':		$isBinary = $value;			break;
			case 'message':			$message = $value;			break;
			case 'size':			$size = $value;				break;
			case 'hash':			$hash = $value;				break;
			case 'type':			$type = $value;				break;
		}
	}

	if (('' == $username) || ('' == $packageUID) || ('' == $password)) { echo '<fail/>'; die(); }
	if (('' == $fileUID) || ('' == $path) || ('' == $hash)) { echo '<fail/>'; die(); }

	$package = new Code_Package($packageUID);
	if (false == $package->loaded) { echo '<fail>Unknown Package.</fail>'; die(); }

	//----------------------------------------------------------------------------------------------
	//	try authenticate the user
	//----------------------------------------------------------------------------------------------
	if (false == $user->loadByName($username)) { echo '<fail>Unknown User.</fail>'; die(); }
	if (false == $user->checkPassword($password)) { echo '<fail>Wrong password.</fail>'; die(); }

	if (false == $package->hasPrivilege($user->UID, 'commit')) { 
		echo "<fail>No commit permission on this object.</fail>"; die(); 
	}

	//----------------------------------------------------------------------------------------------
	//	check if this item already exists in this project
	//----------------------------------------------------------------------------------------------
	$model = new Code_File();
	if (true == $db->objectExists('code_file', $fileUID)) {
		$model->load($fileUID);

		//------------------------------------------------------------------------------------------
		//	store a revision if text
		//------------------------------------------------------------------------------------------
		if ('yes' != $isBinary) {
			$objAry = $model->toArray();
			$objAry['fileUID'] = $model->UID;

			$revision = new Code_Revision();
			$revision->loadArray($objAry);
			$revision->save();
		}

	}

	//------------------------------------------------------------------------------------------
	//	add to current project
	//------------------------------------------------------------------------------------------
	$model->UID = $fileUID;
	$model->package = $package->UID;
	$model->parent = $package->getParentFolder($path);
	$model->path = $path;
	$model->type = $type;
	$model->title = $title;
	$model->revision = ((int)$model->revision + 1) + 1;
	$model->description = $description;
	$model->content = $content;
	$model->message = $message;
	$model->size = $size;
	$model->hash = $hash;
	$model->isBinary = $isBinary;
	//$model->fileName = '';

	$report = $model->save();
	if ('' != $report) { echo "<fail>could not save record: $report</fail>"; die(); }

	echo "<ok/>";

?>
