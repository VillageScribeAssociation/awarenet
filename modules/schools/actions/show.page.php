<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]schoolName[`|pc][`|pc]</title>
	<content>
		<div class='block'>
		[[:theme::navtitlebox::width=570::label=About:]]
		[[:schools::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::width=570::label=Announcements::toggle=divAnnouncements:]]
		[`|lt]div id=[`|sq]divAnnouncements[`|sq][`|gt]
		<div class='spacer'></div>

		[[:announcements::list::refModule=schools::refModel=schools[`|us]school::refUID=[`|pc][`|pc]schoolUID[`|pc][`|pc]::num=10:]]
		[[:announcements::newlink::refModule=schools::refModel=schools[`|us]school::refUID=[`|pc][`|pc]schoolUID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		</div>
		[`|lt]br/[`|gt]</content>
	<nav1>
		<div class='block'>
		[[:theme::navtitlebox::label=All School Categories::toggle=divTags:]]
		[`|lt]div id=[`|sq]divTags[`|sq][`|gt]
		[[:tags::modelcloud::refModule=schools::refModel=schools[`|us]school:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=All Schools::toggle=divAllSchools::hidden=yes:]]
		[`|lt]div id=[`|sq]divAllSchools[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:schools::geographicnav:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Students And Teachers::toggle=divStudentsAndTeachers::hidden=yes:]]
		[`|lt]div id=[`|sq]divStudentsAndTeachers[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:schools::allgrades::schoolUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Clubs, Teams and Societies::toggle=divSchoolGroups:]]
		[`|lt]div id=[`|sq]divSchoolGroups[`|sq][`|gt]
		[[:groups::atschoolnav::schoolUID=[`|pc][`|pc]schoolUID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Recent Blog Posts::toggle=divRecentBlogs:]]
		[`|lt]div id=[`|sq]divRecentBlogs[`|sq][`|gt]
		[[:moblog::schoolrecentnav::schoolUID=[`|pc][`|pc]schoolUID[`|pc][`|pc]::num=5:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Online Now:]]
		[[:users::onlineschoolnav::school=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:schools::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>
		[[:theme::breadcrumb::label=Schools - ::link=/schools/:]]
		[[:theme::breadcrumb::label=[`|pc][`|pc]schoolName[`|pc][`|pc]::link=/schools/[`|pc][`|pc]schoolRa[`|pc][`|pc]:]]
	</breadcrumb>
</page>

*/ ?>
