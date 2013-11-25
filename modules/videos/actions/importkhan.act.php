<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');
	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temporary action to import kahn academy videos
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	//if ('admin' == $user->role) { $page->do403(); }

	$videoFiles = array();
	$galleries = array();

	//----------------------------------------------------------------------------------------------
	//	make a list of galleries
	//----------------------------------------------------------------------------------------------
	
	$dirs = $kapenta->fileList('data/khan/','', true);
	foreach($dirs as $dir) {
		$galleryUID = substr(sha1($dir), 0, 25);
		$title = str_replace('data/khan/', '', $dir);
		$title = str_replace('/', '', $title);
		echo "found gallery: $dir (UID: $galleryUID) <br/>\n";
		echo "title: $title <br/>";
		echo "<br/>\n";

		$gallery = array(
			'UID' => $galleryUID,
			'galleryUID' => $galleryUID,
			'title' => $title,
			'dir' => $dir,
			'description' => $title,
			'shared' => 'no',
			'videos' => array()
		);

		$videoFiles = array();
		$flvs = $kapenta->fileList($dir, '.flv');
		$mp4s = $kapenta->fileList($dir, '.mp4');

		foreach($flvs as $fileName) { $videoFiles[] = $fileName; }		
		foreach($mp4s as $fileName) { $videoFiles[] = $fileName; }

		foreach($videoFiles as $videoFile) {
			//--------------------------------------------------------------------------------------
			//	video file
			//--------------------------------------------------------------------------------------
			$videoUID = substr(sha1($videoFile), 0, 25);
			$videoTitle = str_replace($dir, '', $videoFile);
			$videoTitle = str_replace('.mp4', '', $videoTitle);
			$videoTitle = str_replace('.flv', '', $videoTitle);
			echo "found video: $videoFile ($videoUID)<br/>\n";
			echo "title: $videoTitle<br/>";

			//--------------------------------------------------------------------------------------
			//	image file
			//--------------------------------------------------------------------------------------
			$imageFile = $videoFile . '.jpg';
			$imageUID = substr(sha1($imageFile), 0, 25);
			//echo "<img src='/" . $imageFile . "' /><br/>";
			

			//--------------------------------------------------------------------------------------
			//	video meta
			//--------------------------------------------------------------------------------------
			$meta = videos_getMplayerInfo($videoFile . '.txt');
			//foreach($meta as $key => $value) { echo "meta: $key = $value <br/>\n"; }

			$gallery['videos'][$videoUID] = array(
				'UID' => $videoUID,
				'videoUID' => $videoUID,
				'fileName' => $videoFile,
				'videoFile' => $videoFile,
				'title' => $videoTitle,
				'imageUID' => $imageUID,
				'imageFile' => $imageFile,
				'meta' => $meta
			);

		} 

		$galleries[$galleryUID] = $gallery;

	}

	//----------------------------------------------------------------------------------------------
	//	save everything
	//----------------------------------------------------------------------------------------------

	foreach($galleries as $ary) {
		if (false == $db->objectExists('videos_gallery', $ary['UID'])) {
			$gallery = new Videos_Gallery();
			$gallery->UID = $ary['UID'];
			$gallery->title = $ary['title'];
			$gallery->description = 'Khan Academy';
			$gallery->shared = 'no';
			$report = $gallery->save();
			if ('' == $report) {
				echo "created gallery: " . $gallery->title . '(' . $gallery->UID . ')<br/>';
			} else {
				echo "could not create gallery: " . $gallery->title . '(' . $gallery->UID . ')<br/>';
			}
		} else {
			$gallery = new Videos_Gallery($ary['UID']);
			$gallery->save();
		}

		foreach($ary['videos'] as $videoUID => $item) {
			if (false == $db->objectExists('videos_video', $item['videoUID'])) {
				$video = new Videos_Video();
				$video->UID = $item['videoUID'];
				$video->refModule = 'videos';
				$video->refModel = 'videos_video';
				$video->refUID = $ary['UID'];
				$video->title = $item['title'];
				$video->licence = 'CC-BY-NC-SA';
				$video->attribName = 'Khan Academy';
				$video->attribUrl = 'http://www.khanacademy.org/';
				$video->fileName = $item['videoFile'];
				$video->format = 'mp4';
				if (false != strpos($item['videoFile'], '.flv')) { $video->format = 'flv'; }
				$video->length = $item['meta']['ID_LENGTH'];
				$video->shared = 'no';
				$report = $video->save();
				if ('' == $report) { echo "saved video " . $item['videoUID'] . "(" . $item['videoFile'] . ")<br/>"; }
				else {  echo "could not save video: $report<br/>\n"; }
			}

			if (false == $db->objectExists('images_image', $item['imageUID'])){
				$image = new Images_image();
				$image->UID = $item['imageUID'];
				$image->refModule = 'videos';
				$image->refModel = 'videos_video';
				$image->refUID = $item['videoUID'];
				$image->title = $item['title'];
				$image->fileName = $item['imageFile'];
				$image->shared = 'no';
				$image->format = 'jpg';
				$image->licence = 'CC-BY-NC-SA';
				$image->attribName = 'Khan Academy';
				$image->attribUrl = 'http://www.khanacdemy.org/';
				$report = $image->save();
				if ('' == $report) {
					echo "saved image " . $item['imageUID'] . "(" . $item['imageFile'] . ");";	
				} else {
					echo "could not save image " . $item['imageUID'] . "(" . $item['imageFile'] . ")<br/>$report<br/>";	
				}
			}
		}

	}

//==================================================================================================
//	utility functions for this action
//==================================================================================================

	function videos_getMplayerInfo($fileName) {
		global $kapenta;
		$meta = array();

		if (false == $kapenta->fs->exists($fileName)) {	return $meta; }

		$raw = $kapenta->fs->get($fileName, false, false);
		$lines = explode("\n", $raw);
		foreach($lines as $line) {
			$eqPos = strpos($line, '=');
			if (false != $eqPos) {
				$key = substr($line, 0, $eqPos);
				$value = substr($line, $eqPos + 1);
				$meta[$key] = $value;
			}
		}

		return $meta;
	}
	
?>
