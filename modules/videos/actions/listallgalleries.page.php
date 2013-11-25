<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - all image galleries</title>
	<content>
		[[:theme::navtitlebox::label=Video Galleries (%%originlabel%%)::width=570:]]
		[[:videos::orderlinks:]]
		[[:live::river::mod=videos::view=summarylist::pv=pageNo::allow=orderBy|pagination|num|origin::pagination=no::orderBy=%%orderBy%%::origin=%%origin%%::num=5:]]

	</content>
	<nav1>

		[[:videos::newgalleryform:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Gallery Tags::toggle=divTagCloud:]]
		[`|lt]div id=[`|sq]divTagCloud[`|sq][`|gt]
		[[:tags::modelcloud::refModule=videos::refModel=videos[`|us]gallery:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Popular Videos::toggle=divPopularVideos:]]
		<div id='divPopularVideos'>
		[[:videos::listpopularvideosnav::num=10:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Recently Added::toggle=divRecentVideos:]]
		<div id='divRecentVideos'>
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
	<breadcrumb>[[:theme::breadcrumb::label=Video Gallery - ::link=/videos/:]]
[[:theme::breadcrumb::label=Edit::link=/videos/edit/[`|pc][`|pc]raUID[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
