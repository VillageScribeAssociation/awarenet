<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	experimental interface to Khan Academy dataset
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	list available videos
//--------------------------------------------------------------------------------------------------
//returns: set of Lessons_Course objects [array:Lessons_Course]

function lessons_listKhan() {
	global $kapenta;
	global $utils;

	$courses = array();
	$topics = lessons_listKhanTopics();
	$url = 'https://www.khanacademy.org/api/v1/topics/library/compact?callback=__dataCb&v=787';
	$fileName = 'data/lessons/scraper/khan.json';

	if (false == $kapenta->fs->exists($fileName)) {
		$raw = $utils->curlGet($url);
		$raw = substr($raw, strlen("__dataCb("));
		$raw = substr($raw, 0, strlen($raw) - 1);
		$kapenta->fs->put($fileName, $raw);
	} else {
		$raw = $kapenta->fs->get($fileName);
	}

	$data = json_decode($raw);

	foreach($data as $k => $v) {
		//echo "key: " . $k . "<br/>";

		$model = new Lessons_Course();
		$model->UID = substr('khen' . $utils->makeAlphaNumeric($k), 0, 18);
		$model->group = 'videolessons';

		foreach($topics as $topic) {
			if ($topic['identifier'] == $k) {
				//echo "Match: " . $topic['identifier'] . ' with ' . $k . "<br/>\n";
				$model->title = $topic['title'];
				$model->description = $topic['description'];
				$model->subject = $topic['subject'];
			}
		}

		foreach($v->children as $video) {
			$model->documents[] = array(
				'title' => $video->title,
				'uid' => $video->progress_key,
				'downloadfrom' => 'http://www.khanacademy.org' . $video->url
			);
		}

		if ('' !== $model->title) {
			$courses[$model->UID] = $model;
		} else {
			if (count($model->documents) > 0) {
				$model->title = str_replace('-', ' ', $k);
				$courses[$model->UID] = $model;
				//echo "Title not found for topic: " . $k . "<br/>\n";
			}
		}
	}

	//echo "<pre>"; print_r($data); echo "</pre>";
	return $courses;
}


//--------------------------------------------------------------------------------------------------
//	get topic headings and descriptions
//--------------------------------------------------------------------------------------------------
//returns: set of Lessons_Course objects [array:Lessons_Course]

function lessons_listKhanTopics() {
	global $kapenta;
	global $utils;

	$raw = '';
	$superTopic = '';
	$subTopic = '';
	$description = '';
	$id = '';
	$topics = array();
	$topic = array();
	$url = 'https://www.khanacademy.org/library';
	$fileName = 'data/lessons/scraper/khan.lib.html';

	if (false == $kapenta->fs->exists($fileName)) {
		$raw = $utils->curlGet($url);
		$kapenta->fs->put($fileName, $raw);
	} else {
		$raw = $kapenta->fs->get($fileName);
	}

	$lines = explode("\n", $raw);

	foreach($lines as $line) {

		if (false !== strpos($line, 'subtopic-1-heading')) {
			$superTopic = trim(strip_tags($line));
			//echo "found supertopic: $superTopic<br/>\n";
		}

		if (false !== strpos($line, '-container" data-theme="b">')) {
			$startPos = strpos($line, 'id="') + 4;
			$endPos = strpos($line, '-container', $startPos);
			$identifier = substr($line, $startPos, $endPos - $startPos);
			//echo "found identifier: $identifier<br/>\n";
		}

		if (false !== strpos($line, 'subtopic-2-heading')) {
			$subTopic = trim(strip_tags($line));
			$description = '';
			//echo "found subtopic: $subTopic<br/>\n";
		}

		if (false !== strpos($line, 'topic-desc')) {
			$description = trim(strip_tags($line));
		}

		if (false !== strpos($line, 'class="topic-loading"')) {

			$topics[] = array(
				'identifier' => $identifier,
				'title' => $superTopic . ' - ' . $subTopic,
				'description' => $description,
				'subject' => $superTopic
			);
			//echo "Adding topic: $identifier<br/>\n";

		}
		
	}

	return $topics;
}

//--------------------------------------------------------------------------------------------------
//	scrape a video page for the youtube ID
//--------------------------------------------------------------------------------------------------
//arg: videoUrl - URL of a video page on khanacademy.org [string]
//returns: a Youtube video ID if found, empty string if not [string]

function lessons_getVideoID($videoUrl) {
	global $kapenta;
	global $utils;

	$hash = sha1($videoUrl);
	$fileName = 'data/lessons/scraper/' . $hash . '.deleteme';
	$raw = '';

	if (false == $kapenta->fs->exists($fileName)) {
		$raw = $utils->curlGet($videoUrl);
		$raw = $kapenta->fs->put($fileName, $raw);
	} else {
		$raw = $kapenta->fs->get($fileName);
	}

	//echo "<textarea rows='10' cols='80'>" . htmlentities($raw) . "</textarea><br/>\n";

	$startPos = strpos($raw, 'youtubeid="');
	if (false == $startPos) { return ''; }
	$startPos += strlen('youtubeid="');
	$endPos = strpos($raw, '"', $startPos);
	if (false == $endPos) { return ''; }
	return substr($raw, $startPos, $endPos - $startPos);
}
?>
