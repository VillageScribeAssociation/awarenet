<?

//-------------------------------------------------------------------------------------------------
//	display a project revision
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/projects/models/projectrevision.act.php');
	if (dbRecordExists('projectrevisions', $request['ref']) == false) { do404(); }

	$model = new ProjectRevision($request['ref']);

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	echo $model->data['content'];

?>
