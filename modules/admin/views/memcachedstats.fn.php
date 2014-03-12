<?php

//--------------------------------------------------------------------------------------------------
//*	display memcached statis if enabled, formatted for nav
//--------------------------------------------------------------------------------------------------

function admin_memcachedstats($args) {
	global $kapenta;
	global $theme;
	global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and extension status
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (false == $kapenta->mcEnabled) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$stats = $kapenta->mc->getStats();

	if (false == is_array($stats)) { return ''; }

	foreach($stats as $server => $kv) {
		$table = array(array('Metric', 'Value'));

		$table[] = array("<b>$server</b>", "<span class='ajaxmsg'>available</span>");

		foreach($kv as $key => $value) {
			$table[] = array($key, $value);
		}
		
		$html .= $theme->arrayToHtmlTable($table, true, true);
	}

	$html = ''
	 . "<div class='block'>"
	 . "[[:theme::navtitlebox::label=Memcached::toggle=divMemcachedStats::hidden=yes:]]\n"
	 . "<div id='divMemcachedStats' style='display: none;'>\n"
	 . "<div class='spacer'></div>\n"
	 . $html
	 . "</div>\n"
	 . "<div class='foot'></div>"
	 . "</div>\n"
	 . "<br/>\n";

	return $html;
}

?>
