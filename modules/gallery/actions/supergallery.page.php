<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - supergallery</title>
	<content>
		<div class='block'>
		[[:theme::navtitlebox::width=570::label=Recent Images From Everybody:]]
		[[:live::river::mod=gallery::view=recentthumbsall::pv=page::allow=num|pagination::pagination=no::num=25:]]
		</div>
	</content>
	<nav1>
		[[:sketchpad::tip_introduction:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Create New Gallery::toggle=divNewGalleryForm::hidden=yes:]]
		[`|lt]div id=[`|sq]divNewGalleryForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:gallery::newgalleryform:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Gallery Tags::toggle=divTagCloud:]]
		[`|lt]div id=[`|sq]divTagCloud[`|sq][`|gt]
		[[:tags::modelcloud::refModule=gallery::refModel=gallery[`|us]gallery:]]
		[`|lt]/div[`|gt]
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Random Galleries:]]
		[[:gallery::randomgalleriesnav::num=10:]]</nav1>
		</div>

	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:gallery::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Gallery - ::link=/gallery/:]]
[[:theme::breadcrumb::label=Everyone[`|sq]s Pictures::link=/gallery/supergallery/:]]</breadcrumb>
</page>

*/ ?>
