<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]imageTitle[`|pc][`|pc] (image)</title>
	<content>
		[[:images::showfull::raUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]

		[[:theme::navtitlebox::label=Add A Comment::width=570::toggle=divAddCommentForm::hidden=yes:]]
		<div id='divAddCommentForm' style='visibility: hidden; display: none;'>
		[[:comments::addcommentform::refModule=images::refModel=images_image::refUID=%%imageUID%%::return=/gallery/image/%%imageRa%%:]]
		</div>
		<div class='foot'></div>
		<br/>

		[[:theme::navtitlebox::label=Comments::width=570::toggle=divComments:]]
		<div id='divComments'>
		[[:comments::list::refModule=images::refModel=images[`|us]image::refUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]
		</div>
		<div class='foot'></div>
		<br/>
	</content>
	<nav1>
		[[:theme::navtitlebox::label=About:]]
		[[:images::metadata::imageUID=%%imageUID%%:]]
		[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Navigation:]]
		[[:gallery::gallerynav::galleryUID=[`|pc][`|pc]galleryUID[`|pc][`|pc]::imageUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]
		<br/>

		[[:sketchpad::tip_introduction:]]

		[[:theme::navtitlebox::label=Unsorted Images::toggle=divRandomImages:]]
		<div id='divRandomImages'>
		[[:gallery::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=9:]]
		</div>
		<div class='foot'></div>
		[`|lt]br/[`|gt]
		[[:theme::navtitlebox::label=Galleries::toggle=divUserGalleries:]]
		<div id='divUserGalleries'>
		[[:gallery::navlist::userUID=%%userUID%%:]]
		</div>
		<div class='foot'></div>
		<br/>

		[[:gallery::movetogallery::imageUID=%%imageUID%%:]]

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit>galleryNav[`|us]init()[`|sc]</jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Gallery - ::link=/gallery/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]galleryTitle[`|pc][`|pc] - ::link=/gallery/[`|pc][`|pc]galleryRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]imageTitle[`|pc][`|pc]::link=/gallery/image/[`|pc][`|pc]imageRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
