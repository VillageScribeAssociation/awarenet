<?

//--------------------------------------------------------------------------------------------------
//	object for managing calendar posts
//--------------------------------------------------------------------------------------------------

class Calendar {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;		// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Calendar($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] = 'New Calendar Item';
		$this->data['venue'] = '';
		$this->data['year'] = date('Y');
		$this->data['month'] = date('m');
		$this->data['day'] = date('d');
		$this->data['eventStart'] = '00:00';
		$this->data['eventEnd'] = '00:00';
		$this->data['published'] = 'no';
		$this->data['createdOn'] = mysql_datetime();
		$this->data['createdBy'] = $_SESSION['sUserUID'];
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('calendar', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('calendar', $d['UID'], $d['title'], 'calendar');
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'calendar';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'title' => 'VARCHAR(255)',
			'category' => 'VARCHAR(100)',	
			'venue' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'year' => 'VARCHAR(5)',
			'month' => 'VARCHAR(5)',
			'day' => 'VARCHAR(5)',
			'eventStart' => 'VARCHAR(50)',
			'eventEnd' => 'VARCHAR(50)',
			'createdOn' => 'DATETIME',	
			'createdBy' => 'VARCHAR(30)',
			'published' => 'VARCHAR(30)',
			'hitcount' => 'VARCHAR(30)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20', 'category' => '20');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('calendar', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%calendar/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (authHas('calendar', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%calendar/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('calendar', 'edit', $this->data)) {
			$ary['delUrl'] =  '%%serverPath%%calendar/confirmdelete/UID_'. $this->data['UID'] .'/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (authHas('calendar', 'new', $this->data)) { 
			$ary['newUrl'] = "%%serverPath%%calendar/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new coin]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------
	
		$ary['summary'] = substr($ary['content'], 0, 300);

		$ary['contentJs'] = $ary['content'];
		$ary['contectJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);

		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing calendar Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create calendar table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('calendar') == false) {	
			echo "installing calendar module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created calendar table and indices...<br/>';
		} else {
			$this->report .= 'calendar table already exists...<br/>';	
		}

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//	draw a calendar month
	//----------------------------------------------------------------------------------------------
	//	$days is an array ([day] => [bgcolor][label]
	
	function drawMonthTable($month, $year, $days, $size) {		
		global $serverPath;
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
			$dayLink = "onClick=\"window.location='" . $serverPath . "calendar/day_" . $year . "_" 
						. $month . "_" . $dayNum . "'\"";

			if ($size == 'large') {
			  $blocks[] = "<td bgcolor='" . $day['bgcolor'] . "' width='80' valign='top' $dayLink>" 
				    . "<h3>$dayNum</h3>" . $day['label'] . "</td>\n";

			} else {
			  $blocks[] = "<td bgcolor='" . $day['bgcolor'] . "' width='40' $dayLink>$dayNum</td>";
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
	//	find out how many days are in a month
	//----------------------------------------------------------------------------------------------
	//	month is 01 through 12, year is four digits
	
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
	//	find first day of month (mon-sun)
	//----------------------------------------------------------------------------------------------

	function firstDayOfMonth($month, $year) {
		$ts = strtotime($year . '/' . $month . '/01');
		return date("D", $ts);
	}
	
	//----------------------------------------------------------------------------------------------
	//	get month name, 01 => January
	//----------------------------------------------------------------------------------------------

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
	//	get day name, Monday - Sunday
	//----------------------------------------------------------------------------------------------

	function getDayName($day, $month, $year) {
		$ts = strtotime($year . '/' . $month . '/' . $day);
		return date("l", $ts);
	}
	
	//----------------------------------------------------------------------------------------------
	//	load a month's worth of data into an array
	//----------------------------------------------------------------------------------------------

	function loadMonth($month, $year) {
		$retVal = array();
		$sql = "select * from calendar where year='" . sqlMarkup($year) . "' and month=" 
		     . sqlMarkup($month) . " and published='yes' order by year, month, day, eventStart";
	
		$result = dbQuery($sql);
		while($row = dbFetchAssoc($result)) { $retVal[$row['UID']] = sqlRMArray($row); }
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	load a day's worth of data into an array
	//----------------------------------------------------------------------------------------------

	function loadDay($day, $month, $year) {
		$retVal = array();
		$sql = "select * from calendar where year='" . sqlMarkup($year) . "' and month=" 
		     . sqlMarkup($month) . " and day=" . sqlMarkup($day) . " and published='yes' " 
		     . "order by year, month, day, eventStart";
	
		$result = dbQuery($sql);
		while($row = dbFetchAssoc($result)) { $retVal[$row['UID']] = sqlRMArray($row); }
		return $retVal;
	}
	
	//----------------------------------------------------------------------------------------------
	//	make an html calendar for a given month, $size = large|small, $month = mm, $year = yyyy
	//----------------------------------------------------------------------------------------------

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
	//	load upcoming events in a certain category
	//----------------------------------------------------------------------------------------------

	function loadUpcoming($category, $num) {
		$retVal = array();
		$sql = "select * from calendar where category='" . $category . "' and year >= " . date('Y') 
			 . " and month >= " . date('m') . " and day >= " . date('j') . " and published='yes' " 
		     . "order by year, month, day, eventStart limit $num";
	
		$result = dbQuery($sql);
		while($row = dbFetchAssoc($result)) { $retVal[$row['UID']] = sqlRMArray($row); }
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	load all upcoming events 
	//----------------------------------------------------------------------------------------------
	
	function loadAllUpcoming($num) {
		$retVal = array();
		$sql = "select * from calendar where year >= " . date('Y') . " and month >= " 
		     . date('m') . " and day >= " . date('j') . " and published='yes' " 
		     . "order by year, month, day, eventStart limit $num";
	
		$result = dbQuery($sql);
		while($row = dbFetchAssoc($result)) { $retVal[$row['UID']] = sqlRMArray($row); }
		return $retVal;
	}
	
	//----------------------------------------------------------------------------------------------
	//	ensure a number has two digits (9 => 09)
	//----------------------------------------------------------------------------------------------

	function twoDigits($num) {
		if (strlen($num) == 2) { return $num; }
		return '0' . $num;
	}
	
	//----------------------------------------------------------------------------------------------
	//	find the next calendar month (returns array [year][month])
	//----------------------------------------------------------------------------------------------
	
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
	//	find the previous calendar month (returns array [year][month])
	//----------------------------------------------------------------------------------------------
	
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
	//	find the next calendar day (returns array [year][month][day])
	//----------------------------------------------------------------------------------------------
	
	function getNextDay($day, $month, $year) {
		$ts = strtotime($year . '/' . $month . '/' . $day);
		$ts += (60 * 60 * 24); // add one day
		$next = array();
		$next['day'] = date('d', $ts);
		$next['month'] = date('m', $ts);
		$next['year'] = date('Y', $ts);
		return $next;
	}

	//----------------------------------------------------------------------------------------------
	//	find the previous calendar day (returns array [year][month][day])
	//----------------------------------------------------------------------------------------------
	
	function getPrevDay($day, $month, $year) {
		$ts = strtotime($year . '/' . $month . '/' . $day);
		$ts -= (60 * 60 * 24); // add one day
		$prev = array();
		$prev['day'] = date('d', $ts);
		$prev['month'] = date('m', $ts);
		$prev['year'] = date('Y', $ts);
		return $prev;
	}

	//----------------------------------------------------------------------------------------------
	//	delete the current entry
	//----------------------------------------------------------------------------------------------
	
	function delete() {
		$thisUID = sqlMarkup($this->data['UID']);
		$sql = "delete from images where refModule='calendar' and refUID='" . $thisUID . "'";
		dbQuery($sql);
		$sql = "delete from files where refModule='calendar' and refUID='" . $thisUID. "'";
		dbQuery($sql);
		
		raDeleteAll('calendar', $this->data['UID']);
		dbDelete('calendar', $this->data['UID']);
	}

	
}

?>
