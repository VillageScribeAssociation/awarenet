<? /*
<page>
	<template>twocol-rightnav.template.php</template>
	<content>[[:theme::navtitlebox::label=Blog::width=570:]]

[[:moblog::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Add A Comment::width=570::toggle=divAddBlogComment::hidden=yes:]]
[`|lt]div id=[`|sq]divAddBlogComment[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
[[:comments::addcommentformjs::refModule=moblog::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=moblog/[`|pc][`|pc]raUID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Comments::width=570::toggle=divMoblogComments:]]
[`|lt]div id='divMoblogComments'[`|gt]
[[:comments::listjs::refModule=moblog::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

</content>
	<title>awareNet - blogs - [`|pc][`|pc]postTitle[`|pc][`|pc] ([`|pc][`|pc]userName[`|pc][`|pc])</title>
	<script></script>
	<nav1>[`|pc][`|pc]newPostForm[`|pc][`|pc]

[[:theme::navtitlebox::label=Author:]]
[[:moblog::showauthornav::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]

[[:theme::navtitlebox::label=In This Blog:]]
[[:moblog::listrecentsamenav::UID=[`|pc][`|pc]UID[`|pc][`|pc]::num=10:]]
[`|lt]br/[`|gt]
</nav1>
	<nav2></nav2>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=People - ::link=/users/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]userName[`|pc][`|pc] - ::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=Blog::link=/moblog/blog/[`|pc][`|pc]userRa[`|pc][`|pc]:]]</breadcrumb>
	<jsinit>msgSubscribe([`|sq]comments-moblog-[`|pc][`|pc]UID[`|pc][`|pc][`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
</page>
*/ ?>
