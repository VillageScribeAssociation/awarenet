<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	action to update a package on the repository
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'commit' [string]
//postarg: UID - UID of an installed package [string]
//postarg: message - commit message [string]
//TODO: error messages working with progressive HTML page

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not supplied'); }
	if ('commit' != $_POST['action']) { $page->do404('Action not supported'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given'); }
	if (false == array_key_exists('message', $_POST)) { $page->do404('message not given'); }

	$package = new KPackage($_POST['UID']);
	$um = new KUpdateManager();

	$message = trim($_POST['message']);
	if ('' == $message) {
		$session->msg('No commit message, not updating repository.', 'bad');
		$page->do302('packages/show/' . $package->UID);
	}

	$ext = $package->extArray();
	if ('' == $ext['username']) {
		$session->msg('No credentials, cannot update repository.', 'bad');
		$page->do302('packages/show/' . $package->UID);		
	}

	//----------------------------------------------------------------------------------------------
	//	start HTML output
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader::title=Commit Package:]]', ''); flush();
	echo "<h1>Committing package: " . $package->name . " (" . $package->UID . ")</h1>";
	$um->log('Repository: ' . $package->source);

	//----------------------------------------------------------------------------------------------
	//	get list of files which are different to manifest's versions
	//----------------------------------------------------------------------------------------------
	$toCommit = $package->getLocalDifferent();

	if (0 == count($toCommit)) {
		$um->log('No changes to commit.', 'red');
		echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
		die();	
	}

	//----------------------------------------------------------------------------------------------
	//	test credentials
	//----------------------------------------------------------------------------------------------
	$um->log('Checking commit privilege on repository.');		
	
	if (true == $package->testCredentials('commit')) {
		$um->log('<b>Auth:</b> Credentials accepted by repository.', 'green');		
	} else {
		if ('' == $result) {
			$um->log('<b>Fail:</b> Could not connect to repository.', 'red');
		} else {
			$um->log('<b>Fail:</b> Credentials not accepted by repository.', 'red');
		}
		echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	post any changes to package metadata
	//----------------------------------------------------------------------------------------------
	$check = $package->postToRepository();

	if (true == $check) {
		$manifestLink = "<a href='" . $package->manifestUrl . "'>" . $package->manifestUrl . "</a>";
		$um->log('Package updated on repository:<br/>' . $manifestLink, 'green');

	} else {
		$msg = 'Could not update package metadata on repository, '
			 . 'please check credentials and network connection.';

		$um->log($msg, 'red');
		echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	post changelog message
	//----------------------------------------------------------------------------------------------
	$clFiles = '';
	foreach($toCommit as $uid => $file) { $clFiles .= $file['path'] . "\n"; }
	$check = $package->postChangelogMessage($message, $clFiles);

	if (true == $check) {
		$clLink = "<a href='" . $package->source . "'>" . $package->source . "</a>";
		$um->log('Changelog: ' . $clLink . '<br/><b>Message:</b> ' . $message, 'green');
		$um->log('<b>Affected files:</b><br/>' . str_replace("\n", "<br/>\n", $clFiles));

	} else {
		$msg = 'Could not update changelog on repository, '
			 . 'please check credentials and network connection.';

		$um->log($msg, 'red');
		echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	send the files
	//----------------------------------------------------------------------------------------------
	$toRetry = array();

	foreach($toCommit as $lf) {
		$description = $package->getFileDescription($lf['path'], $lf['type']);
		if ('' == $description) { $description = "<span class='ajaxerror'>none</span>"; }

		$msg = 'Commiting: ' . $lf['path'] . "<br/>\n"
			 . 'Hash: ' . $lf['hash'] . "<br/>\n"
			 . 'Size: ' . $lf['size'] . "<br/>\n"
			 . 'Type: ' . $lf['type'] . "<br/>\n"
			 . 'Description: ' . $description . "<br/>\n";

		$check = $package->commit($lf, strip_tags($description), $message);

		if (true == $check) {
			$um->log($msg, 'green');
		} else {
			$um->log($msg, 'red');
			$um->log('Commit failed, will retry.', 'black');
			$toRetry[] = $lf;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	retry any which failed
	//----------------------------------------------------------------------------------------------
	$toRetryAgain = array();

	foreach($toRetry as $lf) {
		$description = $package->getFileDescription($lf['path'], $lf['type']);
		if ('' == $description) { $description = "<span class='ajaxerror'>none</span>"; }

		$msg = 'Commiting: ' . $lf['path'] . " (retry 1)<br/>\n"
			 . 'Hash: ' . $lf['hash'] . "<br/>\n"
			 . 'Size: ' . $lf['size'] . "<br/>\n"
			 . 'Type: ' . $lf['type'] . "<br/>\n"
			 . 'Description: ' . $description . "<br/>\n";

		$check = $package->commit($lf, strip_tags($description), $message);

		if (true == $check) {
			$um->log($msg, 'green');
		} else {
			$um->log($msg, 'red');
			$um->log('Commit failed, will retry again.', 'black');
			$toRetryAgain[] = $lf;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	retry again any which failed
	//----------------------------------------------------------------------------------------------
	$failedFiles = array();

	foreach($toRetryAgain as $lf) {
		$description = $package->getFileDescription($lf['path'], $lf['type']);
		if ('' == $description) { $description = "<span class='ajaxerror'>none</span>"; }

		$msg = 'Commiting: ' . $lf['path'] . " (retry 1)<br/>\n"
			 . 'Hash: ' . $lf['hash'] . "<br/>\n"
			 . 'Size: ' . $lf['size'] . "<br/>\n"
			 . 'Type: ' . $lf['type'] . "<br/>\n"
			 . 'Description: ' . $description . "<br/>\n";

		$check = $package->commit($lf, strip_tags($description), $message);

		if (true == $check) {
			$um->log($msg, 'green');
		} else {
			$um->log($msg, 'red');
			$um->log('Commit failed, will retry again.', 'black');
			$failedFiles[] = $lf;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	report on any files which failed
	//----------------------------------------------------------------------------------------------
	//TODO: this

	//----------------------------------------------------------------------------------------------
	//	update our copy of the manifest
	//----------------------------------------------------------------------------------------------

	$check = $package->updateFromRepository();

	if (true == $check) {
		$um->log('Updated local manifest from repository.');

	} else {
		$msg = 'Could not update manifest.';
		$um->log($msg, 'red');
		echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', ''); 
		die();
	}

?>
