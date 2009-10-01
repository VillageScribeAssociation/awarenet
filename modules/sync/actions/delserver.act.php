<?

//--------------------------------------------------------------------------------------------------
//	delete an awareNet server record given its UID
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }						// check user is admin
	if ($request['ref'] == '') { do404(); }									// check reference sent
	if (dbRecordExists('servers', $request['ref']) == false) { do404(); }	// check record exists

	dbDelete('servers', $request['ref']);									// delete the record
	$_SESSION['sMessage'] .= "Deleted server record " . $request['ref'] . " <br/>\n";
	do302('sync/listservers/');

?>
