<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - all image galleries</title>
	<content>
		[[:theme::navtitlebox::label=Image Galleries (everyone)::width=570:]]
		[[:gallery::orderlinks:]]
		[[:live::river::mod=gallery::view=summarylist::pv=pageNo::allow=orderBy|pagination|num::orderBy=[`|pc][`|pc]orderBy[`|pc][`|pc]::pagination=no::num=3:]]
	</content>
	<nav1>
		[[:sketchpad::tip_introduction:]]

		[[:theme::navtitlebox::label=Create New Gallery::toggle=divNewGalleryForm::hidden=[`|sq]yes[`|sq]:]]
		[`|lt]div id=[`|sq]divNewGalleryForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:gallery::newgalleryform:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=My Galleries::toggle=divMyGalleries:]]
		<div id='divMyGalleries'>
		[[:gallery::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
		</div>
		<div class='foot'></div>
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=By School::toggle=divBySchool:]]
		<div id='divBySchool'>
		[[:gallery::schoolsnav:]]
		</div>
		<div class='foot'></div>
		<br/>

		[[:theme::navtitlebox::label=Gallery Tags::toggle=divTagCloud:]]
		[`|lt]div id=[`|sq]divTagCloud[`|sq][`|gt]
		[[:tags::modelcloud::refModule=gallery::refModel=gallery[`|us]gallery:]]
		[`|lt]/div[`|gt]
		<br/>

		[[:theme::navtitlebox::label=Unsorted Images:]]
		[[:gallery::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=9:]]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:gallery::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Galleries - ::link=/gallery/:]]
[[:theme::breadcrumb::label=all - ::link=/gallery/listall/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]orderLabel[`|pc][`|pc]::link=/gallery/listall/orderBy[`|us][`|pc][`|pc]orderBy[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
