<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]imageTitle[`|pc][`|pc] (image)</title>
	<content>
		[[:images::showfull::raUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Add A Comment::width=570::toggle=divAddCommentForm::hidden=yes:]]
		<div id='divAddCommentForm' style='visibility: hidden; display: none;'>
		[[:comments::addcommentform::refModule=images::refModel=images_image::refUID=%%imageUID%%::return=/gallery/image/%%imageRa%%:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Comments::width=570::toggle=divComments:]]
		<div id='divComments'>
		[[:comments::list::refModule=images::refModel=images[`|us]image::refUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>
	</content>
	<nav1>
		<div class='block'>
		[[:theme::navtitlebox::label=About:]]
		<div class='spacer'></div>
		[[:images::metadata::imageUID=%%imageUID%%:]]
		[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Navigation:]]
		[[:gallery::gallerynav::galleryUID=[`|pc][`|pc]galleryUID[`|pc][`|pc]::imageUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]
		</div>
		<br/>

		[[:sketchpad::tip_introduction:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Unsorted Images::toggle=divRandomImages:]]
		<div id='divRandomImages'>
		[[:gallery::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=9:]]
		</div>
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Galleries::toggle=divUserGalleries:]]
		<div id='divUserGalleries'>
		[[:gallery::navlist::userUID=%%userUID%%:]]
		</div>
		<div class='foot'></div>
		</div>
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
