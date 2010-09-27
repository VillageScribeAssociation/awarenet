<? /*
<page>
<template>twocol-rightnav.template.php</template>
<content>[[:theme::navtitlebox::label=Image Galleries (everyone)::width=570:]]

[[:gallery::summarylist::orderBy=%%orderBy%%::pageNo=%%pageNo%%:]]</content>
<title>awareNet - all image galleries</title>
<script></script>
<nav1>
[[:theme::navtitlebox::label=Create New Gallery::toggle=divNewGalleryForm::hidden='yes':]]
[`|lt]div id='divNewGalleryForm' style='visibility: hidden; display: none;'[`|gt]
[[:gallery::newgalleryform:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=My Galleries:]]
[[:gallery::navlist::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
[[:theme::navtitlebox::label=Unsorted Images:]]
[[:gallery::randomthumbs::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]::num=30:]]</nav1>
<nav2></nav2>
<banner></banner>
<head></head>
<menu1>[[:home::menu:]]</menu1>
<menu2>[[:gallery::menu:]]</menu2>
<section></section>
<subsection></subsection>
<breadcrumb>[[:theme::breadcrumb::label=Galleries - ::link=/gallery/:]]
[[:theme::breadcrumb::label=all - ::link=/gallery/listall/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]orderLabel[`|pc][`|pc]::link=/gallery/list/orderBy_[`|pc][`|pc]orderBy[`|pc][`|pc]:]]</breadcrumb>
</page>
*/ ?>
