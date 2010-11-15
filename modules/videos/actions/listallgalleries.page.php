<? /*
<page>
<template>twocol-rightnav.template.php</template>
<content>[[:theme::navtitlebox::label=Video Galleries (everyone)::width=570:]]

[[:videos::summarylist::orderBy=%%orderBy%%::pageNo=%%pageNo%%:]]</content>
<title>awareNet - all image galleries</title>
<script></script>
<nav1>
[[:theme::navtitlebox::label=Create New Gallery::toggle=divNewGalleryForm::hidden='yes':]]
[`|lt]div id='divNewGalleryForm' style='visibility: hidden; display: none;'[`|gt]
[[:videos::newgalleryform:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=My Video Galleries:]]
[[:videos::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Unsorted Images:]]
[[:videos::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=30:]]</nav1>
<nav2></nav2>
<banner></banner>
<head></head>
<menu1>[[:home::menu:]]</menu1>
<menu2>[[:videos::menu:]]</menu2>
<section></section>
<subsection></subsection>
<breadcrumb>[[:theme::breadcrumb::label=Video Galleries - ::link=/videos/:]]
[[:theme::breadcrumb::label=all - ::link=/videos/listall/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]orderLabel[`|pc][`|pc]::link=/videos/listall/orderBy_[`|pc][`|pc]orderBy[`|pc][`|pc]:]]</breadcrumb>
</page>
*/ ?>
