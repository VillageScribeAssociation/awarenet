<?

function formatDisplayHtml($safeContent) {
	$lines = explode("\n", $safeContent);
	$htmlContent = '';
	foreach($lines as $line) {
		$pad = '';
		while (substr($line, 0, 1) == ' ') {
			$line = substr($line, 1);
			$pad .= '&nbsp;';
		}
		$htmlContent .= $pad . $line . "<br/>\n";
	}
	return $htmlContent;
}

?>
