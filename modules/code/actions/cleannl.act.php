<?

//--------------------------------------------------------------------------------------------------
//	convert all text documents to linux newline format
//--------------------------------------------------------------------------------------------------

	if ($user->role != 'admin') { $page->do403(); }
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');

	$sql = "select * from code";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$c = new Code();
		$c->loadArray($row);

		if (($c->data['type'] == 'php') 
			OR ($c->data['type'] == 'page')
			OR ($c->data['type'] == 'block')
			OR ($c->data['type'] == 'txt')
			) {

			echo "cleaning: " . $c->data['title'] . " (" . $c->data['type'] . ")...<br/>\n";
			echo $c->data['content'] . "<br/><br/>\n";

			$c->data['content'] = str_replace("\r", "", $c->data['content']);
			$c->save();

		}

	}
	

?>
