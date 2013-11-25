<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]announcementTitle[`|pc][`|pc] (announcement)</title>
	<content>
		[[:theme::navtitlebox::width=570::label=Announcement:]]
		[[:announcements::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Comments::width=570:]]
		[[:comments::list::refModule=announcements::refModel=announcements[`|us]announcement::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		[[:comments::addcommentform::refModule=announcements::refModel=announcements[`|us]announcement::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=announcements/[`|pc][`|pc]raUID[`|pc][`|pc]:]]
	</content>
	<nav1>
		[[:announcements::bulkpm::UID=%%UID%%:]]
		[[:theme::navtitlebox::label=Previously:]]
		[[:announcements::listnav::refUID=[`|pc][`|pc]refUID[`|pc][`|pc]::refModule=[`|pc][`|pc]refModule[`|pc][`|pc]:]]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit>galleryNav[`|us]init()[`|sc]
msgSubscribe([`|sq]comments-gallery-[`|pc][`|pc]imageUID[`|pc][`|pc][`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:announcements::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>
[[:theme::breadcrumb::label=Announcements - ::link=/announcements/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]announcementOwner[`|pc][`|pc] - ::link=/[`|pc][`|pc]refModule[`|pc][`|pc]/[`|pc][`|pc]refUID[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]announcementTitle[`|pc][`|pc]::link=/announcements/[`|pc][`|pc]raUID[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
