<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/dbe.inc.php');

//--------------------------------------------------------------------------------------------------
//*	test / development action for debugging this scraper
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//echo lessons_dbeGetPage('http://www.education.gov.za/Examinations/PastExamPapers/NSCNovember2009/Sepedi2009Papers/tabid/570/Default.aspx');
	//echo lessons_dbeGetPage('http://www.education.gov.za/Examinations/PastExamPapers/FebruaryMarch2009/2009FebMarchLanguages/tabid/646/Default.aspx');

	$url = 'http://www.education.gov.za/Examinations/PastExamPapers/FebruaryMarch2009/2009FebMarchLanguages/tabid/646/Default.aspx';

	$raw = lessons_dbeGetPage($url);

	//echo "<h1>" . lessons_dbeGetTitle($raw) . "</h1>";

	$courses = lessons_dbeExpandLanguageTable($raw);

?>
