<?

//--------------------------------------------------------------------------------------------------
//*	search database fro abolute URLs and replace with serverPath environment variable
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	echo ''
	 . $theme->expandBlocks("[[:theme::ifscrollheader:]]", '')
	 .  "<h1>Fix URLs</h1>\n"
	 . "<div class='chatmessageblack'>\n"
	 . "This script replaces FQDNs/hostnames with the serverPath environment variable, in database "
	 . "content so that URLs are rendered correctly relative to the local awareNet instance."
	 . "</div>\n";

	//----------------------------------------------------------------------------------------------
	//	show form if no URL POSTed
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('url', $_POST)) {
		$form = ''
		 . "<form name='setUrl' method='POST'>\n"
		 . "<b>Url:</b>&nbsp;"
		 . "<input name='url' value='http://awarenet.tld/' />&nbsp;"
		 . "<input type='submit' value='Replace All' />"
		 . "</form>\n"
		 . '';

		echo "<div class='chatmessagegreen'>" . $form . "</div>\n";
		echo $theme->expandBlocks("[[:theme::ifscrollfooter:]]", '');
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	perform database-wide search and replace
	//----------------------------------------------------------------------------------------------
	$tables = $db->loadTables();
	$url = $_POST['url'];

	foreach($tables as $table) {
		if ('p2p' != substr($table, 0, 3)) {

			echo "<div class='chatmessageblack'><h2>Searching: $table</h2></div>\n";
			$count = 0;

			$sql = "select * from $table";
			$result = $db->query($sql);
			while($row = $db->fetchAssoc($result)) {

				foreach($row as $field => $value) {
					if (false !== strpos($value, $url)) {
						echo ''
						 . "<div class='chatmessagered'>"
						 . "found in " . $table . '.' . $field . ": " . $row['UID']
						 . "</div>";

						$clean = str_replace($url, '[`|pc][`|pc]serverPath[`|pc][`|pc]', $value);
			
						$sql = ''
						 . "Update `$table` "
						 . "set $field=\"" . $clean . "\" "
						 . "where UID='" . $row['UID'] . "'";
		
						//echo htmlentities($sql) . "<br/>\n";

						$db->query($sql);
						$count++;
					}
				} 

			} // while record
		} // if not p2p
		if ($count > 0) { echo "<div class='chatmessageblack'>Fixed $count instances.</div>\n"; }
	} // foreach table

	echo "<div class='chatmessageblack'>Finished.</div>\n";
	echo $theme->expandBlocks("[[:theme::ifscrollfooter:]]", '');

?>
