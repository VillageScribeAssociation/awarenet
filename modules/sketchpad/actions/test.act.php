<?php

//--------------------------------------------------------------------------------------------------
//*	temporary / test action to initialize and render canvas sketchpad app
//--------------------------------------------------------------------------------------------------
	
	$template = $kapenta->fs->get('modules/sketchpad/templates/sketchpad.template.php');

	$labels = array(
		'defaultTheme' => $kapenta->defaultTheme,
		'serverPath' => $kapenta->serverPath,
		'userUID' => $user->UID,
		'imageUrl' => 'images/s_width570/1280px-sayornis-saya2.jpg',
		'title' = 'test image scribble',
		'title64' = base64_encode('Test image scribble')
	);

	$html = $theme->replaceLabels($labels, $template);
	echo $html;

?>
