<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - %%title%%</title>
	<content>
		<div class='block'>
		[[:theme::navtitlebox::label=Video:]]
		<div class='spacer'></div>
		[[:videos::player::videoUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

		[`|lt]h2[`|gt][`|pc][`|pc]title[`|pc][`|pc][`|lt]/h2[`|gt]
		[`|lt]p[`|gt][`|pc][`|pc]caption[`|pc][`|pc][`|lt]/p[`|gt]
		</div>
		<br/>

		%%editBlock%%

		[[:like::show::refModule=videos::refModel=videos_video::refUID=%%UID%%:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Add A Comment::width=570::toggle=divAddCommentForm::hidden=yes:]]
		[`|lt]div id=[`|sq]divAddCommentForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:comments::addcommentform::refModule=videos::refModel=videos[`|us]video::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=/videos/play/[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Comments::width=570:]]
		[[:comments::list::refModule=videos::refModel=videos[`|us]video::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]
	</content>
	<nav1>
		[[:videos::samegallerynav::videoUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Gallery Tags::toggle=divTagCloud:]]
		[`|lt]div id=[`|sq]divTagCloud[`|sq][`|gt]
		[[:tags::modelcloud::refModule=videos::refModel=videos[`|us]gallery:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Popular Videos::toggle=divPopularVideos::hidden=yes:]]
		<div id='divPopularVideos' style='visibility: hidden; display: none'>
		[[:videos::listpopularvideosnav::num=10:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Recently Added::toggle=divRecentVideos::hidden=yes:]]
		<div id='divRecentVideos' style='visibility: hidden; display: none'>
		[[:videos::listrecentvideosnav::num=10:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:videos::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
