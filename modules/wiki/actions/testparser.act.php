<?

	require_once($kapenta->installPath . 'modules/wiki/inc/parser.class.php');
	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	test wikicode parser
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = 'Pink'; }

	$model = new Wiki_Article($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	$parser = new Wiki_Parser($model->content);

	echo "raw:<br/><textarea rows='10' cols='120'>" . $model->content . "</textarea><br/><br/>\n";

	foreach($parser->sections as $id => $section) {
		echo "section: " . $section['title'] . " (id: $id depth: " . $section['depth']. " ordinal: " . $section['ordinal'] . ")<br/>";
		echo "<textarea rows='10' cols='120'>" . $section['wikicode'] . "</textarea><br/><br/>";
	}

?>
