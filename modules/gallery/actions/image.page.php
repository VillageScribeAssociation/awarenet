<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]imageTitle[`|pc][`|pc] (image)</title>
	<content>[[:images::showfull::raUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Add A Comment::width=570::toggle=divAddCommentForm::hidden=yes:]]
[`|lt]div id=[`|sq]divAddCommentForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
[[:comments::addcommentform::refModule=gallery::refModel=gallery[`|us]gallery::refUID=[`|pc][`|pc]imageUID[`|pc][`|pc]::return=/gallery/image/[`|pc][`|pc]imageRa[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Comments::width=570:]]
[[:comments::list::refModule=gallery::refModel=gallery[`|us]gallery::refUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]</content>
	<nav1>[[:theme::navtitlebox::label=Uploaded By:]]
[[:users::summarynav::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Navigation:]]
[[:gallery::gallerynav::galleryUID=[`|pc][`|pc]galleryUID[`|pc][`|pc]::imageUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Unsorted Images:]]
[[:gallery::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=30:]]
[`|lt]br/[`|gt]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Galleries:]]
[[:gallery::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]

[[:gallery::movetogallery::imageUID=[`|pc][`|pc]imageUID[`|pc][`|pc]:]]

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
[[:theme::breadcrumb::label=[`|pc][`|pc]galleryTitle[`|pc][`|pc] - ::link=/gallery/[`|pc][`|pc]galleryRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]imageTitle[`|pc][`|pc]::link=/gallery/image/[`|pc][`|pc]imageRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>