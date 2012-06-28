<?

//--------------------------------------------------------------------------------------------------
//|	display schools ordered by country and region formatted for nav
//--------------------------------------------------------------------------------------------------

function schools_geographicnav($args) {
	global $db;
	global $user;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	//TODO
	//----------------------------------------------------------------------------------------------
	//	laod schools from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "hidden='no' OR hidden=''";
	//  any other conditions go here

	$range = $db->loadRange('schools_school', '*', $conditions, 'country, region, name');

	//----------------------------------------------------------------------------------------------
	//	sort the list
	//----------------------------------------------------------------------------------------------
	$lastRegion = '';
	$lastCountry = '';

	$countries = array();
	$counts = array();

	foreach($range as $row) {
		$country = $row['country'];
		$region = $row['country'] . " - " . $row['region'];
		
		if (false == array_key_exists($country, $countries)) { 
			$countries[$country] = array(); 
			$counts[$country] = 0; 
		}

		if (false == array_key_exists($region, $countries[$country])) { 
			$countries[$country][$region] = array(); 
		}

		$countries[$country][$region][] = "[[:schools::name::link=yes::UID=" . $row['UID'] . ":]]\n";
		$counts[$country] = $counts[$country] + 1;
	}

	arsort($counts);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach($counts as $country => $count) {
		$rCounts = array();

		// sort regions by number of schools, desc
		foreach($countries[$country] as $region => $items) { $rCounts[$region] = count($items); }
		arsort($rCounts);

		// print list of schools in each region
		foreach($rCounts as $region => $count) {
			$html .= "<h3>$region (" . $count . ")</h3>\n";
			foreach($countries[$country][$region] as $sch) { $html .= $sch . "<br/>\n"; }
		}
	}

	return $html;
}

?>
