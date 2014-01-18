<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a flowplayer to load a given video
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string] or filename of video file to be played
//opt: videoUID - overrides raUID if present [string]
//TODO: registry setup for video player sizes

function videos_player($args) {
	global $kapenta;
	global $user;
	global $session;
	global $theme;
	global $page;

	$area = 'content';			//%	content area / column in which this is rendered [string]
	$width = '570';				//%	player width, pixels [string]
	$height = '420';			//%	player height, pixels [string]
	$coverSize = 'width570';	//%	size of cover image [string]
	$extra = '';				//%	additional controls defined by caller [string]
	$like = 'yes';				//%	show like button [string]
	$html = '';					//%	return value [string]
	$filename = '';				//% file name of video to be played
	$cover = '';				//% file name of cover image
	$browserLink = '';			//% browser link	
	$autoPlay = 'no';			//% 'yes' or 'no' to affectuate autoplay of video

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { 
		if (false == array_key_exists('filename', $args)) {
			return '(please specify a video)'; 
		} else {
			$args['raUID'] = 'none';		
		}
	}

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { 
		if (false == array_key_exists('filename', $args)) {
			return '(video not found)'; 
		}
	}
	
	if (true == $model->loaded) {
		if (('public' == $user->role) && ('public' != $model->category)) {
			return "[[:users::pleaselogin:]]";
		}
	}
	
	if (true == $model->loaded) {
		if ('mp3' == $model->format) { 
			$block = "[[:videos::playeraudio::raUID=" . $model->UID . ":]]";
			return $block;
		}
	}

	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('like', $args)) { $like = $args['like']; }
	if (true == array_key_exists('extra', $args)) { $extra = $args['extra']; }
	if (true == array_key_exists('area', $args)) { $area = $args['area']; }
	if (true == array_key_exists('filename', $args)) { 
		$filename = $args['filename']; 		
	} else {
		$filename = $model->fileName;
	}
	if (true == array_key_exists('cover', $args)) { $cover = $args['cover']; }
	if (true == array_key_exists('browserLink', $args)) { $browserLink = $args['browserLink']; }
	
	if (true == array_key_exists('autoPlay', $args)) {$autoPlay = $args['autoPlay']; }

	//----------------------------------------------------------------------------------------------
	//	check that we actually have the file to be played
	//----------------------------------------------------------------------------------------------

	if (false == $kapenta->fs->exists($filename)) {
		$block = $theme->loadBlock('modules/videos/views/nofile.block.php');
		return $block;
	}

	//----------------------------------------------------------------------------------------------
	//	decide on a size (TODO: handle this through the registry)
	//----------------------------------------------------------------------------------------------

	if ('desktop' !== $session->get('deviceprofile')) { $area = 'mobile'; }

	switch($area) {
		case 'content':		$width = 570;	$height = 420;		break;
		case 'indent':		$width = 515;	$height = 400;		break;
		case 'nav1':		$width = 300;	$height = 200;		break;
		case 'nav2':		$width = 300;	$height = 200;		break;
		case 'mobile':		$width = 320;	$height = 240;		break;
	}

	if (true == array_key_exists('width', $args)) { $width = (int)$args['width']; }
	if (true == array_key_exists('height', $args)) { $height = (int)$args['height']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if ('swf' == $model->format) {
		//------------------------------------------------------------------------------------------
		//	flash animation
		//------------------------------------------------------------------------------------------
		$html .= "(cannot preview this object)";

	} else {
		//------------------------------------------------------------------------------------------
		//	flash or mp4 video
		//------------------------------------------------------------------------------------------
		$page->requireJs($kapenta->serverPath . 'modules/videos/js/flowplayer-3.2.6.min.js');
		$blockFile = '';
		if ('yes' == $autoPlay) {
			$blockFile = 'modules/videos/views/playerAuto.block.php';
		} else {
			$blockFile = 'modules/videos/views/player.block.php';
		}
		
		$block = $theme->loadBlock($blockFile);
		
		if (true == $model->loaded) {
			$ext = $model->extArray();
		} else {
			$ext = array('fileName' => $filename);
		}

		$ext['area'] = $area;
		$ext['width'] = $width;
		$ext['height'] = $height;
		$ext['rand'] = substr($kapenta->createUID(), 0, 5);	

		if (true == $model->loaded) {
			// load cover image (TODO: make this better)
			$ciBlock = ''
			 . "[[:images::default"
			 . "::size=width570" // . $args['area']
			 . "::link=no"
			 . "::refModule=videos"
			 . "::refModel=videos_video"
			 . "::refUID=" . $model->UID
			 . ":]]";

			$ext['like'] = ''
			 . '[[:like::link'
			 . '::refModule=videos'
			 . '::refModel=videos_video'
			 . '::refUID=' . $ext['UID']
			 . ':]]';
			$ciTag = $theme->expandBlocks($ciBlock, '');
			$parts = explode("'", $ciTag);

			$ext['coverImage'] = $parts[1];
		} else {
			$ext['coverImage'] = $cover;
			$ext['browserLink'] = $browserLink;
			$like = 'no';
			$ext['extra'] = '';
		}

		if ('no' == $like) { $ext['like'] = ''; }

		// assemble the block
		$html = $theme->replaceLabels($ext, $block);
	}

	return $html;
}

?>
