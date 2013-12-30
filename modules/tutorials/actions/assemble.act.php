<?

require_once($kapenta->installPath . 'modules/tutorials/inc/assemble.inc.php');

//--------------------------------------------------------------------------------------------------
//*	assembles known tutorial files into one big file again.
//--------------------------------------------------------------------------------------------------

	$fileNames = array("modules/tutorials/assets/awarenet_getting_started.mp4",
				"modules/tutorials/assets/awarenet_tutorial_2.mp4");
	tutorials_assemble($fileNames);
?>
