<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]galleryTitle[`|pc][`|pc] (image gallery)</title>
	<content>
		<div class='block'>
		[[:theme::navtitlebox::width=570::label=Image Gallery:]]
		[[:gallery::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]</content>
		</div>
	<nav1>
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
		[[:theme::navtitlebox::label=Unsorted Images:]]
		[[:gallery::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=30:]]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Made By:]]
		[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Galleries:]]
		[[:gallery::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit>galleryNav[`|us]init()[`|sc]
msgSubscribe([`|sq]comments-gallery-[`|pc][`|pc]imageUID[`|pc][`|pc][`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Gallery - ::link=/gallery/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]galleryTitle[`|pc][`|pc]::link=/gallery/[`|pc][`|pc]galleryRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
