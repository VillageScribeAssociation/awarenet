<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - all image galleries</title>
	<content>
		<div class='block'>
		[[:theme::navtitlebox::label=Image Galleries::width=570:]]
		[`|lt]h1[`|gt]In This Collection[`|lt]/h1[`|gt]
		[[:gallery::summarylistuser:]]
		</div>
	</content>
	<nav1>
		<div class='block'>
		[[:theme::navtitlebox::label=Made By:]]
		[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Create New Gallery::toggle=divNewGalleryForm::hidden=[`|sq]yes[`|sq]:]]
		[`|lt]div id=[`|sq]divNewGalleryForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:gallery::newgalleryform:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		[[:sketchpad::tip_introduction:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Galleries:]]
		[[:gallery::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Gallery Tags::toggle=divTagCloud:]]
		[`|lt]div id=[`|sq]divTagCloud[`|sq][`|gt]
		[[:tags::modelcloud::refModule=gallery::refModel=gallery[`|us]gallery:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		<br/>

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit>galleryNav[`|us]init()[`|sc]
msgSubscribe([`|sq]comments-gallery-[`|pc][`|pc]imageUID[`|pc][`|pc][`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>
		[[:theme::breadcrumb::label=People - ::link=/users/:]]
		[[:theme::breadcrumb::label=[`|pc][`|pc]userName[`|pc][`|pc] - ::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
		[[:theme::breadcrumb::label=Galleries::link=/gallery/list/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
	</breadcrumb>
</page>

*/ ?>
