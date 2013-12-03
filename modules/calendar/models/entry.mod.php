<?

//--------------------------------------------------------------------------------------------------
//*	object for managing calendar entries
//--------------------------------------------------------------------------------------------------

class Calendar_Entry {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			//_	currently loaded database record [array]
	var $dbSchema;		//_	database table definition [array]
	var $loaded;		//_	set to true when an object has been loaded [bool]

	var $UID;			//_ UID [string]
	var $title;			//_ title [string]
	var $category;		//_ varchar(100) [string]
	var $venue;			//_ varchar(255) [string]
	var $content;		//_ wyswyg [string]
	var $year;			//_ varchar(10) [string]
	var $month;			//_ varchar(10) [string]
	var $day;			//_ varchar(10) [string]
	var $eventStart;	//_ varchar(50) [string]
	var $eventEnd;		//_ varchar(50) [string]
	var $published;		//_ varchar(30) [string]
	var $createdOn;		//_ datetime [string]
	var $createdBy;		//_ ref:Users_User [string]
	var $editedOn;		//_ datetime [string]
	var $editedBy;		//_ ref:Users_User [string]
	var $alias;			//_ alias [string]


	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a calendar entry [string]

	function Calendar_Entry($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Calendar Item ' . $this->UID;
			$this->venue = '';
			$this->year = date('Y');
			$this->month = date('m');
			$this->day = date('d');
			$this->eventStart = '00:00';
			$this->eventEnd = '00:00';
			$this->published = 'yes';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Entry object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID = '') {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Entry object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		//if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->category = $ary['category'];
		$this->venue = $ary['venue'];
		$this->content = $ary['content'];
		$this->year = $ary['year'];
		$this->month = $ary['month'];
		$this->day = $ary['day'];
		$this->eventStart = $ary['eventStart'];
		$this->eventEnd = $ary['eventEnd'];
		$this->published = $ary['published'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
	
		$this->published = 'yes';		//TODO: complete this feature
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('calendar', 'calendar_entry', $this->UID, $this->title);
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}


	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'calendar';
		$dbSchema['model'] = 'calendar_entry';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'category' => 'VARCHAR(100)',
			'venue' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'year' => 'VARCHAR(10)',
			'month' => 'VARCHAR(10)',
			'day' => 'VARCHAR(10)',
			'eventStart' => 'VARCHAR(50)',
			'eventEnd' => 'VARCHAR(50)',
			'published' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'title',
			'category',
			'venue',
			'content',
			'year',
			'month',
			'day',
			'eventStart',
			'eventEnd',
			'published' );

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'title' => $this->title,
			'category' => $this->category,
			'venue' => $this->venue,
			'content' => $this->content,
			'year' => $this->year,
			'month' => $this->month,
			'day' => $this->day,
			'eventStart' => $this->eventStart,
			'eventEnd' => $this->eventEnd,
			'published' => $this->published,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user, $theme;
		$ary = $this->toArray();
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['nameLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('calendar', 'calendar_entry', 'show', $this->UID)) {
			$ary['viewUrl'] = '%%serverPath%%calendar/' . $ary['alias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>";
			$ary['nameLink'] = "<a href='" . $ary['viewUrl'] . "'>" . $ary['title'] . "</a>";  
		}

		if (true == $user->authHas('calendar', 'calendar_entry', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%calendar/edit/' . $ary['alias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (true == $user->authHas('calendar', 'calendar_entry', 'delete', $this->UID)) {
			$ary['delUrl'] =  '%%serverPath%%calendar/confirmdelete/UID_'. $this->UID .'/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (true == $user->authHas('calendar', 'calendar_entry', 'new', $this->UID)) {
			$ary['newUrl'] = "%%serverPath%%calendar/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new coin]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------
	
		$ary['summary'] = $theme->makeSummary($ary['content'], 300);

		$ary['contentJs'] = $ary['content'];
		$ary['contentJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);

		$ary['userLink'] = '<b>Created By:</b> [[:users::namelink::raUID=' . $ary['createdBy'] . ':]]';
		$ary['venueString'] = '<b>Venue:</b> ' . $ary['venue'];
		$ary['eventStartString'] = '<b>Starting:</b> ' . $ary['eventStart'];
		$ary['eventEndString'] = '<b>Ending:</b> ' . $ary['eventEnd'];

		if ('' == trim($ary['venue'])) { $ary['venueString'] = ' '; }
		if ('00:00' == trim($ary['eventStart'])) { $ary['eventStartString'] = ' '; }
		if ('00:00' == trim($ary['eventEnd'])) { $ary['eventEndString'] = ' '; }

		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	draw a calendar month
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//arg: days - an array of day => bgcolor,label [array]
	//arg: size - may be rendered large for content column or sidebar size (large|small) [string]
	//returns: html [string]
	
	function drawMonthTable($month, $year, $days, $size) {		
		global $kapenta;
		$index = 0; $skip = 0; $blocks = array();
		$numDays = $this->daysInMonth($month, $year);
		$firstDay = $this->firstDayOfMonth($month, $year);
		$html = '';
		
		//------------------------------------------------------------------------------------------
		//	empty blocks at beginning of month
		//------------------------------------------------------------------------------------------
				
		switch($firstDay) {
			case 'Mon':	$skip = 0; break;
			case 'Tue':	$skip = 1; break;
			case 'Wed':	$skip = 2; break;
			case 'Thu':	$skip = 3; break;
			case 'Fri':	$skip = 4; break;
			case 'Sat':	$skip = 5; break;
			case 'Sun':	$skip = 6; break;
		}
		
		while ($skip > 0) {
			$blocks[] = "<td></td>\n";
			$skip--;
		}

		//------------------------------------------------------------------------------------------
		//	draw the blocks
		//------------------------------------------------------------------------------------------
		
		foreach($days as $dayNum => $day) {
			if (false == array_key_exists('label', $day)) { $day['label'] = ''; }
			$dayLink = "onClick=\"window.location='" . $kapenta->serverPath . "calendar/"
					 . "day_" . $year . "_" . $month . "_" . $dayNum . "'\"";

			if ($size == 'large') {
			  $blocks[] = "<td bgcolor='" . $day['bgcolor'] . "' width='80' valign='top' $dayLink>" 
				    . "<h3>$dayNum</h3>" . $day['label'] . "</td>\n";

			} else {
			  $blocks[] = "<td bgcolor='" . $day['bgcolor'] . "' width='40' style='cursor: pointer;' $dayLink>$dayNum</td>";
			}
		}
		
		$html .= "<h2><a class='black' href='/calendar/month_" . $year . "_" . $month . "'>" 
			. $this->getMonthName($month) . ' ' . $year . "</a></h2>";
		$html .= "<table noborder>\n";
		$html .= "<tr><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td>" 
			. "<td>Sat</td><td>Sun</td></tr>\n";
		
		$day = 0;
		foreach($blocks as $block) {
			if ($day == 0) { $html .= "<tr>\n"; }
			$html .= $block;
			$day++;
			if ($day == 7) { $day = 0; $html .= "</tr>\n"; }
		}
		
		$html .= "</table>\n";
		return $html;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	find out how many days are in a month
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//,	month is 01 through 12, year is four digits
	
	function daysInMonth($month, $year) {
		$leap = false;
		$days = 31;
		
		if (((($year % 4) == 0) AND (($year % 100) != 0)) OR (($year % 400) == 0)) 
				{ $leap = true; }
		if (($month == '04') OR ($month == '06') OR ($month == '09') OR ($month == '11')) 
				{ $days = 30; }

		if ($month == '02') { if ($leap == true) { $days = 29; } else { $days = 28; } }
		
		return $days;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	find first day of month (mon-sun)
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: 3 letter day abbreviation [string]
	//,	month is 01 through 12, year is four digits


	function firstDayOfMonth($month, $year) {
		global $kapenta;
		$ts = $kapenta->strtotime($year . '-' . $month . '-01');
		return date("D", $ts);
	}
	
	//----------------------------------------------------------------------------------------------
	//.	get month name, 01 => January
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//returns: full name of month or false on failure [string][bool]

	function getMonthName($month) {
		switch($month) {
			case '01': return 'January';
			case '02': return 'February';
			case '03': return 'March';
			case '04': return 'April';
			case '05': return 'May';
			case '06': return 'June';
			case '07': return 'July';
			case '08': return 'August';
			case '09': return 'September';
			case '10': return 'October';
			case '11': return 'November';
			case '12': return 'December';
		}
		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	get day name, Monday - Sunday
	//----------------------------------------------------------------------------------------------
	//arg: day - two digit day number ('00'-'31') [string]
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: full day name (Monday-Sunday) [string]

	function getDayName($day, $month, $year) {
		global $kapenta;
		$ts = $kapenta->strtotime($year . '-' . $month . '-' . $day);
		return date("l", $ts);
	}
	
	//----------------------------------------------------------------------------------------------
	//.	load a month's worth of data into an array
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: nested array of calendar entries [array]	

	function loadMonth($month, $year) {
		global $db;
		$retVal = array();

		if (1 == strlen(trim($month))) { $month = '0' . $month; }

		$conditions = array();
		$conditions[] = "year='" . $db->addMarkup($year) . "'";
		$conditions[] = "month='" . $db->addMarkup($month) . "'";
		$conditions[] = "published='yes'";

		$range = $db->loadRange('calendar_entry', '*', $conditions, 'year, month, day, eventStart');

		// $sql = "select * from Calendar_Entry where year='". $db->addMarkup($year) ."' and month=" 
		//     . $db->addMarkup($month) . " and published='yes' order by year, month, day, eventStart";
	
		foreach($range as $row) { $retVal[$row['UID']] = $row; }
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a day's worth of data into an array
	//----------------------------------------------------------------------------------------------
	//arg: day - two digit day number ('00'-'31') [string]
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: nested array of calendar entries [array]	

	function loadDay($day, $month, $year) {
		global $db;
		$retVal = array();

		if (1 == strlen($day)) { $day = '0' . $day; }

		$conditions = array();
		$conditions[] = "year='" . $db->addMarkup($year) . "'";
		$conditions[] = "month='" . $db->addMarkup($month) . "'";
		$conditions[] = "day='" . $db->addMarkup($day) . "'";
		$conditions[] = "published='yes'";

		$range = $db->loadRange('calendar_entry', '*', $conditions, 'year, month, day, eventStart');

		// $sql = "select * from Calendar_Entry where year='". $db->addMarkup($year) ."' and month=" 
		//   . $db->addMarkup($month) . " and day=" . $db->addMarkup($day) . " and published='yes' " 
		//   . "order by year, month, day, eventStart";
	
		foreach($range as $row) { $retVal[$row['UID']] = $row; }
		return $retVal;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	make an html calendar for a given month, $size = large|small, $month = mm, $year = yyyy
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//arg: size - may be rendered large for content column or sidebar size (large|small) [string]
	//returns: html table [string]

	function drawMonth($month, $year, $size) {

		//------------------------------------------------------------------------------------------
		//	make days array
		//------------------------------------------------------------------------------------------
		$numDays = $this->daysInMonth($month, $year);
		$days = array();
		for ($i = 1; $i <= $numDays; $i++) {
			$days[$i] = array('bgcolor' => '#bbbbbb', 'numEvents' => 0);
		}
		
		//------------------------------------------------------------------------------------------
		//	populate with events for this month
		//------------------------------------------------------------------------------------------
		
		$events = $this->loadMonth($month, $year);
		foreach($events as $UID => $row) {
		  $day = floor($row['day']) . '';
		  if (array_key_exists($day, $days)) {
			$days[$day]['numEvents'] += 1;
		  }
		}
		
		//------------------------------------------------------------------------------------------
		//	update bgcolor and label
		//------------------------------------------------------------------------------------------

		foreach($days as $idx => $day) {
			if ($day['numEvents'] > 0) {
				$days[$idx]['label'] = $day['numEvents'] . ' events';
				$days[$idx]['bgcolor'] = '#7777bb';
			}
			if ( ($month == date('m')) AND ($year == date('Y')) AND ($idx == date('j')) ) {
				$days[$idx]['bgcolor'] = '#77bb77';
			}
		}
		
		//------------------------------------------------------------------------------------------
		//	render to html
		//------------------------------------------------------------------------------------------

		return $this->drawMonthTable($month, $year, $days, $size);
	}

	//----------------------------------------------------------------------------------------------
	//.	load upcoming events in a certain category
	//----------------------------------------------------------------------------------------------
	//arg: category - an event category [string]
	//arg: num - maximum number of entrie to return [string]
	//returns: nested array of calendar entries [array]	

	function loadUpcoming($category, $num) {
	global $db;

		$retVal = array();
		
		$conditions = array();
		$conditions[] = "category='" . $db->addMarkup($category) . "'";
		$conditions[] = "year >= " . date('Y');
		$conditions[] = "month >= " . date('m');
		$conditions[] = "day >= " . date('j');
		$conditions[] = "published='yes'";

		//$sql = "select * from Calendar_Entry where category='" . $category . "' and year >= " . date('Y') 
		//	  . " and month >= " . date('m') . " and day >= " . date('j') . " and published='yes' " 
		//    . "order by year, month, day, eventStart limit " . (int)$num;
	
		$by = "year, month, day, eventStart";
		
		$range = $db->loadRange('calendar_entry', '*', $conditions, $by, (int)$num, '');

		foreach($range as $row) { $retVal[$row['UID']] = $db->rmArray($row); }
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//.	load all upcoming events 
	//----------------------------------------------------------------------------------------------
	//arg: num - maximum number of entrie to return [string]	
	//returns: nested array of calendar entries [array]	

	function loadAllUpcoming($num) {
		global $db;

		$retVal = array();

		$conditions = array();
		$conditions[] = "year >= " . date('Y');
		$conditions[] = "month >= " . date('m');
		$conditions[] = "day >= " . date('j');
		$conditions[] = "published='yes'";

		$by = "year, month, day, eventStart";

		//$sql = "select * from Calendar_Entry where year >= " . date('Y') . " and month >= " 
		//     . date('m') . " and day >= " . date('j') . " and published='yes' " 
		//     . "order by year, month, day, eventStart limit $num";
	
		$range = $db->loadRange('calendar_entry', '*', $conditions, $by, (int)$num, '');

		foreach($range as $row) { $retVal[$row['UID']] = $db->rmArray($row); }
		return $retVal;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	ensure a number has two digits (9 => 09)
	//----------------------------------------------------------------------------------------------
	//arg: num - string representing a number [string]
	//returns: two digit number [string]

	function twoDigits($num) {
		if (strlen($num) == 2) { return $num; }
		return '0' . $num;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	find the next calendar month ()
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: array of {year, month} [array]

	function getNextMonth($month, $year) {
		$next = array();
		$next['month'] = $month;
		$next['year'] = $year;
		switch($month) {
			case '12': $next['month'] = '01'; $next['year'] += 1; return $next;
			default: $next['month'] = $this->twodigits($month + 1); return $next;
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	find the previous calendar month (returns array [year][month])
	//----------------------------------------------------------------------------------------------
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: array of {year, month} [array]
	
	function getPrevMonth($month, $year) {
		$next = array();
		$next['month'] = $month;
		$next['year'] = $year;
		switch($month) {
			case '01': $next['month'] = '12'; $next['year'] -= 1; return $next;
			default: $next['month'] = $this->twodigits($month - 1); return $next;
		}
		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	find the next calendar day (returns array [year][month][day])
	//----------------------------------------------------------------------------------------------
	//arg: day - two digit day number ('01'-'31') [string]
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: array of {year, month, day} [array]
	
	function getNextDay($day, $month, $year) {
		global $kapenta;
		$ts = $kapenta->strtotime($year . '-' . $month . '-' . $day);
		$ts += (60 * 60 * 24); // add one day
		$next = array();
		$next['day'] = date('d', $ts);
		$next['month'] = date('m', $ts);
		$next['year'] = date('Y', $ts);
		return $next;
	}

	//----------------------------------------------------------------------------------------------
	//.	find the previous calendar day (returns array [year][month][day])
	//----------------------------------------------------------------------------------------------
	//arg: day - two digit day number ('01'-'31') [string]
	//arg: month - month number ('01'-'12') [string]
	//arg: year - four digit year [string]
	//returns: array of {year, month, day} [array]		

	function getPrevDay($day, $month, $year) {
		global $kapenta; 
		$ts = $kapenta->strtotime($year . '-' . $month . '-' . $day);
		$ts -= (60 * 60 * 24); // add one day
		$prev = array();
		$prev['day'] = date('d', $ts);
		$prev['month'] = date('m', $ts);
		$prev['year'] = date('Y', $ts);
		return $prev;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}
	
}

?>
