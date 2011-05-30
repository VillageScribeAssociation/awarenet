<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - all image galleries</title>
	<content>[[:theme::navtitlebox::label=Image Galleries::width=570:]]
[`|lt]h1[`|gt]In This Collection[`|lt]/h1[`|gt]
[[:gallery::summarylistuser:]]</content>
	<nav1>[[:theme::navtitlebox::label=Made By:]]
[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Create New Gallery::toggle=divNewGalleryForm::hidden=[`|sq]yes[`|sq]:]]
[`|lt]div id=[`|sq]divNewGalleryForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
[[:gallery::newgalleryform:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Galleries:]]
[[:gallery::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Unsorted Images:]]
[[:gallery::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=30:]]</nav1>
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
	<breadcrumb>[[:theme::breadcrumb::label=People - ::link=/users/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]userName[`|pc][`|pc] - ::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=Galleries::link=/gallery/list/[`|pc][`|pc]userRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>