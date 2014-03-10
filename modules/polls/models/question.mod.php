<?

//--------------------------------------------------------------------------------------------------
//*	Records question to which answers are attached
//--------------------------------------------------------------------------------------------------

class Polls_Question {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;				//_	database table definition [array]
	var $loaded = false;		//_	set to true when an object has been loaded [bool]

	var $UID;					//_ UID [string]
	var $refModule;				//_ module [string]
	var $refModel;				//_ model [string]
	var $refUID;				//_ ref:*-* [string]
	var $content;				//_ wyswyg [string]
	var $createdOn;				//_ datetime [string]
	var $createdBy;				//_ ref:users_user [string]
	var $editedOn;				//_ datetime [string]
	var $editedBy;				//_ ref:users_user [string]

	var $answers;				//_	array of serialized Polls_Answer objects [array:array]
	var $answersLoaded = false;	//_	set to true when answers loaded from db [bool]

	var $results;				//_	array of answerUID => score [array]
	var $resultsLoaded = false;	//_	set to true when results are loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Question object [string]

	function Polls_Question($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Question object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Question object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->content = $ary['content'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a given object has a question attached to it
	//----------------------------------------------------------------------------------------------
	//returns: UID of question if found, empty string if not [string]

	function hasQuestion($refModule, $refModel, $refUID) {
		global $kapenta;

		$conditions = array();
		$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";
		$conditions[] = "refModel='" . $kapenta->db->addMarkup($refModel) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";

		$range = $kapenta->db->loadRange('polls_question', '*', $conditions);
		foreach($range as $item) { return $item['UID']; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'polls';
		$dbSchema['model'] = 'polls_question';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'refModule' => 'VARCHAR(50)',
			'refModel' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(30)',
			'content' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'content'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'content' => $this->content,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('polls', 'polls_question', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%polls/showquestion/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('polls', 'polls_question', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%polls/editquestion/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('polls', 'polls_question', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%polls/delquestion/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//==============================================================================================
	//	answers
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	load all answers to this question from the database
	//----------------------------------------------------------------------------------------------
	//returns: array of serialized Polls_Answer objects [array:array]
	
	function loadAnswers() {
		global $kapenta;
 
		$conditions = array();
		$conditions[] = "question='" . $kapenta->db->addMarkup($this->UID) . "'";

		$range = $kapenta->db->loadRange('polls_answer', '*', $conditions, 'weight ASC');

		$this->answers = $range;
		$this->answersLoaded = true;
		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	gets the heaviest item in the set of answers
	//----------------------------------------------------------------------------------------------
	//returns: weight of heaviest answer, 0 if no answers [int]

	function getMaxWeight() {
		$maxWeight = 0;		//%	return value [int]

		if (false == $this->answersLoaded) { $this->loadAnswers(); }
		foreach($this->answers as $item) {
			if ((int)$item['weight'] > $maxWeight) { $maxWeight = (int)$item['weight']; }
		}

		return $maxWeight;
	}

	//==============================================================================================
	//	votes
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	discover is a given user has voted on this question
	//----------------------------------------------------------------------------------------------
	//returns: true if user has voted, false if not [bool]

	function hasVoted($userUID) {
		global $kapenta;		

		$conditions = array();
		$conditions[] = "question='" . $kapenta->db->addMarkup($this->UID) . "'";
		$conditions[] = "createdBy='" . $kapenta->db->addMarkup($userUID) . "'";
		$range = $kapenta->db->loadRange('polls_vote', '*', $conditions);

		if (0 == count($range)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	load results of poll from database
	//----------------------------------------------------------------------------------------------
	//returns: array of answerUID => score [array]

	function loadResults() {
		global $kapenta;
		$scores = array();					//%	return value [array]

		if (false == $this->answersLoaded) { $this->loadAnswers(); }
		foreach($this->answers as $item) { $scores[$item['UID']] = 0; }

		$sql = "select answer, count(UID) as score from polls_vote "
			 . "where `question`='" . $kapenta->db->addMarkup($this->UID) . "'"
			 . "group by `answer`";

		$result = $kapenta->db->query($sql);
		while ($row = $kapenta->db->fetchAssoc($result)) {
			$scores[$row['answer']] = (int)$row['score'];
		}

		$this->results = $scores;
		$this->resultsLoaded = true;	
		return $scores;
	}

	//----------------------------------------------------------------------------------------------
	//.	get total number of votes cast on this poll
	//----------------------------------------------------------------------------------------------
	//returns: number of votes [int]

	function getTotalVotes() {
		if (false == $this->resultsLoaded) { $this->loadResults(); }
		$total = 0;					//%	return value [int]
		foreach($this->results as $answerUID => $score) { $total = $total + $score; }
		return $total;
	}

	//----------------------------------------------------------------------------------------------
	//.	get largest vote count for a single answer
	//----------------------------------------------------------------------------------------------
	//returns: number of votes [int]

	function getMaxVotes() {
		if (false == $this->resultsLoaded) { $this->loadResults(); }
		$max = 0;					//%	return value [int]
		foreach($this->results as $answerUID => $score) { 
			if ($score > $max) { $max = $score; }
		}
		return $max;
	}

}

?>
