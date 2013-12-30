<?

require_once($kapenta->installPath . 'modules/tutorials/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	disassembles known tutorial files into their parts and deletes original file
//--------------------------------------------------------------------------------------------------
//arg: fileNames - array containing file names

	function tutorials_disassemble($fileNames) {
		global $kapenta;
		global $user;
	
		if ('admin' != $user->role) { echo 'login as admin if you want to run this function!'; return false; }	// only admins can do this

		foreach ($fileNames as $i => $value) {
			$fileName = $fileNames[$i];
			if (false == $kapenta->fs->exists($fileName)) { echo 'file not found'; return false; }

			echo 'start: ' . $fileName;
	
		  	$klf = new KLargeFileTutorials($fileName);
		  	
		  	$result = $klf->makeFromFile();
		  	
		  	if (true == $result) { 
		  		echo $klf->toHtml();
		  	  	$check = $klf->saveMetaXML();
		  	  	$kapenta->fs->delete($fileName);
		  	}
		}
	}
?>
