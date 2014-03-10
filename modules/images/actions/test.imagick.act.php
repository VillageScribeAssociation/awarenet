<?php

//--------------------------------------------------------------------------------------------------
//*	action to test ImageMagick indentify function
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//$fileName = 'setup.tar.gz';
	$fileName = 'data/images/c/x/i/cxikss2pxjm0hmbnnu.jpg';

	try {
		$im = new Imagick($fileName);
		$improp = $im->identifyImage();
		echo "<pre>\n";
		print_r($improp);
		echo "</pre>\n";

	} catch (Exception $e) {
		echo "not an image\n";
	}

	$reg = $kapenta->registry->search('live', 'live.file.');

	$uploadName = '/something/here/any.FLV';

	foreach($reg as $key => $value) {
		$ext = str_replace('live.file.', '', $key);
		$compare = substr($uploadName, strlen($uploadName) - strlen($ext));
		if (strtolower($ext) == strtolower($compare)) {
			$module = $value;
			$extension = $ext;
			echo "/uploadcomplete/ module: $module ext: $extension upload: " . $uploadName;
		}
	}

	if ('' == $module) {
		echo "Files of this type are not supported by %%websiteName%%.<br/>$srcName";
	}

?>
