<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	test script to compile course objects by scraping the Khan Academt site
//--------------------------------------------------------------------------------------------------
//TOSO: more sophisticated version of this to check for updated content


	if ('admin' !== $user->role) { $kapenta->page->do403(); }

	$startUrl = 'https://xh.khanacademy.org/';
	$courses = khan_getCourses($startUrl);
	$batch = '';
	//echo "<pre>"; print_r($courses); echo "</pre>";

	foreach($courses as $course) {
		//	create a consistent unique identifier for this course

		$courseId = 'khanxh' . strtolower($course['title']);
		$courseId = str_replace(array(' ', '/', ':'), array('', '', ''), $courseId);		

		$model = new Lessons_Course($courseId);
		$model->UID = $courseId;
		$model->description = '';
		$model->group = 'videosxh';
		$model->group = 'videosxh';
		$model->language = 'xh';
		$model->title = $course['title'];
	

		echo "<h2>" . $course['title'] . " (" . $courseId . ")</h2>\n";

		foreach($course['videos'] as $video) {

			echo ''
			 . "<a href='" . $video['link'] ."'>"
			 . $video['title'] . ' (' . $video['youtubeId'] . ')'
			 . "</a><br/>\n";

			//print_r($video); echo "<br/>\n";

			$model->documents[] = array(
				'uid' => $video['youtubeId'],
				'type' => 'flv',
				'title' => $video['title'],
				'description' => $video['description'],
				'cover' => '',
				'thumb' => '',
				'file' => 'data/lessons/' . $courseId . '/documents/' . $video['youtubeId'] . '.flv',
				'attribname' => 'Khan Academy',
				'attriburl' => 'https://xh.khanacademy.org/',
				'licencename' => 'CC BY-NC-SA',
				'licenceurl' => 'http://creativecommons.org/licenses/by-nc-sa/3.0/us/',
				'downloadfrom' => 'youtube://' . $video['youtubeId']
			);

		}

		if (count($model->documents) > 0) {
			$model->fileName = 'data/lessons/' . $courseId . '/manifest.xml';
			$check = $model->save();
			if (false == $check) {
				echo "<b>Could not save course.</b><br/>\n";
			} else {
				echo "<b>Saved course.</b><br/>\n";
				echo "<textarea rows='10' cols='80'>" . $model->toXml() . "</textarea><br/>\n";

				$fn = $model->fileName;
				$kapenta->fileMakeSubDirs(str_replace('manifest.xml', 'documents/x.x', $fn));
				$kapenta->fileMakeSubDirs(str_replace('manifest.xml', 'thumbs/x.x', $fn));
				$kapenta->fileMakeSubDirs(str_replace('manifest.xml', 'covers/x.x', $fn));

				$batch .= "php ./downloadcourse.sh.php $courseId\n";

			}

		}
	}

	echo ''
	 . "<h2>Bacth Download</h2>\n"
	 . "<pre>cd " . $kapenta->installPath . "modules/lessons/shell/\n" . $batch . "</pre>";

	//----------------------------------------------------------------------------------------------
	//	helper function to extract course listings
	//----------------------------------------------------------------------------------------------
	//arg: url - URL of a course listing page, eg https://xh.khanacademy.org/ [string]

	function khan_getCourses($url) {
		$courses = array();
		$video = array();
		$raw = implode(file($url));
		$lines = explode("\n", $raw);

		$course = array();
		$course['title'] = '';
		$course['videos'] = array();

		foreach($lines as $i => $line) {

			if (false !== strpos($line, 'topic-heading')) {

				if (
					(true == array_key_exists('title', $course)) &&
					(true == array_key_exists('videos', $course))
				) {
					$courses[$course['title']] = $course;
					$course = array();
					$course['title'] = '';
					$course['videos'] = array();
				}

				$course['title'] = trim(strip_tags($line));
				echo "<b>Found course: " . $course['title'] . "</b><br/>\n";
			}

			if (false !== strpos($line, 'href="/video?')) {
				$video = array();
				$video['link'] = trim($url . str_delim($line, 'href="/', '"'));
				$video['youtubeId'] = trim(str_delim($line, '&v=', '"'));
				$video['description']  = trim(str_delim($lines[$i + 1], 'title="', '"'));
				$video['title'] = trim($lines[$i + 3]);
				$video['course'] = trim($course['title']);

				echo "Found video:<br/>\n"
				 . "&nbsp;&nbsp;&nbsp;&nbsp;course: " . $video['course'] . "<br/>\n"
				 . "&nbsp;&nbsp;&nbsp;&nbsp;link: " . $video['link'] . "<br/>\n"
				 . "&nbsp;&nbsp;&nbsp;&nbsp;title: " . $video['title'] . "<br/>\n"
				 . "&nbsp;&nbsp;&nbsp;&nbsp;youtubeId: " . $video['youtubeId'] . "<br/>\n"
				 . "&nbsp;&nbsp;&nbsp;&nbsp;description: " . $video['description'] . "<br/>\n";
				
				$course['videos'][] = $video;
			}

		}

		//	add the last one
		if (
			(array_key_exists('title', $course)) &&
			(array_key_exists('videos', $course))
		) { $courses[$course['title']] = $course; }

		return $courses;
	}

	//----------------------------------------------------------------------------------------------
	//	get first substring beginning bounded as specified by start and end
	//----------------------------------------------------------------------------------------------
	//returns: substring on success, empty string on filure [string]

	function str_delim($str, $start, $end) {
		$startPos = strpos($str, $start);
		if (false == $startPos) { return ''; }
		$startPos = $startPos + strlen($start);
		$endPos = strpos($str, $end, $startPos);
		if (false == $endPos) { return ''; }
		$str = substr($str, $startPos, $endPos - $startPos);
		return $str;
	}

?>
