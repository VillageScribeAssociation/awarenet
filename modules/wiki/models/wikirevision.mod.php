<?

//--------------------------------------------------------------------------------------------------
//	object for manging wiki revisions
//--------------------------------------------------------------------------------------------------

class WikiRevision {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure


	var $allRevisions;	// php enclosed wikicode

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function WikiRevision($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['refUID'] = '';
		$this->data['content'] = '';
		$this->data['editedBy'] = $user->date['UID'];
		$this->data['editedOn'] = mysql_datetime(time());
		$this->allRevisions = array();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($UID) {
		$ary = dbLoad('wikirevisions', $UID);
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
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'wikirevisions';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'refUID' => 'VARCHAR(30)',		
			'content' => 'TEXT',
			'type' => 'VARCHAR(50)',
			'reason' => 'VARCHAR(255)',
			'editedBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME' );

		$dbSchema['indices'] = array('UID' => '10', 'refUID' => '10');

		$dbSchema['nodiff'] = array( 'UID', 'refUID', 'content', 'type', 'reason', 
									 'editedBy', 'editedOn', 'recordAlias' );
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;
		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (authHas('wiki', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%wiki/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='%%serverPath%%wiki/" . $ary['recordAlias'] . "'>"
					 . "[read on &gt;&gt;]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------
		$ary['editedOnLong'] = date('jS F, Y', strtotime($ary['editedOn']));

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	delete an article and all its assets
	//----------------------------------------------------------------------------------------------

	function delete() {
		if (dbRecordExists('wikirevisions', $this->data['UID']) == false) { return false; }
		dbDelete('wikirevisions', $this->data['UID']);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	find UID of previous version of this wiki article or talk page
	//----------------------------------------------------------------------------------------------

	function getPreviousUID() {
		foreach($this->allRevisions as $key => $row) {	// for each revision
			if ($row['UID'] == $this->data['UID']) {	// if this one is found

				if ($key > 0) {
					$prev = $this->allRevisions[($key - 1)];
					return $prev['UID'];
				} else {
					return false;	// this is the first revision
				}

			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	find UID of next version of this wiki article or talk page
	//----------------------------------------------------------------------------------------------

	function getNextUID() {
		foreach($this->allRevisions as $key => $row) {	// for each revision
			if ($row['UID'] == $this->data['UID']) {	// if this one is found

				if ($key != (count($this->allRevisions) - 1)) {
					$next = $this->allRevisions[($key + 1)];
					return $next['UID'];
				} else {
					return false;	// this is the latest revision
				}

			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	find all revisions to this wiki article or talk page, make list at $this->allRevisions
	//----------------------------------------------------------------------------------------------

	function getAllRevisions() {
		if ($this->data['refUID'] == '') { return false; }
		$this->allRevisions = array();

		$sql = "select UID, refUID, type, reason, editedBy, editedOn from wikirevisions "
			 . "where refUID='" . $this->data['refUID'] . "' "
			 . "and type='" . $this->data['type'] . "' "
			 . "order by editedOn";	// least to most recent

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { $this->allRevisions[] = sqlRMArray($row); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Wiki Revisions</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create wiki table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('wikirevisions') == false) {	
			echo "crating table: wikirevisions\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created wikirevisions table and indices...<br/>';
		} else {
			$this->report .= 'wikirevisions table already exists...<br/>';	
		}

		return $report;
	}

}

?>
