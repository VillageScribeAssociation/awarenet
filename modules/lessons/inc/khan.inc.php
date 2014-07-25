<?php

//	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	KA Lite Integration helper functions
//--------------------------------------------------------------------------------------------------
	
//--------------------------------------------------------------------------------------------------
//	automatically create a KA Lite user if it is 1st time access
//--------------------------------------------------------------------------------------------------
function createAndLoginKhanLite() {
	global $kapenta;
		
	//not signed in:
	//
	//----------------------------------------------------------------------------------------------
	//	Check if awarenet user exists in khanlite database
	//----------------------------------------------------------------------------------------------	
    $dbh = connectToKhanLiteDB();
	
	if (NULL !== $dbh) {
		$retarg = '';
        $kapenta->session->msgAdmin('Logging into KA Lite as ' . $kapenta->user->username, 'info');
		$username = $kapenta->user->username;
		$query = "SELECT * FROM securesync_facilityuser WHERE username=\"".$username."\"";
		$sth = prepareSQLStatement($dbh, $query);
		executeSQLStatement($sth);
		if (FALSE === $sth->fetch()) {
///			echo "no user for: " . $kapenta->user->username . "\n";

            $kapenta->session->msgAdmin('Creating KA Lite account for ' . $kapenta->user->username, 'info');

			//----------------------------------------------------------------------------------------------
			//	Check if awarenet user exists in khanlite database
			//----------------------------------------------------------------------------------------------	
			// so now we have to insert the awarenet user but we are using the webfunctionalitye of khanlite's user creation 
			// instead of direct sql insertion because of the many fields that need to be defined by that process

			$kaliteAdmin = $kapenta->registry->get('kalite.admin.user');
			$kaliteAdminPwd = $kapenta->registry->get('kalite.admin.pwd');			
			//----------------------------------------------------------------------------------------------
			//	Check if awarenet user exists in khanlite database
			//----------------------------------------------------------------------------------------------	
			// login as khanlite admin (stored in kapenta registry)
			$retarg = loginToKhanLite($kaliteAdmin, $kaliteAdminPwd);
			$kapenta->session->set('kalite_sessionid', $retarg['sessionid']);
			$kapenta->session->set('kalite_csrftoken', $retarg['csrftoken']);
		
			
			//----------------------------------------------------------------------------------------------
			//	Check if awarenet user exists in khanlite database
			//----------------------------------------------------------------------------------------------	
			// check if we need to create a student or a teacher
			if ('student' === $kapenta->user->role) {
				createKhanLiteAccount('student');					
			} else if ('teacher' === $kapenta->user->role or 'admin' === $kapenta->user->role) {
				createKhanLiteAccount('teacher');
			}
			
			logoutKhanLite();
		}
		$sth = null;
		$dbh = null;
		
		//----------------------------------------------------------------------------------------------
		//	Perform automatic login
		//----------------------------------------------------------------------------------------------	
 		$retarg = loginToKhanLite('', '');


   	} else {        
   		echo "no connection to database<br/>";
		$kapenta->logEvent('kalite', 'login', 'no database', 'no connection to database!');
   	}	
   	
   	return $retarg;
}

//--------------------------------------------------------------------------------------------------
//	replace links in html page with awarenet links
//--------------------------------------------------------------------------------------------------
//arg: pageStr - html page returned from a request to KA Lite [string]
//returns: the html page with the replaced values [string]
function changeLocalLinksFromKhanLitePage($pageStr) {
	$replaced = str_replace("/math", "/kalite/math", $pageStr);
	$replaced = str_replace("/science", "/kalite/science", $replaced);
	$replaced = str_replace("/humanities", "/kalite/humanities", $replaced);
	$replaced = str_replace("/economics-finance-domain", "/kalite/economics-finance-domain", $replaced);
	$replaced = str_replace("/test-prep", "/kalite/testprep", $replaced);
	$replaced = str_replace("/discovery-lab", "/kalite/discovery", $replaced);
	$replaced = str_replace("/partner-content", "/kalite/partner-content", $replaced);
	return $replaced;
}

//--------------------------------------------------------------------------------------------------
//	replace links in html page with awarenet links
//--------------------------------------------------------------------------------------------------
//arg: pageStr - html page returned from a request to KA Lite [string]
//returns: the html page with the replaced values [string]
function replaceLinksFromKhanLitePage($pageStr) {
	$replaced = str_replace("/static/css", "/kalite/static/css", $pageStr);
	$replaced = str_replace("/exercisedashboard", "/kalite/exercisedashboard", $replaced);
	$replaced = str_replace("/static/images", "/kalite/static/images", $replaced);
	$replaced = str_replace("/static/data", "/kalite/static/data", $replaced);
	$replaced = str_replace("/static/js", "/kalite/static/js", $replaced);
	$replaced = str_replace('/static" + topic.icon_url', '/kalite/static" + topic.icon_url', $replaced);
	$replaced = str_replace("/static/video-js", "/kalite/static/video-js", $replaced);
	$replaced = str_replace("/content/", "/kalite/content/", $replaced);
	$replaced = str_replace("/jsi18n/", "/kalite/jsi18n/", $replaced);
	$replaced = str_replace("/api/info", "/kalite/api/info", $replaced);
	$replaced = str_replace("/api/get", "/kalite/api/get", $replaced);
	$replaced = str_replace("/api/start", "/kalite/api/start", $replaced);
	$replaced = str_replace("/api/delete", "/kalite/api/delete", $replaced);
	$replaced = str_replace("/api/check_video", "/kalite/api/check_video", $replaced);
	$replaced = str_replace("/api/check_subtitle", "/kalite/api/check_subtitle", $replaced);
	$replaced = str_replace("/api/save", "/kalite/api/save", $replaced);
	$replaced = str_replace("/api/cancel", "/kalite/api/cancel", $replaced);
	$replaced = str_replace("/api/videos", "/kalite/api/videos", $replaced);
	$replaced = str_replace("/api/updates", "/kalite/api/updates", $replaced);
	$replaced = str_replace("/api/retry", "/kalite/api/retry", $replaced);
	$replaced = str_replace("/coachreports/api", "/kalite/coachreports/api", $replaced);
	$replaced = str_replace("/coachreports/table", "/kalite/coachreports/table", $replaced);
	$replaced = str_replace("/coachreports/scatter", "/kalite/coachreports/scatter", $replaced);
	$replaced = str_replace("/coachreports/timeline", "/kalite/coachreports/timeline", $replaced);
	$replaced = str_replace("/coachreports/student", "/kalite/coachreports/student", $replaced);
	$replaced = str_replace("/securesync/api/status", "/kalite/securesync/api/status", $replaced);
	$replaced = str_replace("/api/status", "/kalite/api/status", $replaced);
	$replaced = str_replace("/update/languages", "/kalite/update/languages", $replaced);
	$replaced = str_replace("/api/languagepacks", "/kalite/api/languagepacks", $replaced);
	$replaced = str_replace("/static/srt", "/kalite/static/srt", $replaced);
	$replaced = str_replace('preload="auto"', 'preload="none"', $replaced);
	return $replaced;
}

//--------------------------------------------------------------------------------------------------
//	Remove KA page frame to allow this document to be added to our frame
//--------------------------------------------------------------------------------------------------
//arg: pageStr - html page returned from a request to KA Lite [string]
//returns: the content of the passed page [string]


function trimKAPage($html) {
    $html = str_replace('<!DOCTYPE HTML>', '', $html);
    $html = str_replace('<html>', '', $html);
    $html = str_replace('<head>', '', $html);
    $html = str_replace('</head>', '', $html);
    $html = str_replace('<body class="light ">', '', $html);
    $html = str_replace('</body>', '', $html);
    $html = str_replace('</html>', '', $html);

    return $html;
}

//--------------------------------------------------------------------------------------------------
//	remove internal KA Links from html page
//--------------------------------------------------------------------------------------------------
//arg: pageStr - html page returned from a request to KA Lite [string]
//returns: the html page with the replaced values [string]
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
					or (strpos($pageStr, 'management/account', $start) and strpos($pageStr, 'Logout', $start) < $end)
					or (strpos($pageStr, 'Practice Lessons', $start) and strpos($pageStr, 'Logout', $start) < $end)
					or (strpos($pageStr, 'Watch Videos', $start) and strpos($pageStr, 'Logout', $start) < $end)
					) {
					$pageStr = substr_replace ($pageStr , '' , $start, $end - $start);
				} else {
					$start = $end;
				}
			}
		}
	}
	
	$start = strpos($pageStr, '<form action');
	$end = strpos($pageStr, '</form>') + 7;
	$pageStr = substr_replace ($pageStr , '' , $start, $end - $start);
	

	$replaced = str_replace('href="/"', '', $pageStr);
	
	return $replaced;
}

//--------------------------------------------------------------------------------------------------
//	logout from KA Lite
//--------------------------------------------------------------------------------------------------
function logoutKhanLite() {
	global $kapenta;
	
	$cookies = "";

	if (true == $kapenta->session->has('kalite_sessionid')) {
		$sessionid = $kapenta->session->get('kalite_sessionid');
		$cookies = 'sessionid='.$sessionid.';';
	}
	if (true == $kapenta->session->has('kalite_csrftoken')) {
		$csrftoken = 	$kapenta->session->get('kalite_csrftoken');
		$cookies = $cookies . 'csrftoken='.$csrftoken;
	}
	$reply = $kapenta->utils->curlGet($kapenta->registry->get('kalite.installation').'/securesync/logout/', '', false, $cookies);
	$kapenta->session->set('kalite_sessionid', '');
	$kapenta->session->set('kalite_csrftoken', '');	
//	$kapenta->logEvent('kalite', 'login', 'logging out', 'received reply:' . $reply);

	//----------------------------------------------------------------------------------------------
	//	ok, done
	//----------------------------------------------------------------------------------------------	
	return true;
}

//--------------------------------------------------------------------------------------------------
//	creates a KA Lite user account automatically
//--------------------------------------------------------------------------------------------------
//arg: type - student, teacher or admin [string]
//returns: the html page with the replaced values [string]
function createKhanLiteAccount($type) {
	global $kapenta;
	
	//--------------------------------------------------------------------------------------------------
	//	add awarenet user to khanlite
	//--------------------------------------------------------------------------------------------------
	$cookies = "";
	$sessionid = "";
	$csrftoken = "";
	if (true == $kapenta->session->has('kalite_sessionid')) {
		$sessionid = $kapenta->session->get('kalite_sessionid');
		$cookies = 'sessionid='.$sessionid.';';
	}
	if (true == $kapenta->session->has('kalite_csrftoken')) {
		$csrftoken = $kapenta->session->get('kalite_csrftoken');
		$cookies = $cookies . 'csrftoken='.$csrftoken;
	}
	
	$urlRequest = $kapenta->registry->get('kalite.installation').'/securesync/add/'.$type.'/';

	$reply = $kapenta->utils->curlGet(
        $kapenta->registry->get('kalite.installation').'/securesync/add/'.$type.'/', 
        '', 
	    false, 
        $cookies
    );

	if (0 < strlen($reply) ) {
		$start = strpos($reply, "name='csrfmiddlewaretoken' value='") + 34;
		$end = strpos($reply, "'", $start);
		$csrftoken = substr($reply, $start, $end - $start);
		//--------------------------------------------------------------------------------------------------
		//	extract facility id from page
		//--------------------------------------------------------------------------------------------------
		$index = strpos($reply, 'name="facility" value=');
		if (FALSE !== $index) {
			//--------------------------------------------------------------------------------------------------
			//	set up arguments for POST/add
			//--------------------------------------------------------------------------------------------------
			$pw = sha1($kapenta->user->password . '11111');
			$kapass = 'sha_1$11111$' . $pw;
			$start = strpos($reply, 'name="facility" value="', $index) + 23;
			$end = strpos($reply, '" id="id_facility"', $start);
			$facility = substr($reply, $start, $end - $start);
			$args = 'csrfmiddlewaretoken='.$csrftoken."&";
			$args = $args.'username='.$kapenta->user->username."&";
			$args = $args.'first_name='.$kapenta->user->firstname."&";
			$args = $args.'last_name='.$kapenta->user->surname."&";
			$args = $args.'password_first='.$kapass."&";
			$args = $args.'password_recheck='.$kapass."&";
			$args = $args.'facility='.$facility."&";
			if ('student' == $type) {
				$args = $args.'is_teacher=false';
			} else {
				$args = $args.'is_teacher=true';
			}
		
			$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
			$reply = $kapenta->utils->curlPost($kapenta->registry->get('kalite.installation').'/securesync/add/'.$type.'/', $args, 
					true, $cookies);
			$kapenta->logEvent('kalite', 'login', 'user created: ' . $kapenta->user->username, 'received reply:' . $reply);
		}	
	} else {
		$kapenta->logEvent('kalite', 'login', 'cookies', $cookies);
		$kapenta->logEvent('kalite', 'login', 'request', $urlRequest);
		$kapenta->logEvent('kalite', 'login', 'add User not working', $kapenta->user->username, 'received reply:' . $reply);
	}
}

//---------------------------------------------------------------------------------------------------
//	automatically logs in user to KA Lite
//--------------------------------------------------------------------------------------------------
//arg: username, password - credentials of specified user [string]
function loginToKhanLite($username, $password) {
	global $kapenta;

	$sessionid = '';
	
	//--------------------------------------------------------------------------------------------------
	//	get securesync/login page and extract csrftoken from it
	//--------------------------------------------------------------------------------------------------
	$kalite = $kapenta->registry->get('kalite.installation');
	$reply = $kapenta->utils->curlGet($kalite.'/securesync/login/', '', true, '');

    //echo "<textarea rows='10' style='width: 100%;'>$reply</textarea><br/>\n";

	$start = strpos($reply, 'Set-Cookie:  csrftoken=') + 23;
	$end = strpos($reply, '; expires=', $start);
	$csrftoken = substr($reply, $start, $end - $start);

	//--------------------------------------------------------------------------------------------------
	//	extract facility id from page
	//--------------------------------------------------------------------------------------------------
    $facility = '';
    $marker = '<select name="facility" id="id_facility">';

    if (FALSE !== strpos($reply, $marker)) {
    	$index = strpos($reply, $marker);
		$index = strpos($reply, '<option value="">---------</option', $index) + 7;
		$start = strpos($reply, 'option value="', $index) + 14;
		$end = strpos($reply, '" selected="selected">', $start);
		$facility = substr($reply, $start, $end - $start);

    }

    $marker = '<input type="hidden" name="facility" value="';
    if (FALSE !== strpos($reply, $marker)) {
        $start = strpos($reply, $marker) + strlen($marker);
        $end = strpos($reply, '"', $start + 1);
        $facility = substr($reply, $start, $end - $start);
        $kapenta->session->msgAdmin( "Found facility ID: $facility<br/>\n", 'ok' );
    }

	if ('' !== $facility) {
		//--------------------------------------------------------------------------------------------------
		//	set up arguments for POST/login
		//--------------------------------------------------------------------------------------------------
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

		//--------------------------------------------------------------------------------------------------
		//	POST login data and retrieve sessionid cookie
		//--------------------------------------------------------------------------------------------------
		$reply = $kapenta->utils->curlPost($kalite.'/securesync/login/', $args, true, 'csrftoken='.$csrftoken, 
			array('X-CSRFToken: '.$csrftoken));			

//        echo "Login response:<br/>\n<textarea rows='10' style='width:100%;'>$reply</textarea><br/>\n";

		$start = strpos($reply, 'Set-Cookie:  sessionid=') + 23;
		$end = strpos($reply, '; expires=', $start);
		$sessionid = substr($reply, $start, $end - $start);
	} else {
        $kapenta->session->msgAdmin('KALite facility not set or not readable.', 'bad');
    }
    
    $retarg = array();
    $retarg['sessionid'] =  $sessionid;
    $retarg['csrftoken'] = $csrftoken;
//	$kapenta->logEvent('kalite', 'login', 'logged in', 'as: ' . $user . ' received:' . print_r($retarg, true));
    
    return $retarg;
}

//--------------------------------------------------------------------------------------------------
//	execute SQL Statement with Sqllite db
//--------------------------------------------------------------------------------------------------
//arg: the Sql statement handle
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
}

//--------------------------------------------------------------------------------------------------
//	prepare SQL Statement with Sqllite db
//--------------------------------------------------------------------------------------------------
//arg: dbh - the database handle, statement - the Sql statement [string]
//returns: the Sql statement handle
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

    return $sth;
}

//--------------------------------------------------------------------------------------------------
//	Connect to KA Lite database
//--------------------------------------------------------------------------------------------------
//returns: the database handle
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

	return $dbh;
}

//--------------------------------------------------------------------------------------------------
//*	experimental interface to Khan Academy dataset
//--------------------------------------------------------------------------------------------------
/*
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
*/
?>
