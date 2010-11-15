<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>:: awareNet :: Videos :: test ::</title>
	<content>[[:theme::navtitlebox::label=Video:]]

[[:videos::player::videoUID=%%UID%%:]]

<h2>%%title%%</h2>
<p>%%caption%%</p>

[[:theme::navtitlebox::label=Add A Comment::width=570::toggle=divAddCommentForm::hidden=yes:]]
[`|lt]div id=[`|sq]divAddCommentForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
[[:comments::addcommentform::refModule=videos::refModel=Videos_Video::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=/videos/play/[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Comments::width=570:]]
[[:comments::list::refModule=videos::refModel=Videos_Video::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]

</content>
	<nav1>[[:videos::samegallerynav::videoUID=%%UID%%:]]
</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2></menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
