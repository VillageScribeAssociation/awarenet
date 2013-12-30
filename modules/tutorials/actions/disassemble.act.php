<?

require_once($kapenta->installPath . 'modules/tutorials/inc/disassemble.inc.php');

//--------------------------------------------------------------------------------------------------
//*	disassembles known tutorial files into their parts
//--------------------------------------------------------------------------------------------------

	$fileNames = array("modules/tutorials/assets/awarenet_getting_started.mp4",
				"modules/tutorials/assets/awarenet_tutorial_2.mp4");
	tutorials_disassemble($fileNames);
?>
