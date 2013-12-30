<?

require_once($kapenta->installPath . 'modules/tutorials/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	assemble known tutorial files from parts
//--------------------------------------------------------------------------------------------------
//arg: fileNames - array containing file names

	function tutorials_assemble($fileNames) {
		global $kapenta;
		global $user;
	
		if ('admin' != $user->role) { echo 'login as admin if you want to run this function!'; return false; }	// only admins can do this

		foreach ($fileNames as $i => $value) {
			$fileName = $fileNames[$i];
		  	$klf = new KLargeFileTutorials($fileName);
		  	echo $klf->toHtml();
		   	$result = $klf->stitchTogether();
		}
	}
?>
