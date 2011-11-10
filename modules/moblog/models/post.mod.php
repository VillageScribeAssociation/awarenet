<?

//--------------------------------------------------------------------------------------------------
//*	object for managing moblog posts
//--------------------------------------------------------------------------------------------------
//+ NOTES:
//+ content can contain a special '{fold}' keyword, which determines how much of the post is included
//+ in the summary.
//
//+ 'published' field allows posts to be invisible to the public while they are being composed or 
//+ until they should be released, values can be 'yes', 'no'.  admins and the user which created a 
//+ post can see unpublished content.

class Moblog_Post {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $school;			//_ varchar(33) [string]
	var $grade;				//_ varchar(30) [string]
	var $title;				//_ title [string]
	var $content;			//_ wyswyg [string]
	var $published;			//_ varchar(10) [string]
	var $commentCount;		//_ commentCount [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - alias or UID of a moblog post [string]

	function Moblog_Post($raUID = '') {
		global $db, $user;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Post ' . $this->UID;		// set default title
			$this->published = 'no';
			$this->school = $user->school;
			$this->grade = $user->grade;
			$this->commentCount = 0;
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Post object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Post object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->school = $ary['school'];
		$this->grade = $ary['grade'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->published = $ary['published'];
		$this->commentCount = $ary['commentCount'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
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
		$this->alias = $aliases->create('moblog', 'moblog_post', $this->UID, $this->title);
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
		if ('' == $this->title) { $report .= "Please choose a title for this post.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'moblog';
		$dbSchema['model'] = 'moblog_post';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'school' => 'VARCHAR(33)',
			'grade' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'content' => 'MEDIUMTEXT',
			'published' => 'VARCHAR(3)',
			'commentCount' => 'BIGINT(20)',
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
		$dbSchema['nodiff'] = array('alias');

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'school' => $this->school,
			'grade' => $this->grade,
			'title' => $this->title,
			'content' => $this->content,
			'published' => $this->published,
			'commentCount' => $this->commentCount,
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
	//returns: associative array of object properties in context of the current user [array]

	function extArray() {
		global $kapenta;
		global $user;
		global $theme;

		$ary = $this->toArray();				//%	return value [dict]

		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['nameLink'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	authorisation
		//------------------------------------------------------------------------------------------

		$editAuth = false;
		if ('admin' == $user->role) { $editAuth = true; }
		if ($user->UID == $ary['createdBy']) { $editAuth = true; }
		//TODO: full permission set checks

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('moblog', 'moblog_post', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%moblog/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[permalink]</a>";
			$ary['nameLink'] = "<a href='" . $ary['viewUrl'] . "'>" . $ary['title'] . "</a>";  
		}

		if ($editAuth) {
			$ary['editUrl'] =  '%%serverPath%%moblog/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if ($editAuth) {
			$ary['delUrl'] =  '%%serverPath%%moblog/confirmdelete/UID_' . $this->UID . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}

		if ($editAuth) { 
			$ary['newUrl'] = "%%serverPath%%moblog/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[new post]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	namespace conflict - TODO: remove this
		//------------------------------------------------------------------------------------------

		$ary['mbTitle'] = $ary['title'];
		$ary['mbContent'] = $ary['content'];

		//------------------------------------------------------------------------------------------
		//	user
		//------------------------------------------------------------------------------------------

		$model = new Users_User($ary['createdBy']);
		$ary['userName'] = '[[:users::name::userUID=' . $ary['createdBy'] . ':]]';
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['createdBy'];
		$ary['userLink'] = '[[:users::namelink::userUID=' . $ary['createdBy'] . ':]]';

		//------------------------------------------------------------------------------------------
		//	unpublished items
		//------------------------------------------------------------------------------------------

		$ary['unpublished'] = '';
		if ('no' == $ary['published'])
			{ $ary['unpublished'] = "<font color='red'><b>(unpublished)</b></font>"; }

		//------------------------------------------------------------------------------------------
		//	summarysm (first 300 chars, for nav summary)
		//------------------------------------------------------------------------------------------

		$ary['summarysm'] = $theme->makeSummary($ary['content'], 300);

		//------------------------------------------------------------------------------------------
		//	summary (clipped to {fold})
		//------------------------------------------------------------------------------------------
		$ary['aboveFold'] = trim($this->content);
		$foldPos = strpos($ary['aboveFold'], '{fold}');
		if ($foldPos > 0) { $ary['aboveFold'] = substr($ary['aboveFold'], 0, $foldPos);	}
		$ary['aboveFold'] = str_replace("\n", "<br/>\n", $ary['aboveFold']);

		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['content']);
		$ary['contentHtml'] = str_replace('{fold}', '', $ary['contentHtml']);

		$ary['createdOnLong'] = $kapenta->longDatetime($ary['createdOn']);
		$ary['editedOnLong'] = $kapenta->longDatetime($ary['editedOn']);		

		//------------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
	
		$ary['content'] = str_replace('{fold}', '', $ary['content']);

		//------------------------------------------------------------------------------------------
		//	all done
		//------------------------------------------------------------------------------------------

		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//. perform maintenance tasks on this object
	//----------------------------------------------------------------------------------------------
	//returns: array of HTML notes on any action taken [array:string:html]

	function maintain() {
		global $aliases;
		global $theme;
		$notes = array();

		// article must have a title
		if ('' == $this->title) { 
			$this->title = 'Untitled Blog Post ' . $this->UID; 
			$this->save();
			$notes[] = "Set null title of static page to " . $this->title
					 . ".<!-- error --><!-- fixed -->";
		}

		// article must have at least one alias
		$als = $aliases->getAll('moblog', 'moblog_post', $this->UID);
		if (0 == count($als)) {
			$this->save();
			$als = $aliases->getAll('moblog', 'moblog_post', $this->UID);
			if (0 == count($als)) {
				$notes[] = "Could not create alias.<!-- error -->";
			} else { 
				$notes[] = "Re-saved to create alias " . $this->alias 
						 . ".<!-- error --><!-- fixed -->";
			}
		}

		// check that the default alias is in the list
		$foundAlias = false;

		foreach($als as $alias) { 
			if (strtolower($alias) == strtolower($this->alias)) { $foundAlias = true; } 
		}

		if (false == $foundAlias) {
			$this->save();
			$notes[] = "Re-saved to correct alias.<!-- error --><!-- fixed -->";
		}

		// check comment count

		$block = '[[:comments::count'
				. '::refModule=moblog'
				. '::refModel=moblog_post'
				. '::refUID=' . $this->UID . ':]]';

		$commentCount = $theme->expandBlocks($block, '');
		if ($commentCount != $this->commentCount) {
			$this->commentCount = $commentCount;
			$this->save();
			$notes[] = "Changed comment count to $commentCount.<!-- error --><!-- fixed -->";
		}

		return $notes;
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

	//----------------------------------------------------------------------------------------------
	//.	increment hit count
	//----------------------------------------------------------------------------------------------

	function incrementcommentCount() { 
		//TODO: this
		//$db->updateQuiet('moblog', $this->UID, 'commentCount', ($this->data['commentCount'] + 1)); 
	}

}

?>
