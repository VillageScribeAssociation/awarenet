<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>home.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - welcome</title>
	<content>

		[[:theme::registerbutton:]]

		<a href='%%serverPath%%schools/list/'>
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/buttons/btn_schools.png' border='0' />
		</a>
		<br/><br/>

		<a href='%%serverPath%%projects/'>
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/buttons/btn_preview.png' border='0' />
		</a>
		<br/><br/>

		<div class='block'>
		<form name='siteLogin' method='POST' action='%%serverPath%%users/login/'>
		<input type='hidden' name='action' value='login' />

		<table noborder width='100%'>
		  <tr>
		    <td width='80px'><b>Username</b></td>
		    <td><input type='text' name='user' value='' size='20' style='width: 100%;' /></td>
		  </tr>
		  <tr>
		    <td><b>Password</b></td>
		    <td><input type='password' name='pass' size='20' value='' style='width: 100%;' /></td>
		  </tr>
		  <tr>
		    <td></td>
		    <td align='right'><input type='submit' value='Log In' /></td>
		  </tr>
		</table>
		</form>
		</div>

		<br/><br/><br/>

		[[:videos::player::width=300::height=200::raUID=awarenet-intro.flv:]]<br/>

	</content>
	<nav1></nav1>
	<nav2></nav2>
	<script>awareNetChat = false[`|sc]</script>
	<jsinit>galleryNav[`|us]init()[`|sc]
msgSubscribe([`|sq]comments-gallery-[`|pc][`|pc]imageUID[`|pc][`|pc][`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2></menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=awareNet - home:]]</breadcrumb>
</page>

*/ ?>
