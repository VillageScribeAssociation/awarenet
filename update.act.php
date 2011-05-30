<?

	$listUrl = 'http://kapenta.org.uk/code/projectlist/210833480571475984';
	echo "downloading: $listUrl <br/>"; flush();

	$raw = implode(file($listUrl));

	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		echo $line . "<br/>\n";
	}

?>
