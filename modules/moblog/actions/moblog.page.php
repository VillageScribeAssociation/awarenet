<? /*
<page>
	<template>twocol-rightnav.template.php</template>
	<content>[[:theme::navtitlebox::label=Aggregated Blogs (everyone)::width=570:]]

[[:moblog::summarylist::page=%%pageno%%::num=10:]]</content>
	<title>awareNet - blogs - everyone</title>
	<script></script>
	<nav1>
[[:theme::navtitlebox::label=Blogs By School::toggle=divSchoolStats:]]
[`|lt]div id=[`|sq]divSchoolStats[`|sq][`|gt]
[[:moblog::schoolstatsnav:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Tags::toggle=divTagCloud:]]
[`|lt]div id=[`|sq]divTagCloud[`|sq][`|gt]
[[:tags::modelcloud::refModule=moblog::refModel=Moblog_Post:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Recent Posts::toggle=divMoblogRecent::hidden=yes:]]
[`|lt]div id=[`|sq]divMoblogRecent[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
[[:moblog::listrecentnav:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Most Popular Posts::toggle=divMoblogPopular:]]
[`|lt]div id=[`|sq]divMoblogPopular[`|sq][`|gt]
[[:moblog::listpopularnav:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[`|lt]br/[`|gt]</nav1>
	<nav2></nav2>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:moblog::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Uberblog - ::link=/moblog/:]]
[[:theme::breadcrumb::label=Recent Posts::link=/moblog/:]]</breadcrumb>
	<jsinit></jsinit>
</page>
*/ ?>
