<?


$raw = "

-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: awarenet
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12.6

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aliases_alias`
--

USE awarenet;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aliases_alias` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(30) DEFAULT NULL,
  `aliaslc` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxaliases_aliasUID` (`UID`(10)),
  KEY `idxaliases_aliasrefModule` (`refModule`(10)),
  KEY `idxaliases_aliasrefModel` (`refModel`(10)),
  KEY `idxaliases_aliasrefUID` (`refUID`(10)),
  KEY `idxaliases_aliascreatedOn` (`createdOn`),
  KEY `idxaliases_aliascreatedBy` (`createdBy`(10)),
  KEY `idxaliases_aliaseditedOn` (`editedOn`),
  KEY `idxaliases_aliaseditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `announcements_announcement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements_announcement` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxannouncements_announcementUID` (`UID`(10)),
  KEY `idxannouncements_announcementrefModule` (`refModule`(10)),
  KEY `idxannouncements_announcementrefModel` (`refModel`(10)),
  KEY `idxannouncements_announcementrefUID` (`refUID`(10)),
  KEY `idxannouncements_announcementcreatedOn` (`createdOn`),
  KEY `idxannouncements_announcementcreatedBy` (`createdBy`(10)),
  KEY `idxannouncements_announcementeditedOn` (`editedOn`),
  KEY `idxannouncements_announcementeditedBy` (`editedBy`(10)),
  KEY `idxannouncements_announcementalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `announcements_announcementannouncements_announcement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements_announcementannouncements_announcement` (
  `UID` varchar(30) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` datetime DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_entry`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_entry` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `content` text,
  `year` varchar(10) DEFAULT NULL,
  `month` varchar(10) DEFAULT NULL,
  `day` varchar(10) DEFAULT NULL,
  `eventStart` varchar(50) DEFAULT NULL,
  `eventEnd` varchar(50) DEFAULT NULL,
  `published` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxcalendar_entryUID` (`UID`(10)),
  KEY `idxcalendar_entrycreatedOn` (`createdOn`),
  KEY `idxcalendar_entrycreatedBy` (`createdBy`(10)),
  KEY `idxcalendar_entryeditedOn` (`editedOn`),
  KEY `idxcalendar_entryeditedBy` (`editedBy`(10)),
  KEY `idxcalendar_entryalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_discussion`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_discussion` (
  `UID` varchar(33) DEFAULT NULL,
  `user` varchar(33) DEFAULT NULL,
  `queue` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments_comment`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments_comment` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `comment` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxcomments_commentUID` (`UID`(10)),
  KEY `idxcomments_commentrefModule` (`refModule`(10)),
  KEY `idxcomments_commentrefModel` (`refModel`(10)),
  KEY `idxcomments_commentrefUID` (`refUID`(10)),
  KEY `idxcomments_commentcreatedOn` (`createdOn`),
  KEY `idxcomments_commentcreatedBy` (`createdBy`(10)),
  KEY `idxcomments_commenteditedOn` (`editedOn`),
  KEY `idxcomments_commenteditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_file`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files_file` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `licence` varchar(100) DEFAULT NULL,
  `attribName` varchar(255) DEFAULT NULL,
  `attribUrl` varchar(255) DEFAULT NULL,
  `fileName` varchar(255) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `transforms` text,
  `caption` text,
  `weight` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxfiles_fileUID` (`UID`(10)),
  KEY `idxfiles_filerefModule` (`refModule`(10)),
  KEY `idxfiles_filerefModel` (`refModel`(10)),
  KEY `idxfiles_filerefUID` (`refUID`(10)),
  KEY `idxfiles_filecreatedOn` (`createdOn`),
  KEY `idxfiles_filecreatedBy` (`createdBy`(10)),
  KEY `idxfiles_fileeditedOn` (`editedOn`),
  KEY `idxfiles_fileeditedBy` (`editedBy`(10)),
  KEY `idxfiles_filealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_folder`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files_folder` (
  `UID` varchar(33) DEFAULT NULL,
  `parent` text,
  `title` varchar(255) DEFAULT NULL,
  `children` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxfiles_folderUID` (`UID`(10)),
  KEY `idxfiles_folderparent` (`parent`(10)),
  KEY `idxfiles_foldercreatedOn` (`createdOn`),
  KEY `idxfiles_foldercreatedBy` (`createdBy`(10)),
  KEY `idxfiles_foldereditedOn` (`editedOn`),
  KEY `idxfiles_foldereditedBy` (`editedBy`(10)),
  KEY `idxfiles_folderalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_board`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forums_board` (
  `UID` varchar(33) DEFAULT NULL,
  `school` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `weight` varchar(10) DEFAULT NULL,
  `threads` varchar(30) DEFAULT NULL,
  `replies` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxforums_boardUID` (`UID`(10)),
  KEY `idxforums_boardschool` (`school`(10)),
  KEY `idxforums_boardcreatedOn` (`createdOn`),
  KEY `idxforums_boardcreatedBy` (`createdBy`(10)),
  KEY `idxforums_boardeditedOn` (`editedOn`),
  KEY `idxforums_boardeditedBy` (`editedBy`(10)),
  KEY `idxforums_boardalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_reply`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forums_reply` (
  `UID` varchar(33) DEFAULT NULL,
  `forum` varchar(33) DEFAULT NULL,
  `thread` varchar(33) DEFAULT NULL,
  `content` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxforums_replyUID` (`UID`(10)),
  KEY `idxforums_replyforum` (`forum`(10)),
  KEY `idxforums_replythread` (`thread`(10)),
  KEY `idxforums_replycreatedOn` (`createdOn`),
  KEY `idxforums_replycreatedBy` (`createdBy`(10)),
  KEY `idxforums_replyeditedOn` (`editedOn`),
  KEY `idxforums_replyeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_thread`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forums_thread` (
  `UID` varchar(33) DEFAULT NULL,
  `board` text,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `sticky` varchar(10) DEFAULT NULL,
  `replies` varchar(10) DEFAULT NULL,
  `updated` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxforums_threadUID` (`UID`(10)),
  KEY `idxforums_threadboard` (`board`(10)),
  KEY `idxforums_threadsticky` (`sticky`),
  KEY `idxforums_threadupdated` (`updated`(10)),
  KEY `idxforums_threadcreatedOn` (`createdOn`),
  KEY `idxforums_threadcreatedBy` (`createdBy`(10)),
  KEY `idxforums_threadeditedOn` (`editedOn`),
  KEY `idxforums_threadeditedBy` (`editedBy`(10)),
  KEY `idxforums_threadalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_threadforums_thread`
--


--
-- Table structure for table `gallery_gallery`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery_gallery` (
  `UID` varchar(33) DEFAULT NULL,
  `parent` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `imagecount` bigint(20) DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_group`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_group` (
  `UID` varchar(33) DEFAULT NULL,
  `school` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `description` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxgroups_groupUID` (`UID`(10)),
  KEY `idxgroups_groupcreatedOn` (`createdOn`),
  KEY `idxgroups_groupcreatedBy` (`createdBy`(10)),
  KEY `idxgroups_groupeditedOn` (`editedOn`),
  KEY `idxgroups_groupeditedBy` (`editedBy`(10)),
  KEY `idxgroups_groupalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_membership`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_membership` (
  `UID` varchar(30) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `groupUID` varchar(33) DEFAULT NULL,
  `position` varchar(30) DEFAULT NULL,
  `admin` varchar(10) DEFAULT NULL,
  `joined` datetime DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxgroups_membershipUID` (`UID`(10)),
  KEY `idxgroups_membershipuserUID` (`userUID`(10)),
  KEY `idxgroups_membershipgroupUID` (`groupUID`(10)),
  KEY `idxgroups_membershipcreatedOn` (`createdOn`),
  KEY `idxgroups_membershipcreatedBy` (`createdBy`(10)),
  KEY `idxgroups_membershipeditedOn` (`editedOn`),
  KEY `idxgroups_membershipeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `home_static`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `home_static` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `template` varchar(30) DEFAULT NULL,
  `content` text,
  `nav1` text,
  `nav2` text,
  `script` text,
  `jsinit` text,
  `banner` varchar(255) DEFAULT NULL,
  `menu1` text,
  `menu2` text,
  `breadcrumb` text,
  `section` varchar(255) DEFAULT NULL,
  `subsection` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxhome_staticUID` (`UID`(10)),
  KEY `idxhome_staticcreatedOn` (`createdOn`),
  KEY `idxhome_staticcreatedBy` (`createdBy`(10)),
  KEY `idxhome_staticeditedOn` (`editedOn`),
  KEY `idxhome_staticeditedBy` (`editedBy`(10)),
  KEY `idxhome_staticalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images_image`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images_image` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `licence` varchar(30) DEFAULT NULL,
  `attribName` varchar(255) DEFAULT NULL,
  `attribUrl` varchar(255) DEFAULT NULL,
  `fileName` varchar(255) DEFAULT NULL,
  `format` varchar(30) DEFAULT NULL,
  `transforms` text,
  `caption` text,
  `category` varchar(100) DEFAULT NULL,
  `weight` bigint(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idximages_imageUID` (`UID`(10)),
  KEY `idximages_imagerefModule` (`refModule`(10)),
  KEY `idximages_imagerefModel` (`refModel`(10)),
  KEY `idximages_imagerefUID` (`refUID`(10)),
  KEY `idximages_imagecreatedOn` (`createdOn`),
  KEY `idximages_imagecreatedBy` (`createdBy`(10)),
  KEY `idximages_imageeditedOn` (`editedOn`),
  KEY `idximages_imageeditedBy` (`editedBy`(10)),
  KEY `idximages_imagealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images_imageimages_image`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images_imageimages_image` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `licence` varchar(30) DEFAULT NULL,
  `attribName` varchar(255) DEFAULT NULL,
  `attribUrl` varchar(255) DEFAULT NULL,
  `fileName` varchar(255) DEFAULT NULL,
  `format` varchar(30) DEFAULT NULL,
  `transforms` text,
  `caption` text,
  `category` varchar(100) DEFAULT NULL,
  `weight` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages_message`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages_message` (
  `UID` varchar(33) DEFAULT NULL,
  `owner` varchar(33) DEFAULT NULL,
  `folder` varchar(33) DEFAULT NULL,
  `fromUID` varchar(33) DEFAULT NULL,
  `toUID` varchar(33) DEFAULT NULL,
  `cc` text,
  `title` varchar(255) DEFAULT NULL,
  `re` varchar(33) DEFAULT NULL,
  `content` text,
  `status` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxmessages_messageUID` (`UID`(10)),
  KEY `idxmessages_messagecreatedOn` (`createdOn`),
  KEY `idxmessages_messagecreatedBy` (`createdBy`(10)),
  KEY `idxmessages_messageeditedOn` (`editedOn`),
  KEY `idxmessages_messageeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `moblog_post`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `moblog_post` (
  `UID` varchar(33) DEFAULT NULL,
  `school` varchar(33) DEFAULT NULL,
  `grade` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `published` varchar(3) DEFAULT NULL,
  `viewcount` bigint(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxmoblog_postUID` (`UID`(10)),
  KEY `idxmoblog_postcreatedOn` (`createdOn`),
  KEY `idxmoblog_postcreatedBy` (`createdBy`(10)),
  KEY `idxmoblog_posteditedOn` (`editedOn`),
  KEY `idxmoblog_posteditedBy` (`editedBy`(10)),
  KEY `idxmoblog_postalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `moblog_postmoblog_post`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `moblog_postmoblog_post` (
  `UID` varchar(33) DEFAULT NULL,
  `school` varchar(33) DEFAULT NULL,
  `grade` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `published` varchar(3) DEFAULT NULL,
  `viewcount` bigint(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications_notification`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications_notification` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `refUrl` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxnotifications_notificationUID` (`UID`(10)),
  KEY `idxnotifications_notificationrefModule` (`refModule`(10)),
  KEY `idxnotifications_notificationrefModel` (`refModel`(10)),
  KEY `idxnotifications_notificationrefUID` (`refUID`(10)),
  KEY `idxnotifications_notificationcreatedOn` (`createdOn`),
  KEY `idxnotifications_notificationcreatedBy` (`createdBy`(10)),
  KEY `idxnotifications_notificationeditedOn` (`editedOn`),
  KEY `idxnotifications_notificationeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Notifications_UserIndex`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Notifications_UserIndex` (
  `UID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `notificationUID` varchar(33) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxNotifications_UserIndexUID` (`UID`(10)),
  KEY `idxNotifications_UserIndexuserUID` (`userUID`(10)),
  KEY `idxNotifications_UserIndexnotificationUID` (`notificationUID`(10)),
  KEY `idxNotifications_UserIndexcreatedOn` (`createdOn`),
  KEY `idxNotifications_UserIndexcreatedBy` (`createdBy`(10)),
  KEY `idxNotifications_UserIndexeditedOn` (`editedOn`),
  KEY `idxNotifications_UserIndexeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_membership`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_membership` (
  `UID` varchar(33) DEFAULT NULL,
  `projectUID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `role` varchar(10) DEFAULT NULL,
  `joined` datetime DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxprojects_membershipUID` (`UID`(10)),
  KEY `idxprojects_membershipprojectUID` (`projectUID`(10)),
  KEY `idxprojects_membershipuserUID` (`userUID`(10)),
  KEY `idxprojects_membershipcreatedOn` (`createdOn`),
  KEY `idxprojects_membershipcreatedBy` (`createdBy`(10)),
  KEY `idxprojects_membershipeditedOn` (`editedOn`),
  KEY `idxprojects_membershipeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_project`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_project` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `abstract` text,
  `content` text,
  `status` varchar(10) DEFAULT NULL,
  `finishedOn` datetime DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxprojects_projectUID` (`UID`(10)),
  KEY `idxprojects_projectcreatedOn` (`createdOn`),
  KEY `idxprojects_projectcreatedBy` (`createdBy`(10)),
  KEY `idxprojects_projecteditedOn` (`editedOn`),
  KEY `idxprojects_projecteditedBy` (`editedBy`(10)),
  KEY `idxprojects_projectalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_revision`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_revision` (
  `UID` varchar(33) DEFAULT NULL,
  `projectUID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `abstract` text,
  `content` text,
  `reason` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxprojects_revisionUID` (`UID`(10)),
  KEY `idxprojects_revisionprojectUID` (`projectUID`(10)),
  KEY `idxprojects_revisioncreatedOn` (`createdOn`),
  KEY `idxprojects_revisioncreatedBy` (`createdBy`(10)),
  KEY `idxprojects_revisioneditedOn` (`editedOn`),
  KEY `idxprojects_revisioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `revisions_deletedItem`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revisions_deletedItem` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(20) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `timestamp` varchar(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `revisions_migrate`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revisions_migrate` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(20) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `fromUrl` varchar(255) DEFAULT NULL,
  `toUrl` varchar(255) DEFAULT NULL,
  `hitCount` bigint(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `revisions_revision`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revisions_revision` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(20) DEFAULT NULL,
  `refModel` varchar(100) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `data` mediumtext,
  `changedOn` datetime DEFAULT NULL,
  `changedBy` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schools_school`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schools_school` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `geocode` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxschools_schoolUID` (`UID`(10)),
  KEY `idxschools_schoolcreatedOn` (`createdOn`),
  KEY `idxschools_schoolcreatedBy` (`createdBy`(10)),
  KEY `idxschools_schooleditedOn` (`editedOn`),
  KEY `idxschools_schooleditedBy` (`editedBy`(10)),
  KEY `idxschools_schoolalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schools_schoolschools_school`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schools_schoolschools_school` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `geocode` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sync_download`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_download` (
  `UID` varchar(33) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `timestamp` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxsync_downloadUID` (`UID`(10)),
  KEY `idxsync_downloadcreatedOn` (`createdOn`),
  KEY `idxsync_downloadcreatedBy` (`createdBy`(10)),
  KEY `idxsync_downloadeditedOn` (`editedOn`),
  KEY `idxsync_downloadeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sync_message`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_message` (
  `UID` varchar(33) DEFAULT NULL,
  `source` varchar(33) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `data` mediumtext,
  `peer` varchar(33) DEFAULT NULL,
  `status` varchar(33) DEFAULT NULL,
  `received` varchar(33) DEFAULT NULL,
  `timestamp` varchar(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sync_notice`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_notice` (
  `UID` varchar(33) DEFAULT NULL,
  `source` varchar(33) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `ndata` text,
  `peer` varchar(33) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `received` varchar(30) DEFAULT NULL,
  `timestamp` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxsync_noticeUID` (`UID`(10)),
  KEY `idxsync_noticesource` (`source`(10)),
  KEY `idxsync_noticecreatedOn` (`createdOn`),
  KEY `idxsync_noticecreatedBy` (`createdBy`(10)),
  KEY `idxsync_noticeeditedOn` (`editedOn`),
  KEY `idxsync_noticeeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sync_server`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_server` (
  `UID` varchar(33) DEFAULT NULL,
  `servername` varchar(255) DEFAULT NULL,
  `serverurl` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `direction` varchar(30) DEFAULT NULL,
  `active` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxsync_serverUID` (`UID`(10)),
  KEY `idxsync_servercreatedOn` (`createdOn`),
  KEY `idxsync_servercreatedBy` (`createdBy`(10)),
  KEY `idxsync_servereditedOn` (`editedOn`),
  KEY `idxsync_servereditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_friendship`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_friendship` (
  `UID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `friendUID` varchar(33) DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxusers_friendshipUID` (`UID`(10)),
  KEY `idxusers_friendshipcreatedOn` (`createdOn`),
  KEY `idxusers_friendshipcreatedBy` (`createdBy`(10)),
  KEY `idxusers_friendshipeditedOn` (`editedOn`),
  KEY `idxusers_friendshipeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_friendshipusers_friendship`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_friendshipusers_friendship` (
  `UID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `friendUID` varchar(33) DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_notification`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_notification` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(20) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `user` varchar(33) DEFAULT NULL,
  `notices` mediumtext,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_role`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_role` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `permissions` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxusers_roleUID` (`UID`(10)),
  KEY `idxusers_rolecreatedOn` (`createdOn`),
  KEY `idxusers_rolecreatedBy` (`createdBy`(10)),
  KEY `idxusers_roleeditedOn` (`editedOn`),
  KEY `idxusers_roleeditedBy` (`editedBy`(10)),
  KEY `idxusers_rolealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_roleusers_role`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_roleusers_role` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `permissions` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_session`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_session` (
  `UID` varchar(33) DEFAULT NULL,
  `userUID` varchar(255) DEFAULT NULL,
  `serverurl` varchar(255) DEFAULT NULL,
  `logintime` datetime DEFAULT NULL,
  `lastseen` varchar(20) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_user`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_user` (
  `UID` varchar(33) DEFAULT NULL,
  `role` varchar(33) DEFAULT NULL,
  `school` varchar(33) DEFAULT NULL,
  `grade` varchar(30) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `lang` varchar(30) DEFAULT NULL,
  `profile` text,
  `permissions` text,
  `lastOnline` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxusers_userUID` (`UID`(10)),
  KEY `idxusers_userusername` (`username`(10)),
  KEY `idxusers_usercreatedOn` (`createdOn`),
  KEY `idxusers_usercreatedBy` (`createdBy`(10)),
  KEY `idxusers_usereditedOn` (`editedOn`),
  KEY `idxusers_usereditedBy` (`editedBy`(10)),
  KEY `idxusers_useralias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_article`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_article` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `nav` text,
  `locked` varchar(30) DEFAULT NULL,
  `namespace` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxwiki_articleUID` (`UID`(10)),
  KEY `idxwiki_articlecreatedOn` (`createdOn`),
  KEY `idxwiki_articlecreatedBy` (`createdBy`(10)),
  KEY `idxwiki_articleeditedOn` (`editedOn`),
  KEY `idxwiki_articleeditedBy` (`editedBy`(10)),
  KEY `idxwiki_articlealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_articlewiki_article`
--

--
-- Table structure for table `wiki_category`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_category` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `parent` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` text,
  KEY `idxwiki_categoryUID` (`UID`(10)),
  KEY `idxwiki_categorytitle` (`title`(10)),
  KEY `idxwiki_categoryparent` (`parent`(10)),
  KEY `idxwiki_categorycreatedOn` (`createdOn`),
  KEY `idxwiki_categorycreatedBy` (`createdBy`(10)),
  KEY `idxwiki_categoryeditedOn` (`editedOn`),
  KEY `idxwiki_categoryeditedBy` (`editedBy`(10)),
  KEY `idxwiki_categoryalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_revision`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_revision` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `nav` varchar(255) DEFAULT NULL,
  `locked` varchar(30) DEFAULT NULL,
  `namespace` varchar(255) DEFAULT NULL,
  `articleUID` varchar(33) DEFAULT NULL,
  `reason` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxwiki_revisionUID` (`UID`(10)),
  KEY `idxwiki_revisioncreatedOn` (`createdOn`),
  KEY `idxwiki_revisioncreatedBy` (`createdBy`(10)),
  KEY `idxwiki_revisioneditedOn` (`editedOn`),
  KEY `idxwiki_revisioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_revision`
--


";

	$lines = explode("\n", $raw);
	$buffer = '';

	foreach($lines as $line) {
		if ('--' ==  $line) {
			$buffer = trim($buffer);

			if ('' != $buffer) {
				echo "\$sql = \"" . $buffer . ";\"\n\n";
			}

			$buffer = '';
		}

		if ('/*' == substr($line, 0, 2)) { $line = ''; }
		if ('--' == substr($line, 0, 2)) { $line = ''; }
		$buffer .= $line . "\n";
	}

?>
