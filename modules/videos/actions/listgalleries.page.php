<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - all image galleries</title>
	<content>
		[[:theme::navtitlebox::label=Video Galleries::width=570:]]
		<div class='spacer'></div>
		[[:videos::summarylistuser:]]
	</content>
	<nav1>
		<div class='block'>
		[[:theme::navtitlebox::label=Made By:]]
		[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]
		</div>
		<br/>

		[[:videos::newgalleryform:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Galleries:]]
		[[:videos::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]

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
	<menu2>[[:users::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=People - ::link=/users/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]userName[`|pc][`|pc] - ::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=Video Galleries::link=/videos/listgalleries/[`|pc][`|pc]userRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
