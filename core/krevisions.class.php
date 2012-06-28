<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');
	require_once($kapenta->installPath . 'modules/revisions/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	interface to revisioning system
//--------------------------------------------------------------------------------------------------
//+	This component keeps track of changes to module objects and archives them after deletion from
//+	the live system.  Note that revision control is performed according to the 'nodiff' array
//+	of model's dbSchema, and that objects marked 'archive' => 'no' are not kept.
//+
//+	New deletions of objects create new Revisions_Deleted objects which record the final state of 
//+	the deleted item, witht he status set to delete.  There may thus be a set of Revisions_Deleted
//+	objects for any item in the live database.
//+
//+	If an object is restored to the live database, the status of that Revisions_Deleted object is
//+	set to 'restore'.  Thus, if any of the set of Revisions_Deleted objects for some item have
//+	status 'restore' then the object is NOT currently deleted.  Any later re-deletions should reset
//+	the 'restore' status to 'deleted', and update the Revisions_Deleted object.
//+
//+	Since dependant obejcts are usually deleted along with their owner (eg, comments and images on
//+	a deleted blog post), restoring the owner will also restore dependant objects.

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
	//.	record deletion of an object
	//----------------------------------------------------------------------------------------------
	//arg: changes - associative array of fields and values [array]
	//arg: dbSchema - a database table definition [array]		
	//opt: isShared - object was shared with other peers so share the deletion, true [bool]
	//returns: true on success, false on failure [bool]

	function recordDeletion($fields, $dbSchema, $isShared = true) {
		global $db, $session;

		//------------------------------------------------------------------------------------------
		//	check table schema
		//------------------------------------------------------------------------------------------
		if (false == $db->checkSchema($dbSchema)) { 
			$session->msgAdmin('<b>Error:</b> Bad schema - ' . $dbSchema['model'], 'bad');
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	check whether objects of this type are not archived
		//------------------------------------------------------------------------------------------
		if ((true == array_key_exists('archive', $dbSchema)) && ('no' == $dbSchema['archive'])) {
			$session->msgAdmin('Objects of this type are not archived or revisioned.');
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	check whether object has already been deleted
		//------------------------------------------------------------------------------------------
		if (true == $this->isDeleted($dbSchema['model'], $dbSchema['fields']['UID'])) {
			$session->msgAdmin('Object is already deleted.', 'bad');
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	create new Revisions_Deleted object
		//------------------------------------------------------------------------------------------
		$model = new Revisions_Deleted();
		$model->refModule = $dbSchema['module'];
		$model->refModel = $dbSchema['model'];
		$model->refUID = $fields['UID'];
		$model->fields = $fields;

		$model->shared = 'yes';
		if (false == $isShared) { $model->shared = 'no'; }

		$report = $model->save();

		if ('' != $report) { 
			$msg = 'Could not move '. $model->refModel .'::'. $model->refUID .' to recycle bin: ';
			$session->msgAdmin($msg . $report, 'bad');
			return false;
		} else {
			$msg = 'Moved ' . $model->refModel . '::' . $model->refUID . ' to the recycle bin.';
			$session->msgAdmin($msg, 'ok');
		}

		//------------------------------------------------------------------------------------------
		//	undo any previous restore of this item
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "refModel='" . $db->addMarkup($model->refModel) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($model->refUID) . "'";
		$range = $db->loadRange('revisions_deleted', '*', $conditions);

		foreach($range as $item) {
			if ('restore' == $item['status']) {
				$deletion = new Revisions_Deleted($item['UID']);
				$deletion->status = 'delete';
				$report = $deletion->save();
				if ('' != $report) { $session->msg('Could not re-delete: ' . $report, 'bad'); }
			}
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
		$retored = false;

		$conditions = array();
		$conditions[] = "refModel='" . $db->addMarkup($model) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($UID) . "'";

		$range = $db->loadRange('revisions_deleted', '*', $conditions);
		if (0 == count($range)) { return false; } // no record of being deleted

		foreach($range as $item) {
			if ('restore' == $item['status']) { return false; } // deleted and restored
		}

		return true;	// record of being deleted, but none of being undeleted
	}

	//----------------------------------------------------------------------------------------------
	//.	revert the most recent deletion of an obejct
	//----------------------------------------------------------------------------------------------
	//arg: model - type of deleted object [string]
	//arg: UID - Unique identifier of deleted object [string]
	//returns: true on success, false on failure [string]
	//TODO: consider adding option to restore this item only (and not dependants)

	function undoLastDeletion($model, $UID) {
		global $db;
		if (false == $this->isDeleted($model, $UID)) { return false; }

		$conditions = array();
		$conditions[] = "refModel='" . $db->addMarkup($model) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($UID) . "'";

		$range = $db->loadRange('revisions_deleted', '*', $conditions, 'createdOn DESC');
		foreach($range as $item) {
			$model = new Revisions_Deleted($item['UID']);
			$check = $model->restore();
			if (true == $check) {
				$this->restoreDependant($model, $UID);
			}
			return $check;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	restore items belonging to some undeleted item
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function restoreDependant($model, $UID) {
		global $db;
		global $session;
		$allOk = true;				//%	return value [bool]

		$conditions = array("owner='" . $db->addMarkup($UID) . "'");
		$range = $db->loadRange('revisions_deleted', '*', $conditions);
		$session->msgAdmin("Restoring " . count($range) . " deleted items for $model $UID");

		foreach($range as $item) {
			$model = new Revisions_Deleted($item['UID']);
			$session->msg('Loaded ' . $item['UID'] . ' refModel: ' . $model->fields['refModel']);
			if (true == $model->loaded) {
				$check = $model->restore();
				if (false == $check) {
					$msg = "Could not restore deleted item: " . $item['UID'];
					$session->msg($msg, 'bad');
					$allOk = false;
				} else {
					$session->msg('Restored ' . $model->refModel . '::' . $model->refUID, 'ok');
				}
			} else {
				$session->msg('Could not load: revisions_deleted::' . $item['UID']);
			}
		}

		return $allOk;
	}

}

?>
