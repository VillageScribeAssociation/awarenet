<? /*
<page>
	<template>twocol-rightnav.template.php</template>
	<content>[[:theme::navtitlebox::width=570::label=Project:]]
[[:projects::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]</content>
	<title>awareNet - [`|pc][`|pc]projectTitle[`|pc][`|pc] (project)</title>
	<script></script>
	<nav1>[[:theme::navtitlebox::label=Members:]]
[[:projects::listmembersnav::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]

[[:projects::askjoinnav::projectUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[[:projects::requestsjoinnav::projectUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

[[:theme::navtitlebox::label=Add A Comment::toggle=divAddCommentNav::hidden=yes:]]
[`|lt]div id=[`|sq]divAddCommentNav[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
[[:comments::addcommentformnav::refModule=projects::refModel=Projects_Project::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=/projects/[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Recent Comments::toggle=divCommentsNav:]]
[`|lt]div id=[`|sq]divCommentsNav[`|sq][`|gt]
[[:comments::listnav::refModule=projects::refModel=Projects_Project::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Tags (this project)::toggle=divTagsNav:]]
[`|lt]div id=[`|sq]divTagsNav[`|sq][`|gt]
[[:tags::cloud::url=projects/tag/::refModule=projects::refModel=Projects_Project::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Their Other Projects:]]
[[:projects::listsamembersanav::UID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]


[[:theme::navtitlebox::label=Tags (all projects)::toggle=divTagsAllNav:]]
[`|lt]div id=[`|sq]divTagsNav[`|sq][`|gt]
[[:tags::modelcloud::refModule=projects::refModel=Projects_Project:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

</nav1>
	<nav2></nav2>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:projects::menu::projectUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Projects - ::link=/projects/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]projectTitle[`|pc][`|pc]::link=/projects/[`|pc][`|pc]projectRa[`|pc][`|pc]:]]</breadcrumb>
	<jsinit>msgSubscribe([`|sq]comments-projects-[`|pc][`|pc]UID[`|pc][`|pc]-nav[`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
</page>
*/ ?>
