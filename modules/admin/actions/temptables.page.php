<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - temp tables (admin)</title>
	<content>
		[[:theme::navtitlebox::label=Maintenance:]]
		<h1>Temporary Database Tables</h1>
		<p>Temporary database tables (those beginning with <tt>tmp_</tt>) are created by software
		updates when database schema are changed.  They contain the previous version of the table
		(all rows) and act as a restore point in case of failed, incomplete or interrupted updates.
		Mostly, they just take up space and can be deleted once module updates have completed.</p>
		[[:admin::temptables:]]
	</content>
	<nav1>
		[[:admin::subnav:]]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:admin::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>
		[[:theme::breadcrumb::label=Administration - ::link=/admin/:]]
		[[:theme::breadcrumb::label=Temporary Database Tables::link=/admin/temptables/:]]
	</breadcrumb>
</page>

*/ ?>
