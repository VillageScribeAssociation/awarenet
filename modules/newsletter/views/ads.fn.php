<?php

//--------------------------------------------------------------------------------------------------
//*	show all current ads
//--------------------------------------------------------------------------------------------------

function newsletter_ads($args) {
	global $kapenta;
	global $kapenta;

	$html = '';													//%	return value [string]

	$html .= ''
	 . "[[:theme::navtitlebox::label=Promoted:]]\n"
	 . "<div class='spacer'></div>\n";

	//----------------------------------------------------------------------------------------------
	//*	check arguments and user role (none for now)
	//----------------------------------------------------------------------------------------------
	//	not done

	//----------------------------------------------------------------------------------------------
	//*	load pinned ads by weight
	//----------------------------------------------------------------------------------------------
	$conditions = array("pinned='yes'");
	$orderBy = 'weight';
	//TODO: casts for MySQL and SQLite
	$range = $kapenta->db->loadRange('newsletter_adunit', '*', $conditions, $orderBy);

	foreach($range as $item) {
		$html .= '[[:newsletter::showadunit::adunitUID=' . $item['UID'] . ':]]';
	}

	//----------------------------------------------------------------------------------------------
	//*	load pinned ads by weight
	//----------------------------------------------------------------------------------------------
	$conditions = array("pinned='no'");
	$orderBy = 'weight';
	//TODO: casts for MySQL and SQLite
	$range = $kapenta->db->loadRange('newsletter_adunit', '*', $conditions, $orderBy);

	shuffle($range);

	foreach($range as $item) {
		$html .= '[[:newsletter::showadunit::adunitUID=' . $item['UID'] . ':]]';
	}
	
	$html .= "<br/>\n";

	return $html;
}

?>
