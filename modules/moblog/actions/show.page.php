<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - blogs - [`|pc][`|pc]postTitle[`|pc][`|pc] ([`|pc][`|pc]userName[`|pc][`|pc])</title>
	<content>

		<div class='block'>
		[[:theme::navtitlebox::label=Blog::width=570:]]
		[[:moblog::show::raUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		<a name='comments'></a>
		[[:theme::navtitlebox::label=Add A Comment::width=570::toggle=divAddBlogComment::hidden=yes:]]
		[`|lt]div id=[`|sq]divAddBlogComment[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:comments::addcommentform::refModule=moblog::refModel=moblog[`|us]post::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=moblog/[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Comments::width=570::toggle=divMoblogComments:]]
		<div id='divMoblogComments'>
		[[:comments::river::refModule=moblog::refModel=moblog_post::refUID=%%UID%%::num=10:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

</content>
	<nav1>
		[`|pc][`|pc]newPostForm[`|pc][`|pc]

		<div class='block'>
		[[:theme::navtitlebox::label=Author:]]
		[[:moblog::showauthornav::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=In This Blog::toggle=divRecent:]]
		<div id='divRecent'>
		[[:moblog::listrecentsamenav::UID=[`|pc][`|pc]UID[`|pc][`|pc]::num=10:]]
		</div>
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=People - ::link=/users/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]userName[`|pc][`|pc] - ::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=Blog::link=/moblog/blog/[`|pc][`|pc]userRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
