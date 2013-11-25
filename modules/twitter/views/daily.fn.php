<?

//--------------------------------------------------------------------------------------------------
//*	daily report sent to twitter
//--------------------------------------------------------------------------------------------------
//opt: date - day for which report is to be shown, today if omitted [string]
//return: 140 char report of awarenet activity [string]

function twitter_daily($args) {
	global $kapenta;
	global $theme;

	$mods = $kapenta->listModules();				//	list of installed modules [array]
	$date = substr($kapenta->datetime(), 0, 10);	//	default is todays date [string]
	$txt = ''; 										//	return value [string]

	$title = '#awareNet_Daily: ';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions (TODO) check that cron is running this
	//----------------------------------------------------------------------------------------------
	
	if (true == array_key_exists('date', $args)) { $date = $args['date']; }

	//----------------------------------------------------------------------------------------------
	//	check each module for a twitterdaily view and run it if found
	//----------------------------------------------------------------------------------------------
	
	$txt = $title;
	$nl = 0;	//	number of newlines / separate tweets [int]

	foreach ($mods as $moduleName) {
		$viewFn = 'modules/' . $moduleName . '/views/twitterdaily.fn.php';
		if ($kapenta->fs->exists($viewFn)) {
			$block = "[[:$moduleName::twitterdaily::date=$date:]]";
			$part = $theme->expandBlocks($block, 'content');

			if (('' != $part) && (floor(strlen($txt) / 80) > $nl)) {
				$nl++;
				$txt .= "\n" . $title . " (continued):";
			}

			$txt .= $part;
		}
	}

	$txt = trim($txt);

	if ("#awareNet_Daily: " == $txt) { $txt = ''; }

	return $txt;
}

?>
