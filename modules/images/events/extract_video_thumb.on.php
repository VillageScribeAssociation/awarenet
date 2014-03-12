<?php

//--------------------------------------------------------------------------------------------------
//|	fired when a video has been attached, and we need a thumbnail for it
//--------------------------------------------------------------------------------------------------
//arg: refModule - module which owns this video (always 'videos' thus far) [string]
//arg: refModel - type of object (always 'videos_video' thus far) [string]
//arg: refUID - UID of owner object (ie, UID of the video) [string]
//arg: fileName - location of video file relative to installPath [string]
//opt: title - video title, for naming thumbnail [string]

function images__cb_extract_video_thumb($args) {
	global $kapenta;	
	global $kapenta;
	global $session;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('fileName', $args)) { return false; }
	if (false == array_key_exists('title', $args)) { $args['title'] = 'Unknown'; }

	if (false == $kapenta->fs->exists($args['fileName'])) { return false; }	// no such file

	$extractor = $kapenta->registry->get('images.videothumbcmd');

	switch($extractor) {
		case 'ffmpeg-linux':

			$tempFile = 'data/temp/' . $kapenta->time() . '_' . $kapenta->createUID() . '.jpg';

			//NOTE: assumes videos duration is 2 seconds or longer

			$shellCmd = ''
			 . 'ffmpeg'
			 . ' -itsoffset -2'
			 . ' -i "' . $kapenta->installPath . $args['fileName'] . '"'
			 . ' -vcodec mjpeg'
			 . ' -vframes 1'
			 . ' -an'
			 . ' -f rawvideo'
			 . ' -s 640x480'
			 . ' "' . $kapenta->installPath . $tempFile . '"';

			shell_exec($shellCmd);

			$kapenta->session->msgAdmin($shellCmd);

			if (true == $kapenta->fs->exists($tempFile)) {
				//----------------------------------------------------------------------------------
				//	thumbnail extracted, attach it
				//----------------------------------------------------------------------------------

				$detail = array(
					'refModule' => $args['refModule'],
					'refModel' => $args['refModel'],
					'refUID' => $args['refUID'],
					'path' => $kapenta->installPath . $tempFile,
					'srcName' => $args['title'] . '.jpg',
					'name' => $args['title'] . '.jpg',
					'ext' => 'jpg',
					'extension' => 'jpg',
					'module' => 'images'
				);

				$kapenta->raiseEvent('images', 'file_attach', $detail);
			}

			break;		//..........................................................................
	}

	return true;
}

?>
