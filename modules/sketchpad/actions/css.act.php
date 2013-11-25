<?php

//--------------------------------------------------------------------------------------------------
//*	action to render stechpad CSS with local absolute URLs
//--------------------------------------------------------------------------------------------------

	$labels = array(
		'serverPath' => $kapenta->serverPath
	);

	$template = $kapenta->fs->get('modules/sketchpad/assets/Sketchpad.css');	
	$css = $theme->replaceLabels($labels, $template);

	header('Content-type: text/css');
	echo $css;

?>
