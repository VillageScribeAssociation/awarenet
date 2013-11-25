<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display contact details as a table
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have contact details [string]
//arg: refUID - UID of object which may have contact details [string]
//opt: label - label to place above table, default is 'Contact' [string]

function contact_show($args) {
	global $db, $user, $theme;
	$html = '';						//%	return value [string]
	$label = 'Contact';				//%	label to place above table of contact details [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	$auth = false;

	if (true == $user->authHas($refModule, $refModel, 'contact-show', $refUID)) { $auth = true; }
	if (true == $user->authHas($refModule, $refModel, 'contact-view', $refUID)) { $auth = true; }
	if (false == $auth) { return ''; }

	if (true == array_key_exists('label', $args)) { $label = $args['label']; }

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

	if (0 == count($range)) { $html = ""; }
	else {
		$html .= "<b>" . $label . ":</b><br/>";

		$table = array();
		$table[] = array('Description', 'Contact');

		foreach($range as $row) {

			$model = new Contact_Detail($row['UID']);
			$ext = $model->extArray();

			$table[] = array(
				$ext['description'],
				$ext['extValue']
			);
		}

		$html .= $theme->arrayToHtmlTable($table, true, true);
	}

	return $html;
}

?>
