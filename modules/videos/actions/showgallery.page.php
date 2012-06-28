<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]galleryTitle[`|pc][`|pc] (video gallery)</title>
	<content>
		[[:theme::navtitlebox::width=570::label=Video Gallery:]]
		[[:videos::showgallery::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
	</content>
	<nav1>
		[[:theme::navtitlebox::label=Create New Gallery::toggle=divNewGalleryForm::hidden=[`|sq]yes[`|sq]:]]
		[`|lt]div id=[`|sq]divNewGalleryForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:videos::newgalleryform:]]
		[`|lt]/div[`|gt]
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Made By:]]
		[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Also By This User:]]
		[[:videos::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Gallery Tags::toggle=divTagCloud:]]
		[`|lt]div id=[`|sq]divTagCloud[`|sq][`|gt]
		[[:tags::modelcloud::refModule=videos::refModel=videos[`|us]gallery:]]
		[`|lt]/div[`|gt]
		<br/>

		[[:theme::navtitlebox::label=Popular Videos::toggle=divPopularVideos::hidden=yes:]]
		<div id='divPopularVideos' style='visibility: hidden; display: none'>
		[[:videos::listpopularvideosnav::num=10:]]
		</div>
		<br/>

		[[:theme::navtitlebox::label=Recently Added::toggle=divRecentVideos::hidden=yes:]]
		<div id='divRecentVideos' style='visibility: hidden; display: none'>
		[[:videos::listrecentvideosnav::num=10:]]
		</div>
		<br/>

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Video Gallery - ::link=/videos/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]galleryTitle[`|pc][`|pc]::link=/videos/[`|pc][`|pc]galleryRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
