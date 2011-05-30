<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - edit gallery</title>
	<content>[[:theme::navtitlebox::width=570::label=Edit Gallery:]]
[`|lt]h1[`|gt]Edit Gallery[`|lt]/h1[`|gt]

[[:gallery::editform::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::width=570::label=Images:]]
[[:images::uploadmultiple::refModule=gallery::refModel=gallery[`|us]gallery::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]</content>
	<nav1></nav1>
	<nav2></nav2>
	<script></script>
	<jsinit>galleryNav[`|us]init()[`|sc]
msgSubscribe([`|sq]comments-gallery-[`|pc][`|pc]imageUID[`|pc][`|pc][`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:gallery::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Gallery - ::link=/gallery/:]]
[[:theme::breadcrumb::label=Edit::link=/gallery/edit/[`|pc][`|pc]raUID[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>