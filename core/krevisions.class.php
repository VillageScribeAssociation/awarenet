<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');
	require_once($kapenta->installPath . 'modules/revisions/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	interface to revisioning system
//--------------------------------------------------------------------------------------------------
//+	This component keeps track of changes to module objects and archives them after deletion from
//+	the live system.  Note that revision control is performed according to the 'nodiff' array
//+	of model's dbSchema, and that objects marked 'archive' => 'no' are not kept.

class KRevisions {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	// none yet

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KRevisions() {
		// nothing at present, may in future read settings controlling behavior of this object
	}

	//----------------------------------------------------------------------------------------------
	//.	record changes to an object
	//----------------------------------------------------------------------------------------------
	//arg: changes - associative array of fields and values [array]
	//arg: dbSchema - a database table definition [array]		
	//arg: UID - UID of the object to be stored [string]
	//returns: true on success, false on failure [bool]

	function storeRevision($changes, $dbSchema, $UID) {
		global $db;
		if (false == $db->checkSchema($dbSchema)) { return false; }

		if ((true == array_key_exists('archive', $dbSchema)) && ('no' == $dbSchema['archive'])) 
			{ return false; }

		$model = new Revisions_Revision();
		$model->refModule = $dbSchema['module'];
		$model->refModel = $dbSchema['model'];
		$model->refUID = $UID;
		$model->fields = $changes;
		$report = $model->save();

		if ('' != $report) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	record deleteion of an object
	//----------------------------------------------------------------------------------------------
	//arg: changes - associative array of fields and values [array]
	//arg: dbSchema - a database table definition [array]		
	//returns: true on success, false on failure [bool]

	function recordDeletion($fields, $dbSchema) {
		global $db, $session;

		if (false == $db->checkSchema($dbSchema)) { 
			$session->msgAdmin('revisions->recordDeletion() BAD SCHEMA', 'bad');
			return false;
		}

		// check whether objects of this type are not archived
		if ((true == array_key_exists('archive', $dbSchema)) && ('no' == $dbSchema['archive'])) {
			$session->msgAdmin('revisions->recordDeletion() NO ARCHIVE', 'bad');
			return false;
		}

		// check whether object has already been deleted
		if (true == $this->isDeleted($dbSchema['model'], $dbSchema['fields']['UID'])) {
			$session->msgAdmin('revisions->recordDeletion() ALREADY DELETED', 'bad');
			return false;
		}

		$model = new Revisions_Deleted();
		$model->refModule = $dbSchema['module'];
		$model->refModel = $dbSchema['model'];
		$model->refUID = $fields['UID'];
		$model->fields = $fields;
		$report = $model->save();

		if ('' != $report) { 
			$session->msgAdmin('revisions->recordDeletion() COULD NOT SAVE:' . $report, 'bad');
			return false;
		}
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if an object has been deleted
	//----------------------------------------------------------------------------------------------
	//arg: model - type of deleted object [string]
	//arg: UID - Unique identifier of deleted object [string]
	//returns: true if item is in recycle bin, false if not [bool]	

	function isDeleted($model, $UID) {
		global $db;

		$conditions = array();
		$conditions[] = "refModel='" . $db->addMarkup($model) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($UID) . "'";

		$num = $db->countRange('revisions_deleted', $conditions);
		if ($num > 0) { return true; }
		return false;
	}

}


?>
