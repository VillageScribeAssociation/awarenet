<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show contact details belonging to some owner object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have contact details [string]
//arg: refUID - UID of obejct which may have contact details [string]

function contact_list($args) {
	global $db, $user, $theme;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $user->authHas($refModule, $refModel, 'contacts-edit', $refUID)) { return '403'; }

	//----------------------------------------------------------------------------------------------
	//	load any contact details from database
	//----------------------------------------------------------------------------------------------

	//	$sql = "select * from Contact_Detail"
	//	 . " where refModule='" . $refModule . "' and refUID='" . $refUID . "'"
	//	 . " order by isDefault='true'";

	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

	$range = $db->loadRange('contact_detail', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == count($range)) { $html = "(no contact details yet added)\n<br/>"; }
	else {
		$table = array();
		$table[] = array('Type', 'Contact', '[x]', '[x]');

		foreach($range as $row) {

			$model = new Contact_Detail();
			$model->loadArray($row);
			$ext = $model->extArray();

			$table[] = array(
				$ext['type'],
				$ext['extValue'],
				$ext['editLink'],
				$ext['delLink']
			);
		}

		$html = $theme->arrayToHtmlTable($table, true, true);
	}

	$newUrl = '%%serverPath%%contact/new'
		 . '/refModule_' . $refModule
		 . '/refModel_' . $refModel
		 . '/refUID_' . $refUID;

	$html .= "<a href='$newUrl'>[add new contact detail]</a>";

	return $html;
}

?>
