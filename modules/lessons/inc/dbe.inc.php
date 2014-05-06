<?php

    require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temp / development action to scrape and import exam papers from the SA Department of Education
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	scrape and parse all pages
//--------------------------------------------------------------------------------------------------

function lessons_dbeList() {
	$results = array();

	//$year = lessons_dbeGetPageTables('845', 'November 2012 Examinations');
	$year = lessons_dbeGetPageLinks('845', 'November 2012 Examinations');

	$results = $year;

	return $results;
}

//--------------------------------------------------------------------------------------------------
//|	get a page from DBE CMS and crop junk
//--------------------------------------------------------------------------------------------------

function lessons_dbeGetPage($url) {
	global $kapenta;
	global $utils;

	$hash = sha1($url);
	$fileName = 'data/lessons/scraper/dbe' . $hash . '.html';
	$raw = '';

	$label = '';
	$extract = '';

	if (false == $kapenta->fs->exists($fileName)) {
		$raw = $utils->curlGet($url);
		$raw = lessons_dbeCMSTrim($raw);
		$kapenta->fs->put($fileName, $raw);
	} else {
		$raw = $kapenta->fs->get($fileName);
	}

	return $raw;
}

//--------------------------------------------------------------------------------------------------
//|	trim extraneous sections from CMS template
//--------------------------------------------------------------------------------------------------
//arg: raw - RAW HTML from CMS [string]

function lessons_dbeCMSTrim($raw) {
	$keep = false;
	$lines = explode("\n", $raw);
	$buffer = '';

	foreach($lines as $line) {
		if (false !== strpos($line, 'valign="top" class="rightholder"')) { $keep = false; }

		if (true == $keep) { $buffer .= $line . "\n"; }

		if (
			(false == $keep) &&
			(false !== strpos($line, '<span id="dnn_dnnBREADCRUMB_lblBreadCrumb">'))
		) {
			$keep = true;
		}
	}

	$buffer = "<title>" . lessons_dbeGetTitle($raw) ."</title>\n" . $buffer;

	return $buffer;
}

//--------------------------------------------------------------------------------------------------
//|	get title from DBE CMS PAGE
//--------------------------------------------------------------------------------------------------
//arg: raw - RAW HTML from CMS [string]

function lessons_dbeGetTitle($raw) {
	$startPos = strpos($raw, '<title>') + 7;
	$endPos = strpos($raw, '</title>');
	$title = substr($raw, $startPos, $endPos - $startPos);
	return trim($title);
}

//--------------------------------------------------------------------------------------------------
//|	some pages are broken down into language tables
//--------------------------------------------------------------------------------------------------
//eg: http://www.education.gov.za/Examinations/PastExamPapers/FebruaryMarch2009/2009FebMarchLanguages/tabid/646/Default.aspx

function lessons_dbeExpandLanguageTable($raw) {
	$title = lessons_dbeGetTitle($raw);
	$lang = '';
	$label = '';

	$courses = array();

	$raw = str_replace(array("\n", "\r", "\t"), array('', '', ''), $raw);
	$raw = str_replace("<tr", "\n<tr", $raw);
	$raw = str_replace("</tr>", "</tr>\n", $raw);

	$lines = explode("\n", $raw);
	$raw = '';

	foreach($lines as $line) {
		$ok = false;
		if (substr($line, 0, 4) == '<tr>') { $ok = true; }
		if (false == strpos($line, '<td><span style="font-size: x-small"><span style="font-family: Arial">')) { $ok = false; }

		if (true == $ok) {
			$line = str_replace(array('<tr>', '</tr>', '</td>'), array('', '', ''), $line);
			$cols = explode('<td>', $line);

			$lang = strip_tags($cols[1]);
			
			echo "<h2>Language: $lang</h2>\n";

			//--------------------------------------------------------------------------------------
			//	get home language papers
			//--------------------------------------------------------------------------------------

			$hlLinks = lessons_dbeGetLinks($cols[2]);

			foreach($hlLinks  as $href => $caption) {
				echo "Document: <a href='$href'>$caption [home language]</a><br/>";
			}

			if (count($hlLinks) > 0) {
				$course = new Lessons_Course();
				$course->language = lessons_getLanguageCode($lang);
				$courses[] = $course;
			}

			//--------------------------------------------------------------------------------------
			//	get first additional language papers
			//--------------------------------------------------------------------------------------

			$falLinks = lessons_dbeGetLinks($cols[3]);

			foreach($falLinks  as $href => $caption) {
				echo "Document: <a href='$href'>$caption [first additional language]</a><br/>";
			}

			//--------------------------------------------------------------------------------------
			//	get second additional language papers
			//--------------------------------------------------------------------------------------

			$salLinks = lessons_dbeGetLinks($cols[4]);

			foreach($salLinks  as $href => $caption) {
				echo "Document: <a href='$href'>$caption [second additional language]</a><br/>";
			}

			//echo "col1:<br/>\n<textarea rows='2' cols='100'>{$cols[1]}</textarea><br/>\n";
			//echo "col2:<br/>\n<textarea rows='10' cols='100'>{$cols[2]}</textarea><br/>\n";
			//echo "col3:<br/>\n<textarea rows='10' cols='100'>{$cols[3]}</textarea><br/>\n";
			//echo "col4:<br/>\n<textarea rows='10' cols='100'>{$cols[4]}</textarea><br/>\n";


		}
	}

}


//--------------------------------------------------------------------------------------------------
//|	get two-letter ISO 639-1 language code given full name of language
//--------------------------------------------------------------------------------------------------
//arg: lang - full name of a language [string]
//arg: returns - two-letter ISO code of language  [string]

function lessons_getLanguageCode($lang) {
	switch(strtolower(trim($lang))) {
		case 'english':			return 'en';
		case 'afrikaans':		return 'af';
		case 'xhosa':			return 'xh';
		case 'isixhosa':		return 'xh';
		case 'zulu':			return 'zu';
		case 'isizulu':			return 'zu';
		case 'northern sotho':	return 'st';		//	 has no ISO 639-1 code
		case 'northern soto':	return 'st';
		case 'sesoto':			return 'st';
		case 'sesotho':			return 'st';
		case 'sepedi':			return 'st';
		case 'pedi':			return 'st';
		case 'tswana':			return 'tn';
		case 'setswana':		return 'tn';
		case 'sotho':			return 'st';
		case 'sesotho':			return 'st';
		case 'xitsonga':		return 'ts';
		case 'tsonga':			return 'ts';
		case 'changana':		return 'ts';
	}
	return 'en';
}


//--------------------------------------------------------------------------------------------------
//|	extratc document links from a snippet of HTML on the DOE page
//--------------------------------------------------------------------------------------------------
//arg: html - snippet of HTML which may contain links [string]
//returns: map of url -> link text [string]

function lessons_dbeGetLinks($html) {
	global $utils;

	$links = array();
	$html = str_replace(array("\n", "\r", "\t"), array('', '', ''), trim($html));
	$html = str_replace(array('<a', '</a>'), array("\n<a", "</a>\n"), $html);
	$lines = explode("\n", $html);

	$expansions = array(
		'P1' => 'Paper 1',
		'P2' => 'Paper 2',
		'P3' => 'Paper 3',
		'P4' => 'Paper 4',
		'P5' => 'Paper 5',
		'M1' => 'Memorandum 1',
		'M2' => 'Memorandum 2',
		'M3' => 'Memorandum 3',
		'M4' => 'Memorandum 4',
		'M5' => 'Memorandum 5',
		'M6' => 'Memorandum 6',
		'EC' => 'Eastern Cape',
		'KZN' => 'Kwa-Zulu Natal',		
		'GT' => 'Gauteng',
		'WC' => 'Western Cape',
		'FS' => 'Free State',
		'LP' => 'Limpopo',
		'NC' => 'Northern Cape',
		'NW' => 'North West',
		'MP' => 'Mpumalanga'
	);

	foreach($lines as $line) {
		if ('<a ' == substr($line, 0, 3)) {

			$link = 'http://www.education.gov.za/' . $utils->strdelim($line, 'href="', '"');
			$label = strip_tags($utils->strdelim($line, '>', '</a>'));

			//echo "link: $line <br/>\n";
			//echo "href: " . $link . "<br/>";
			//echo "linktxt: " . strip_tags($utils->strdelim($line, '>', '</a>')) . "<br/>";

			foreach($expansions as $find => $replace) {
				if (strtolower(trim($label)) == strtolower(trim($find))) { $label = $replace; }
			}

			$links[$link] = $label;
		}
	}

	return $links;
}

//--------------------------------------------------------------------------------------------------
//|	parse a page of documents from the DBE site given site page ID, complex template
//--------------------------------------------------------------------------------------------------
//arg: url - remote CMS page [string]
//arg: label - name of period to which to which these documents apply [string]

function lessons_dbeGetPageTables($url) {
	global $kapenta;
	global $utils;

	$raw = lessons_dbeGetPage($url);

	$label = '';
	$extract = '';

	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		if (false !== strpos($line, '<td><span style="font-size: x-small"><span style="font-family: Arial">')) {

		}
	}

}

//--------------------------------------------------------------------------------------------------
//|	parse a page of documents from the DBE site given site page ID, simple template
//--------------------------------------------------------------------------------------------------
//arg: url - remote CMS page [string]
//arg: label - name of period to which to which these documents apply [string]

function lessons_dbeGetPageLinks($url, $label) {
	global $kapenta;
	global $utils;

	$raw = lessons_dbeGetPage($url);

	$label = '';
	$extract = '';


	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		
	}

}

?>
