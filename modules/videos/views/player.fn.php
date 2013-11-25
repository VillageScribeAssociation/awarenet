<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a flowplayer to load a given video
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
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

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(please specify a video)'; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return '(video not found)'; }
	
	if (('public' == $user->role) && ('public' != $model->category)) {
		return "[[:users::pleaselogin:]]";
	}

	if ('mp3' == $model->format) { 
		$block = "[[:videos::playeraudio::raUID=" . $model->UID . ":]]";
		return $block;
	}

	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('like', $args)) { $like = $args['like']; }
	if (true == array_key_exists('extra', $args)) { $extra = $args['extra']; }
	if (true == array_key_exists('area', $args)) { $area = $args['area']; }

	//----------------------------------------------------------------------------------------------
	//	check that we actually have the file to be played
	//----------------------------------------------------------------------------------------------

	if (false == $kapenta->fs->exists($model->fileName)) {
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
		$block = $theme->loadBlock('modules/videos/views/player.block.php');
		$ext = $model->extArray();

		$ext['area'] = $area;
		$ext['width'] = $width;
		$ext['height'] = $height;
		$ext['rand'] = substr($kapenta->createUID(), 0, 5);

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

		if ('no' == $like) { $ext['like'] = ''; }

		$ciTag = $theme->expandBlocks($ciBlock, '');
		$parts = explode("'", $ciTag);

		$ext['coverImage'] = $parts[1];

		// assemble the block
		$html = $theme->replaceLabels($ext, $block);
	}

	return $html;
}

?>
