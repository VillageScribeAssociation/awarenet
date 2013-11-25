<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>%%websiteName%% - Packages (admin)</title>
	<content>

		<div class='block'>
		[[:theme::navtitlebox::label=About::toggle=divAboutPackages:]]
		<div id='divAboutPackages'>
		<p>This page displays the status of installed packages, and lists packages which you can install in your site.</p>

		<blockquote>
		<p><b>Packages</b> are pieces of software which you can install on your site to add or change functionality.</p>
		<p><b>Sources</b> or repositories are sites from which packages can be downloaded and updated.</p>
		</blockquote>
		</div>
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Installed Packages::toggle=divInstalledPackages:]]
		<div id='divInstalledPackages'>
		<h2>Installed Packages</h2>
		[[:packages::updatesourcesform:]]
		<br/>
		<div id='divInstalledList'>
		[[:packages::installedpackages:]]
		</div>
		</div>
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Available Packages::toggle=divAvailablePackages:]]
		<div id='divAvailablePackages'>
		<h2>All Packages</h2>
		<p><b>Note:</b> Package lists should be automatically updated from the repositories every night, and can be manually refreshed using the button below.</p>
		[[:packages::updatesourcesform:]]
		<br/>
		[[:packages::allpackages::status=available:]]
		<hr/>
		</div>
		<div class='foot'></div>
		</div>
		<br/>

	</content>
	<nav1>
		<div class='block'>
		[[:theme::navtitlebox::label=Sources::toggle=divManageSources:]]
		<div id='divManageSources'>
		<div class='spacer'></div>
		[[:packages::listsources:]]
		[[:packages::addsourceform:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Add Package::toggle=divAddManual:]]
		<div id='divAddManual'>
		[[:packages::addpackageform:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>
		
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
	<breadcrumb>[[:theme::breadcrumb::label=Administration - ::link=/admin/:]]
[[:theme::breadcrumb::label=Packages::link=/packages/:]]</breadcrumb>
</page>

*/ ?>
