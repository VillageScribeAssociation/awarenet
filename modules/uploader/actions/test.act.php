<?

//--------------------------------------------------------------------------------------------------
//	code to test repository.mod.php
//--------------------------------------------------------------------------------------------------

	//if ($user->data['ofGroup'] != 'admin') { do403(); }
	require_once($installPath . 'modules/uploader/models/repository.mod.php');

	//----------------------------------------------------------------------------------------------
	//	set up repository access
	//----------------------------------------------------------------------------------------------

	$list = 'http://kapenta.co.za/code/projectlist/project_106665425118526027';
	$post = 'http://kapenta.co.za/code/postfile/';
	$key = '66awarenet99';

	$repository = new CodeRepository($list, $post, $key);

	$repository->addExemption("setup.inc.php");				// dynamically generated on install
	$repository->addExemption("uploader/");					// this module (CONTAINS KEY)
	$repository->addExemption("install/");					// defunct

	for ($i = 0; $i < 9; $i++) {
		$repository->addExemption("data/images/" . $i);		// user images
		$repository->addExemption("data/files/" . $i);		// user files
	}

	$repository->addExemption("data/log/e");				// ?
	$repository->addExemption(".log.php");					// log files

	$repository->addExemption("~");							// gedit revision files
	$repository->addExemption("/drawcache/");				// dynamically generated images

	//----------------------------------------------------------------------------------------------
	//	get list of files from respoitory and local drive
	//----------------------------------------------------------------------------------------------

	$repositoryList = $repository->getRepositoryList();
	echo "<h2>Repository List</h2>\n";
	echo $repository->listToHtml($repositoryList);

	$localList = $repository->getLocalList($repositoryList);
	echo "<h2>Local List</h2>\n";
	echo $repository->listToHtml($localList);

	$uploadList = $repository->makeUploadList($repositoryList, $localList);
	echo "<h2>To Be Uploaded</h2>\n";
	echo $repository->listToHtml($localList);

?>
