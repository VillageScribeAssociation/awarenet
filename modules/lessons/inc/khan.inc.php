<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	
class Input {
	public static $objStringOpen;
	public static $objStringClose;
	public static $objString;
	public static $childrenStringOpen;
	public static $childrenStringClose;
	public static $outputString;
	public static $depth;
	public static $cursor;
}	

class Entry {

	public static $objId;	
	public static $depth;
	public static $id;
}

//	public $id;
//	public $objString;
//	public $kind;
//	public $title;
//	public $description;
//	public $ka_url;
//	public $youtube_id;
//	public $depth;		
//	public $children;
//	public $parentEntry = null;
//	public $hasData = false;

function createAndLoginKhanLite() {
	global $kapenta;
	
	//not signed in:
	//1) Check if awarenet user exists in khanlite database
    $dbh = connectToKhanLiteDB();
	
	if (NULL !== $dbh) {
		$username = $kapenta->user->username;
		$query = "SELECT * FROM securesync_facilityuser WHERE username=\"".$username."\"";
		$sth = prepareSQLStatement($dbh, $query);
		executeSQLStatement($sth);
		if (FALSE === $sth->fetch()) {
			echo "no result\n";
			//2) so now we have to insert the awarenet user but we are using the webfunctionalitye of khanlite's user creation 
			//instead of direct sql insertion because of the many fields that need to be defined by that process

			$kaliteAdmin = $kapenta->registry->get('kalite.admin.user');
			$kaliteAdminPwd = $kapenta->registry->get('kalite.admin.pwd');			
			//2a) login as khanlite admin (stored in kapenta registry)
			loginToKhanLite($kaliteAdmin, $kaliteAdminPwd);
		
			
			//2b) check if we need to create a student or a teacher
			if ('student' === $kapenta->user->role) {
				createKhanLiteAccount('student');					
			} else if ('teacher' === $kapenta->user->role or 'admin' === $kapenta->user->role) {
				createKhanLiteAccount('teacher');
			}
			
			logoutKhanLite();
		}
		$sth = null;
		$dbh = null;
		
   		//3) Perform automatic login
		loginToKhanLite('', '');


   	} else {
   		echo "no connection to database<br/>";
   	}	
}

function changeLocalLinksFromKhanLitePage($pageStr) {
//	$replaced = str_replace("/math", "/lessons/mathkhan", $pageStr);
	$replaced = str_replace("/science", "/lessons/sciencekhan", $pageStr);
	$replaced = str_replace("/humanities", "/lessons/humanitieskhan", $replaced);
	$replaced = str_replace("/test-prep", "/lessons/testprepkhan", $replaced);
	$replaced = str_replace("/discovery-lab", "/lessons/discoverylabkhan", $replaced);
	$replaced = str_replace("/exercisedashboard", "/lessons/exercisekhan", $replaced);
	return $replaced;
}

function removeLinksFromKhanLitePage($pageStr) {
	$length = strlen($pageStr);
	$start = 0;
	while ($start < $length) {
		$start = strpos($pageStr, '<a href', $start);
		if (FALSE == $start) {
			$start = $length;
		}
	
		if ($start < $length) {
			$end = strpos($pageStr, '</a>', $start + 1);
			if (FALSE == $end) {
				$end = $length;
			} else{
				$end = $end + 4;
				
				if ((strpos($pageStr, 'Admin', $start) and strpos($pageStr, 'Admin', $start) < $end)
					or (strpos($pageStr, 'Add Teacher', $start) and strpos($pageStr, 'Add Teacher', $start) < $end)
					or (strpos($pageStr, 'Add Student', $start) and strpos($pageStr, 'Add Student', $start) < $end)
					or (strpos($pageStr, 'Update', $start) and strpos($pageStr, 'Update', $start) < $end)
					or (strpos($pageStr, 'Coach', $start) and strpos($pageStr, 'Coach', $start) < $end)
					or (strpos($pageStr, 'Logout', $start) and strpos($pageStr, 'Logout', $start) < $end)
					) {
					$pageStr = substr_replace ($pageStr , '' , $start, $end - $start);
				} else {
					$start = $end;
				}
			}
		}
	}

	$replaced = str_replace('href="/"', '', $pageStr);
	
	return $replaced;
}

function logoutKhanLite() {
	global $kapenta;
	
	$reply = $kapenta->utils->curlGet($kapenta->registry->get('kalite.installation').'/securesync/logout/', '', false, '');
	$kapenta->session->set('c_sessionid', '');
	$kapenta->session->set('c_csrftoken', '');
}

function createKhanLiteAccount($type) {
	global $kapenta;

//	echo "Create a user of type ".$type."<br/>\n";
	
	//add awarenet user to khanlite
	$id = $kapenta->session->get('c_sessionid');
	$reply = $kapenta->utils->curlGet($kapenta->registry->get('kalite.installation').'/securesync/add'.$type.'/', '', 
			false, 'sessionid='.$id);

    echo "KALite installation: " . $kapenta->registry->get('kalite.installation') . "<br/>";

	$start = strpos($reply, "name='csrfmiddlewaretoken' value='") + 34;
//	echo "start:".$start;
	$end = strpos($reply, "'", $start);
//	echo "end:".$end;
	$csrftoken = substr($reply, $start, $end - $start);
//	echo "csrftoken=".$csrftoken."<br/>\n";
	$kapenta->session->set('c_csrftoken', $csrftoken);
	//2c) extract facility id from page
	$index = strpos($reply, 'name="facility" value=');
	if (FALSE !== $index) {
		//2d) set up arguments for post/login
		$pw = sha1($kapenta->user->password . '11111');
		$kapass = 'sha_1$11111$' . $pw;
//		echo "kapass=".$kapass."<br/>\n";
		$start = strpos($reply, 'name="facility" value="', $index) + 23;
		$end = strpos($reply, '" id="id_facility"', $start);
		$facility = substr($reply, $start, $end - $start);
//		echo "facility=".$facility."<br/>\n";
		$args = 'csrfmiddlewaretoken='.$csrftoken."&";
		$args = $args.'username='.$kapenta->user->username."&";
		$args = $args.'first_name='.$kapenta->user->firstname."&";
		$args = $args.'last_name='.$kapenta->user->surname."&";
		$args = $args.'password='.$kapass."&";
		$args = $args.'password_recheck='.$kapass."&";
		$args = $args.'facility='.$facility;
		
//		echo "Create putArgs=".$args."<br/>\n";

		$cookies = 'sessionid='.$id.';csrftoken='.$csrftoken;
		$reply = $kapenta->utils->curlPost($kapenta->registry->get('kalite.installation').'/securesync/add'.$type.'/', $args, 
				true, $cookies);
//		echo $reply;	
	}	
}

function loginToKhanLite($username, $password) {
	global $kapenta;

	//	a) firstly get securesync/login page and extract csrftoken from it
	$kalite = $kapenta->registry->get('kalite.installation');
//	echo $kalite;
	$reply = $kapenta->utils->curlGet($kalite.'/securesync/login/', '', true, '');
	$start = strpos($reply, 'Set-Cookie:  csrftoken=') + 23;
	$end = strpos($reply, '; expires=', $start);
	$csrftoken = substr($reply, $start, $end - $start);
	$kapenta->session->set('c_csrftoken', $csrftoken);
	//	b) extract facility id from page
	$index = strpos($reply, '<select name="facility" id="id_facility">');
	if (FALSE !== $index) {
		// 	c) set up arguments for post/login
		$index = strpos($reply, '<option value="">---------</option', $index) + 7;
		$start = strpos($reply, 'option value="', $index) + 14;
		$end = strpos($reply, '" selected="selected">', $start);
		$facility = substr($reply, $start, $end - $start);
		$user = $username;
		if ('' === $user) {
			$user = $kapenta->user->username;
		}
		
		$pw = $password;
		if ('' === $pw) {
			$pw = sha1($kapenta->user->password . '11111');
			$pw = 'sha_1$11111$' . $pw;
		}
		
		$args = 'csrfmiddlewaretoken='.$csrftoken."&";
		$args = $args.'facility='.$facility."&";
		$args = $args.'username='.$user."&";
		$args = $args.'password='.$pw;

//		echo "Login putArgs=".$args."<br/>\n";

		//	d) POST login data and retrieve sessionid cookie
		$reply = $kapenta->utils->curlPost($kalite.'/securesync/login/', $args, true, 'csrftoken='.$csrftoken, 
			array('X-CSRFToken: '.$csrftoken));			
		$start = strpos($reply, 'Set-Cookie:  sessionid=') + 23;
		$end = strpos($reply, '; expires=', $start);
		$sessionid = substr($reply, $start, $end - $start);
		$kapenta->session->set('c_sessionid', $sessionid);
	}
}

function executeSQLStatement($sth) {
	try { $check = $sth->execute(); }
	catch(PDOException $e) {
		$msg = "Failed to execute SQL Statement<br/>\n" . $e->getMessage();
		if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
		else { echo $msg . "<br/>\n"; }
		die();
	}

	if (false === $check) {
		$msg = "Could not execute database query:<br/>" . $query . "<hr/><br/>" . mysql_error();
		if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
		die();
	}

//	echo "Now we have executed a query<br/>";
}

function prepareSQLStatement($dbh, $statement) {
	if (NULL !== $dbh) {
    	try { $sth = $dbh->prepare($statement); }
		catch(PDOException $e) {
			$msg = "Failed to prepare SQL Statement: $statement<br/>\n" . $e->getMessage();
			if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
			else { echo $msg . "<br/>\n"; }
			die();
		}
	}	

 //   echo "We have prepared statement: ".$statement."<br/>";
    return $sth;
}

function connectToKhanLiteDB() {
	global $kapenta;
	
    $dbFile = $kapenta->registry->get('kalite.db.file');
    $msg = '';

	try {
		$dbh = new PDO('sqlite:' . $dbFile);
    	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException  $e) {

		$msg = "SQLite PDO Connection failed: " . $e->getMessage();
		if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
		else { echo $msg . "<br/>\n"; }
		die();
	}

//	echo "Connected to SQLite database.<br/>";
	return $dbh;
}

//--------------------------------------------------------------------------------------------------
//*	experimental interface to Khan Academy dataset
//--------------------------------------------------------------------------------------------------
function parseChildren($id) {
	$char = '';
	$cc = 0;
	$indexOpen = Input::$childrenStringOpen[$id];
	$indexClose = Input::$childrenStringOpen[$id];
	$start = false;
	$count = 0;
	
	$cc = $indexOpen;
	
	$first = substr(Input::$objString, Input::$childrenStringOpen[$id], 1);
	$last = substr(Input::$objString, Input::$childrenStringClose[$id],1);
	echo "children: " . $first . " / " . $last . "<br>";

	//Establish beginning and end of object
	while ($cc < Input::$childrenStringClose[$id]) {
		echo "cc in children: " . $cc . "<br>";
		
		$indexOpen = strpos(Input::$objString, "{", $cc);
		if ($indexOpen >= Input::$childrenStringClose[$id] or false == $indexOpen) {
			echo "end of children string reached!>";
			break;
		}
		
		echo "indexOpen in children: " . $indexOpen . "<br>";
		echo "childrenStringClose: " . Input::$childrenStringClose[$id];
		$cc = $indexOpen+1;
		$count = 1;
				
		while ($cc < Input::$childrenStringClose[$id]) {

			$char = substr(Input::$objString, $cc, 1);
			
			if ($char == "{") {
				$count++;
			} else if ($char == "}") {
				$count--;
			}

			if ($count == 0) {
				$indexClose = $cc;
				if ( $indexOpen < $indexClose) {
					//Create object string
					$nid = Entry::$objId;
					Input::$objStringOpen[$nid] = $indexOpen;
					Input::$objStringClose[$nid] = $indexClose;
			
					echo "cc in children obj found: " . $cc . "<br>";
					echo "indexOpen in children: " . $indexOpen . "<br>";
					echo "indexClose in children: " . $indexClose . "<br>";
					//Create an Entry object with the established object string
					createEntry(Entry::$depth[$id]);
					break;
				}
			} else {
				$cc++;
			}
		}
	}
}

function parseObject($id) {
	$bOk = false;
	$indexOpen = Input::$objStringOpen[$id];
	$char = '';
	$cc = 0;
	$indexClose = Input::$objStringClose[$id];
	$children = '';
	$count = 0;

	Input::$cursor = $indexOpen;
	
	echo "parseObj id: " . $id . "<br>";
	echo "indexOpen: " . $indexOpen . "<br>";
	echo "indexClose: " . $indexClose . "<br>";
	echo "cursor in obj: " . Input::$cursor . "<br>";
	
	$first = substr(Input::$objString, $indexOpen, 1);
	$last = substr(Input::$objString, $indexClose, 1);

	echo "obj: " . $first . " / " . $last . "<br>";
			
	Input::$childrenStringOpen[$id] = 0;
	Input::$childrenStringClose[$id] = 0;		

	//Find children array start
	$indexOpen = strpos(Input::$objString, "\"children\": [", $indexOpen);
	echo "indexOpen: " . $indexOpen . "<br>";
	
	if ($indexOpen > 0) {
		$indexOpen = $indexOpen + 12;
		$count = 1;
		
		//Find children array end
		$cc = $indexClose;

		while ($cc > Input::$objStringOpen[$id]) {
			$char = substr(Input::$objString, $cc, 2);
	
			if ($char == "}]") {
				$count--;
			}
			
			if ($count == 0) {
				echo "cc at end of obj in obj: " . $cc . "<br>";
				$indexClose = $cc+1;
				//Compose children string
				Input::$childrenStringOpen[$id] = $indexOpen;
				Input::$childrenStringClose[$id] = $indexClose;
				$bOk = true;		
				break;
			} 
			$cc--;
		}
		echo "count: " . $count . "<br>";
	}
	
	//Get Title of Object
	$title = '';
	$indexTitle = strpos(Input::$objString, "\"title\":", Input::$objStringOpen[$id]);
	if ($indexTitle > $indexOpen) {
//			echo "title after children<br>";
		$indexTitle = strpos(Input::$objString, "\"title\":", $indexClose);
	}
	
	if ($indexTitle > 0) {
		$title = substr(Input::$objString, $indexTitle + 10, strpos(Input::$objString, "\"", $indexTitle + 10) - ($indexTitle + 10));
		echo "title: " . $title . "<br>";

		$depth = Entry::$depth[$id];
		
		Input::$outputString = Input::$outputString . "<h" . $depth . ">" . $title . "</h" . $depth . "><br>";
	}

	if ($bOk) {
		parseChildren($id);
	}		

	//Get Kind of Object
//		$this->kind = '';
//		$indexKind = strpos(Input::$objString, "\"kind\":");
//		if ($indexKind > $indexOpen) {
//			echo "title after children<br>";
//			$IndexKind = strpos(Input::$objString, "\"kind\":", $indexClose);
//		}
	
//		if ($indexKind > 0) {
//			$this->kind = substr(Input::$objString, $indexKind + 9, strpos(Input::$objString, "\"", $indexKind + 9) - ($indexKind + 9));
//			echo "kind: " . $this->kind . "<br>";
//		}


	//Get ka_url of Object
//		$this->ka_url = '';
//		$indexKaUrl = strpos(Input::$objString, "\"ka_url\":");
//		if ($indexKaUrl > $indexOpen) {
//			echo "title after children<br>";
//			$indexKaUrl = strpos(Input::$objString, "\"ka_url\":", $indexClose);
//		}
	
//		if ($indexKaUrl > 0) {
//			$this->ka_url = substr(Input::$objString, $indexKaUrl + 11, strpos(Input::$objString, "\"", $indexKaUrl + 11) - ($indexKaUrl + 11));
//			echo "ka_url: " . $this->ka_url . "<br>";
//		}


	//Get youtube_id of Object
//		$this->youtube_id = '';
//		$indexYoutube_id = strpos(Input::$objString, "\"youtube_id\":");
//		if ($indexYoutube_id > $indexOpen) {
//			echo "title after children<br>";
//			$indexYoutube_id = strpos(Input::$objString, "\"youtube_id\":", $indexClose);
//		}
	
//		if ($indexYoutube_id > 0) {
//			$this->youtube_id = substr(Input::$objString, $indexYoutube_id + 15, strpos(Input::$objString, "\"", $indexYoutube_id + 15) - ($indexYoutube_id + 15));
//			echo "youtube_id: " . $this->youtube_id . "<br>";
//		}

	//Get Description of Object
//		$this->description = '';
//		$indexDescr = strpos(Input::$objString, "\"description\":");
//		if ($indexDescr > $indexOpen) {
//			echo "title after children<br>";
//			$indexDescr = strpos(Input::$objString, "\"description\":", $indexClose);
//		}
	
//		if ($indexDescr > 0) {
//			$len = strpos(Input::$objString, "\"", $indexDescr + 16) - ($indexDescr + 16);
//			if ($len > 30) {
//				$len = 30;
//			}
//			$this->description = substr(Input::$objString, $indexDescr + 16, $len);
//			echo "description: " . $this->description . "<br>";
//		}
	
	Input::$cursor = $indexClose;
	echo "cursor at end in obj: " . Input::$cursor . "<br>";

	echo "<br>";
	echo "memory: " . memory_get_usage() . "<br>";
}

function createEntry($depth) {
	echo "<br>";
//	$entry = new Entry();

	$depth++;	
	Entry::$depth[Entry::$objId] = $depth;
	Entry::$objId++;
	
//	$entry->depth = $depth + 1;
	
	//create numerical id for Entry
//	$entry->id = Entry::$objId++;
	echo "entry created: " . (Entry::$objId - 1) . "<br>";
	
	//Initialize children fields
//	$entry->children = [];
//	$entry->chilrenIndex = 0;
	
	//set created Entry as child in parent
//	if (null != $parent) {
//		$parent->children[$parent->childrenIndex++] = $entry;
//	}

	//parse the passed in String
	parseObject((Entry::$objId - 1));
	
//	$entry = NULL;	
//	return $entry
}

//--------------------------------------------------------------------------------------------------
//	list available videos
//--------------------------------------------------------------------------------------------------
//returns: set of Lessons_Course objects [array:Lessons_Course]
function lessons_listKhan() {
	global $kapenta;
	global $utils;

	echo '<br><br><br><br><br><br><br><br><br><br>';

	$url = 'https://www.khanacademy.org/api/v1/topictree';
	$fileName = 'data/lessons/scraper/khantreelist.json';

	Input::$objString = "";
	
	if (false == $kapenta->fs->exists($fileName)) {
		Input::$objString = $utils->curlGet($url);
		$kapenta->fs->put($fileName, $utils->curlGet($url));
	} else {
		Input::$objString = $kapenta->fs->get($fileName);
	}

	Entry::$objId = 0;
	Input::$cursor = 0;
	Input::$objStringOpen[0] = 0;
	Input::$objStringClose[0] = strlen(Input::$objString)-1;

	Input::$outputString = "";
	createEntry(0);	
	
//	$parser = new KJson();
//	$outputString = $parser->tokenize($raw);

	$courses = "<br>courses:<br><br>" . Input::$outputString;
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
