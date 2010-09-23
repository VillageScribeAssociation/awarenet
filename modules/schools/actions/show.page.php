<? /*
<page>
<template>twocol-rightnav.template.php</template>
<content>[[:theme::navtitlebox::width=570::label=About:]]
[[:schools::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::width=570::label=Announcements::toggle=divAnnouncements:]]
[`|lt]div id='divAnnouncements'[`|gt]
[`|lt]br/[`|gt]
[[:announcements::list::refModule=schools::refModel=Schools_School::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::num=10:]]
[[:announcements::newlink::refModule=schools::refModel=Schools_School::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]</content>
<title>awareNet - [`|pc][`|pc]schoolName[`|pc][`|pc]</title>
<script></script>
<nav1>[[:theme::navtitlebox::label=All Schools::toggle=divAllSchools:]]
[`|lt]div id='divAllSchools'[`|gt]
[[:schools::listallnav:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Students And Teachers::toggle=divStudentsAndTeachers::hidden=yes:]]
[`|lt]div id='divStudentsAndTeachers' style='visibility: hidden; display: none;'[`|gt]
[[:schools::allgrades::schoolUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Clubs, Teams and Societies:]]
[[:groups::listallnav::school=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Recent Blog Posts:]]
[[:moblog::schoolrecentnav::schoolUID=[`|pc][`|pc]UID[`|pc][`|pc]::num=5:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Online Now:]]
[[:users::onlineschoolnav::school=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]</nav1>
<nav2></nav2>
<banner></banner>
<head></head>
<menu1>[[:home::menu:]]</menu1>
<menu2>[[:schools::menu:]]</menu2>
<section></section>
<subsection></subsection>
<breadcrumb>[[:theme::breadcrumb::label=Schools - ::link=/schools/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]schoolName[`|pc][`|pc]::link=/schools/[`|pc][`|pc]schoolRa[`|pc][`|pc]:]]</breadcrumb>
</page>
*/ ?>
