<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/ksource.class.php');

//--------------------------------------------------------------------------------------------------
//*	add a package / install on this instance
//--------------------------------------------------------------------------------------------------
//post: action - set to addPackage [string]
//post: UID - UID of package on repository [string]
//post: username - respository username [string]
//post: password - repository password [string]

//TODO: move most of this to KUpdatemanager object

	//----------------------------------------------------------------------------------------------
	//	check post vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$UID = '';				//%	UID of package [string]
	$username = '';			//%	username on repository [string]
	$password = '';			//%	password on repository [string]

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not given.', true); }
	if ('addPackage' != $_POST['action']) { $kapenta->page->do404('Action not recognized.'); }

	if (true == array_key_exists('source', $_POST)) { $source = $_POST['source']; }
	if (true == array_key_exists('UID', $_POST)) { $UID = $_POST['UID']; }
	if (true == array_key_exists('username', $_POST)) { $username = $_POST['username']; }
	if (true == array_key_exists('password', $_POST)) { $username = $_POST['password']; }

	if ('' == trim($UID)) { $kapenta->page->do404('UID not given.'); }

	$um = new KUpdateManager();
	$package = new KPackage($UID);
	$package->username = $username;
	$package->password = $username;

	$source = $package->source;

	//----------------------------------------------------------------------------------------------
	//	start HTML output
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader::title=Install Package:]]', ''); flush();
	echo "<h1>Installing package: " . $package->name . " ($UID)</h1>";

	//----------------------------------------------------------------------------------------------
	//	add the source
	//----------------------------------------------------------------------------------------------
	if (false == $um->hasSource($source)) {
		$um->log('Adding source: ' . $source, 'green');
		$um->addSource($source);
	}	

	//----------------------------------------------------------------------------------------------
	//	initialize in registry
	//----------------------------------------------------------------------------------------------
	$um->setPackageField($UID, 'source', $source);
	$um->setPackageField($UID, 'name', 'unknown');
	$um->setPackageField($UID, 'status', 'manual');
	$um->setPackageField($UID, 'user', $username);
	$um->setPackageField($UID, 'user', $password);

	//----------------------------------------------------------------------------------------------
	//	try to download the package manifest
	//----------------------------------------------------------------------------------------------
	$um->log("<b>Repository:</b> $source"); 
	$um->log("Downloading package manifest...");
	
	$check = $package->updateFromRepository();

	if (false == $check) {
		$msg ="<b>Could not download package manifest:</b><br/>" . $package->manifestUrl . '<br/>';
		$um->log($msg, 'red');

		if (false == $package->loaded) {
			$um->log("Module not installed, please check details and retry.<br/>", 'red');
			echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
			die();
		}
	} 

	//----------------------------------------------------------------------------------------------
	//	manifest loaded and saved, display file list
	//----------------------------------------------------------------------------------------------
	echo "<div class='chatmessagegreen'>\n";
	foreach($package->files as $file) { echo $file['path'] . "<br/>\n"; }
	echo "</div>\n";	
	$um->log("Downloaded package manifest.<br/>" . count($package->files) . ' files.', 'green');

	//----------------------------------------------------------------------------------------------
	//	try download all outstanding files
	//----------------------------------------------------------------------------------------------
	$allOk = true;

	foreach($package->files as $file) {
		$msg = ''
			. '<b>file:</b> ' . $file['path'] . '<br/>'
			. '<b>sha1:</b> ' . $file['hash'] . '<br/>'
			. '<b>type:</b> ' . $file['type'] . '<br/>'
			. '<b>size:</b> ' . $file['size'] . '<br/>';

		if (false == file_exists($kapenta->installPath . $file['path'])) {
			$check = $package->updateFile($file['uid']);
			if (true == $check) { $um->log("Downloaded: " . $file['path'], 'green'); }
			else { $um->log("Download failed: " . $file['path'], 'red'); }

		} else {
			if ($kapenta->fileSha1($file['path']) == $file['hash']) {
				$msg .= "file exists, matches hash...<br/>";
				echo "<div class='chatmessageblack'>" . $msg . "</div>";
			} else {
				$check = $package->updateFile($file['uid']);
				if (true == $check) { $um->log("Updated: " . $file['path'], 'green'); }
				else { $um->log("Download failed: " . $file['path'], 'red'); }
			}
		}
	}

	if (false == $allOk) {
		$um->log("Some files could not be downloaded, aborting installation...", 'red');
		echo "<h2>Done.</h2>";
		echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	call install function
	//----------------------------------------------------------------------------------------------
	if ('' != $package->installFile) {
		if (true == $kapenta->fs->exists($package->installFile)) {
			//--------------------------------------------------------------------------------------
			//	install script present, include it
			//--------------------------------------------------------------------------------------
			require_once($kapenta->installPath . $package->installFile);
				
			if (true == function_exists($package->installFn)) {
				//----------------------------------------------------------------------------------
				//	install function present, run it
				//----------------------------------------------------------------------------------
				$fnName = $package->installFn;
				$report = $fnName();

				if (false == strpos($report, '<!-- error -->')) {
					//------------------------------------------------------------------------------
					// install script reports no errors
					//------------------------------------------------------------------------------
					echo "<div class='chatmessagegreeen'>$report</div>";

				} else {
					//------------------------------------------------------------------------------
					// install errors, abort
					//------------------------------------------------------------------------------
					echo "<div class='chatmessagered'>$report</div>"
						. "<div class='chatmessagered'>Install script reportes errors,"
						. " aborting installation.</div>"
						. "<h2>Done.</h2>"
						. $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');
					die();

				}

			} else {
				//----------------------------------------------------------------------------------
				//	install function missing
				//----------------------------------------------------------------------------------
				echo "<div class='chatmessagered'>Install function missing, "
					. "aborting installation.</div>"
					. "<h2>Done.</h2>"
					. $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
				die();
			}


		} else {
			//--------------------------------------------------------------------------------------
			//	install script missing
			//--------------------------------------------------------------------------------------
			echo "<div class='chatmessagered'>Install file missing, aborting installation.</div>"
				. "<h2>Done.</h2>"
				. $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
			die();
		}

	} else {
		//------------------------------------------------------------------------------------------
		//	no install script defined for this package
		//------------------------------------------------------------------------------------------
		echo "<div class='chatmessageblack'>This package does not have an install file.</div>";	
	}

	//----------------------------------------------------------------------------------------------
	//	set registry keys
	//----------------------------------------------------------------------------------------------
	if (true == $allOk) {
		$um->setPackageField($package->UID, 'name', $package->name);
		$um->setPackageField($package->UID, 'status', 'installed');
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	echo "<h2>Package installed.</h2>";
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 

?>
