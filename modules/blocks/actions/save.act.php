<?

//--------------------------------------------------------------------------------------------------
//	save submitted block, redirect to /pages/list
//--------------------------------------------------------------------------------------------------
// TODO: check directory traversal, etc

if ($user->data['ofGroup'] != 'admin') { do403(); } // admins only

if ((array_key_exists('action', $_POST) AND ($_POST['action'] == 'saveBlock'))) {

	$fileName = $installPath . 'modules/' . $_POST['module'] . '/' . $_POST['block'];
	if (file_exists($fileName)) {

		saveBlock($fileName, stripslashes($_POST['blockContent']));

	} else { $_SESSION['sMessage'] .= "no such block :-(<br/>\n"; }

	do302('pages/list/');

}
// drasticdata.nl
?>
