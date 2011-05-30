<?

	require_once($kapenta->installPath . 'modules/docgen/inc/readcomments.inc.php');

//-------------------------------------------------------------------------------------------------
//*	fix up view documentation
//-------------------------------------------------------------------------------------------------
//role: admin - only administrators by run this action

	//---------------------------------------------------------------------------------------------
	//	only admins can do this
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	find all views on all modules
	//---------------------------------------------------------------------------------------------

	$mods = $kapenta->listModules();
	foreach ($mods as $mod) {
		echo "<h2>$mod</h2>\n";

		$views = $kapenta->listViews($mod);
		foreach($views as $view) {
			$fileName = 'modules/' . $mod . '/views/' . $view;
			$raw = $kapenta->fileGetContents($fileName, false, false);

			$done = 'yes';
			if (strpos($raw, "//|") == false) { $done = 'no'; }

			echo $view . " ($done)<br/>\n";

			if ($done == 'no') {
				$dgv = docGenAddViewC($raw);
				echo "<textarea rows=10 cols=80>" . str_replace('</textarea>', '<./textarea>', $dgv) . "</textarea><br/>\n";	

				$fH = fopen($fileName, 'w');
				if ($fH == false) { echo "could not open file<br/>\n"; }
				fwrite($fH, $dgv);
				fclose($fH);
				echo "saved";

			}

		}

	}

function docGenAddViewC($raw) {
	$raw = str_replace("\r", "", $raw);
	$lines = explode("\n", $raw);

	$out = '';
	$first = true;

	foreach($lines as $line) {
		if (substr($line, 0, 2) == '//') {
			if (substr($line, 0, 3) == '//-') {
				// ignore
			} else {
				if (true == $first) {
					$line = str_replace("//", "//|", $line);
					$first = false;
				} 

				if (substr($line, 0, 6) == '//arg:') {
					if (docGetType($line) == "<span class='ajaxerror'>unknown</span>") {
						echo "$line + [string]<br/>\n";
						$line = $line . " [string]";
					} else {
						echo "type known: " . docGetType($line) . "<br/>\n";
					}
				}

				if (substr($line, 0, 6) == '//opt:') {
					if (docGetType($line) == "<span class='ajaxerror'>unknown</span>") {
						echo "$line + [string]<br/>\n";
						$line = $line . " [string]";
					} else {
						echo "type known: " . docGetType($line) . "<br/>\n";
					}
				}

			}
		} 


		$out .= $line . "\n";

	}

	return $out;
}

?>
