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
-- Table structure for table `Aliases_Alias`
--

USE awarenet;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Aliases_Alias` (
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
  KEY `idxAliases_AliasUID` (`UID`(10)),
  KEY `idxAliases_AliasrefModule` (`refModule`(10)),
  KEY `idxAliases_AliasrefModel` (`refModel`(10)),
  KEY `idxAliases_AliasrefUID` (`refUID`(10)),
  KEY `idxAliases_AliascreatedOn` (`createdOn`),
  KEY `idxAliases_AliascreatedBy` (`createdBy`(10)),
  KEY `idxAliases_AliaseditedOn` (`editedOn`),
  KEY `idxAliases_AliaseditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Announcements_Announcement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Announcements_Announcement` (
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
  KEY `idxAnnouncements_AnnouncementUID` (`UID`(10)),
  KEY `idxAnnouncements_AnnouncementrefModule` (`refModule`(10)),
  KEY `idxAnnouncements_AnnouncementrefModel` (`refModel`(10)),
  KEY `idxAnnouncements_AnnouncementrefUID` (`refUID`(10)),
  KEY `idxAnnouncements_AnnouncementcreatedOn` (`createdOn`),
  KEY `idxAnnouncements_AnnouncementcreatedBy` (`createdBy`(10)),
  KEY `idxAnnouncements_AnnouncementeditedOn` (`editedOn`),
  KEY `idxAnnouncements_AnnouncementeditedBy` (`editedBy`(10)),
  KEY `idxAnnouncements_Announcementalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Announcements_AnnouncementAnnouncements_Announcement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Announcements_AnnouncementAnnouncements_Announcement` (
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
-- Table structure for table `Calendar_Entry`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Calendar_Entry` (
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
  KEY `idxCalendar_EntryUID` (`UID`(10)),
  KEY `idxCalendar_EntrycreatedOn` (`createdOn`),
  KEY `idxCalendar_EntrycreatedBy` (`createdBy`(10)),
  KEY `idxCalendar_EntryeditedOn` (`editedOn`),
  KEY `idxCalendar_EntryeditedBy` (`editedBy`(10)),
  KEY `idxCalendar_Entryalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chat_Discussion`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Chat_Discussion` (
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
-- Table structure for table `Comments_Comment`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Comments_Comment` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `comment` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxComments_CommentUID` (`UID`(10)),
  KEY `idxComments_CommentrefModule` (`refModule`(10)),
  KEY `idxComments_CommentrefModel` (`refModel`(10)),
  KEY `idxComments_CommentrefUID` (`refUID`(10)),
  KEY `idxComments_CommentcreatedOn` (`createdOn`),
  KEY `idxComments_CommentcreatedBy` (`createdBy`(10)),
  KEY `idxComments_CommenteditedOn` (`editedOn`),
  KEY `idxComments_CommenteditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Files_File`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Files_File` (
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
  KEY `idxFiles_FileUID` (`UID`(10)),
  KEY `idxFiles_FilerefModule` (`refModule`(10)),
  KEY `idxFiles_FilerefModel` (`refModel`(10)),
  KEY `idxFiles_FilerefUID` (`refUID`(10)),
  KEY `idxFiles_FilecreatedOn` (`createdOn`),
  KEY `idxFiles_FilecreatedBy` (`createdBy`(10)),
  KEY `idxFiles_FileeditedOn` (`editedOn`),
  KEY `idxFiles_FileeditedBy` (`editedBy`(10)),
  KEY `idxFiles_Filealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Files_Folder`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Files_Folder` (
  `UID` varchar(33) DEFAULT NULL,
  `parent` text,
  `title` varchar(255) DEFAULT NULL,
  `children` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxFiles_FolderUID` (`UID`(10)),
  KEY `idxFiles_Folderparent` (`parent`(10)),
  KEY `idxFiles_FoldercreatedOn` (`createdOn`),
  KEY `idxFiles_FoldercreatedBy` (`createdBy`(10)),
  KEY `idxFiles_FoldereditedOn` (`editedOn`),
  KEY `idxFiles_FoldereditedBy` (`editedBy`(10)),
  KEY `idxFiles_Folderalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Forums_Board`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Forums_Board` (
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
  KEY `idxForums_BoardUID` (`UID`(10)),
  KEY `idxForums_Boardschool` (`school`(10)),
  KEY `idxForums_BoardcreatedOn` (`createdOn`),
  KEY `idxForums_BoardcreatedBy` (`createdBy`(10)),
  KEY `idxForums_BoardeditedOn` (`editedOn`),
  KEY `idxForums_BoardeditedBy` (`editedBy`(10)),
  KEY `idxForums_Boardalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Forums_Reply`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Forums_Reply` (
  `UID` varchar(33) DEFAULT NULL,
  `forum` varchar(33) DEFAULT NULL,
  `thread` varchar(33) DEFAULT NULL,
  `content` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxForums_ReplyUID` (`UID`(10)),
  KEY `idxForums_Replyforum` (`forum`(10)),
  KEY `idxForums_Replythread` (`thread`(10)),
  KEY `idxForums_ReplycreatedOn` (`createdOn`),
  KEY `idxForums_ReplycreatedBy` (`createdBy`(10)),
  KEY `idxForums_ReplyeditedOn` (`editedOn`),
  KEY `idxForums_ReplyeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Forums_Thread`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Forums_Thread` (
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
  KEY `idxForums_ThreadUID` (`UID`(10)),
  KEY `idxForums_Threadboard` (`board`(10)),
  KEY `idxForums_Threadsticky` (`sticky`),
  KEY `idxForums_Threadupdated` (`updated`(10)),
  KEY `idxForums_ThreadcreatedOn` (`createdOn`),
  KEY `idxForums_ThreadcreatedBy` (`createdBy`(10)),
  KEY `idxForums_ThreadeditedOn` (`editedOn`),
  KEY `idxForums_ThreadeditedBy` (`editedBy`(10)),
  KEY `idxForums_Threadalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Forums_ThreadForums_Thread`
--


--
-- Table structure for table `Gallery_Gallery`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Gallery_Gallery` (
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
-- Table structure for table `Groups_Group`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Groups_Group` (
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
  KEY `idxGroups_GroupUID` (`UID`(10)),
  KEY `idxGroups_GroupcreatedOn` (`createdOn`),
  KEY `idxGroups_GroupcreatedBy` (`createdBy`(10)),
  KEY `idxGroups_GroupeditedOn` (`editedOn`),
  KEY `idxGroups_GroupeditedBy` (`editedBy`(10)),
  KEY `idxGroups_Groupalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Groups_Membership`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Groups_Membership` (
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
  KEY `idxGroups_MembershipUID` (`UID`(10)),
  KEY `idxGroups_MembershipuserUID` (`userUID`(10)),
  KEY `idxGroups_MembershipgroupUID` (`groupUID`(10)),
  KEY `idxGroups_MembershipcreatedOn` (`createdOn`),
  KEY `idxGroups_MembershipcreatedBy` (`createdBy`(10)),
  KEY `idxGroups_MembershipeditedOn` (`editedOn`),
  KEY `idxGroups_MembershipeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Home_Static`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Home_Static` (
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
  KEY `idxHome_StaticUID` (`UID`(10)),
  KEY `idxHome_StaticcreatedOn` (`createdOn`),
  KEY `idxHome_StaticcreatedBy` (`createdBy`(10)),
  KEY `idxHome_StaticeditedOn` (`editedOn`),
  KEY `idxHome_StaticeditedBy` (`editedBy`(10)),
  KEY `idxHome_Staticalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Images_Image`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Images_Image` (
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
  KEY `idxImages_ImageUID` (`UID`(10)),
  KEY `idxImages_ImagerefModule` (`refModule`(10)),
  KEY `idxImages_ImagerefModel` (`refModel`(10)),
  KEY `idxImages_ImagerefUID` (`refUID`(10)),
  KEY `idxImages_ImagecreatedOn` (`createdOn`),
  KEY `idxImages_ImagecreatedBy` (`createdBy`(10)),
  KEY `idxImages_ImageeditedOn` (`editedOn`),
  KEY `idxImages_ImageeditedBy` (`editedBy`(10)),
  KEY `idxImages_Imagealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Images_ImageImages_Image`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Images_ImageImages_Image` (
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
-- Table structure for table `Messages_Message`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Messages_Message` (
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
  KEY `idxMessages_MessageUID` (`UID`(10)),
  KEY `idxMessages_MessagecreatedOn` (`createdOn`),
  KEY `idxMessages_MessagecreatedBy` (`createdBy`(10)),
  KEY `idxMessages_MessageeditedOn` (`editedOn`),
  KEY `idxMessages_MessageeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Moblog_Post`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Moblog_Post` (
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
  KEY `idxMoblog_PostUID` (`UID`(10)),
  KEY `idxMoblog_PostcreatedOn` (`createdOn`),
  KEY `idxMoblog_PostcreatedBy` (`createdBy`(10)),
  KEY `idxMoblog_PosteditedOn` (`editedOn`),
  KEY `idxMoblog_PosteditedBy` (`editedBy`(10)),
  KEY `idxMoblog_Postalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Moblog_PostMoblog_Post`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Moblog_PostMoblog_Post` (
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
-- Table structure for table `Notifications_Notification`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Notifications_Notification` (
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
  KEY `idxNotifications_NotificationUID` (`UID`(10)),
  KEY `idxNotifications_NotificationrefModule` (`refModule`(10)),
  KEY `idxNotifications_NotificationrefModel` (`refModel`(10)),
  KEY `idxNotifications_NotificationrefUID` (`refUID`(10)),
  KEY `idxNotifications_NotificationcreatedOn` (`createdOn`),
  KEY `idxNotifications_NotificationcreatedBy` (`createdBy`(10)),
  KEY `idxNotifications_NotificationeditedOn` (`editedOn`),
  KEY `idxNotifications_NotificationeditedBy` (`editedBy`(10))
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
-- Table structure for table `Projects_Membership`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Projects_Membership` (
  `UID` varchar(33) DEFAULT NULL,
  `projectUID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `role` varchar(10) DEFAULT NULL,
  `joined` datetime DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxProjects_MembershipUID` (`UID`(10)),
  KEY `idxProjects_MembershipprojectUID` (`projectUID`(10)),
  KEY `idxProjects_MembershipuserUID` (`userUID`(10)),
  KEY `idxProjects_MembershipcreatedOn` (`createdOn`),
  KEY `idxProjects_MembershipcreatedBy` (`createdBy`(10)),
  KEY `idxProjects_MembershipeditedOn` (`editedOn`),
  KEY `idxProjects_MembershipeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Project`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Projects_Project` (
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
  KEY `idxProjects_ProjectUID` (`UID`(10)),
  KEY `idxProjects_ProjectcreatedOn` (`createdOn`),
  KEY `idxProjects_ProjectcreatedBy` (`createdBy`(10)),
  KEY `idxProjects_ProjecteditedOn` (`editedOn`),
  KEY `idxProjects_ProjecteditedBy` (`editedBy`(10)),
  KEY `idxProjects_Projectalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Revision`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Projects_Revision` (
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
  KEY `idxProjects_RevisionUID` (`UID`(10)),
  KEY `idxProjects_RevisionprojectUID` (`projectUID`(10)),
  KEY `idxProjects_RevisioncreatedOn` (`createdOn`),
  KEY `idxProjects_RevisioncreatedBy` (`createdBy`(10)),
  KEY `idxProjects_RevisioneditedOn` (`editedOn`),
  KEY `idxProjects_RevisioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Revisions_DeletedItem`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Revisions_DeletedItem` (
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
-- Table structure for table `Revisions_Migrate`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Revisions_Migrate` (
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
-- Table structure for table `Revisions_Revision`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Revisions_Revision` (
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
-- Table structure for table `Schools_School`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Schools_School` (
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
  KEY `idxSchools_SchoolUID` (`UID`(10)),
  KEY `idxSchools_SchoolcreatedOn` (`createdOn`),
  KEY `idxSchools_SchoolcreatedBy` (`createdBy`(10)),
  KEY `idxSchools_SchooleditedOn` (`editedOn`),
  KEY `idxSchools_SchooleditedBy` (`editedBy`(10)),
  KEY `idxSchools_Schoolalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Schools_SchoolSchools_School`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Schools_SchoolSchools_School` (
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
-- Table structure for table `Sync_Download`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sync_Download` (
  `UID` varchar(33) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `timestamp` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxSync_DownloadUID` (`UID`(10)),
  KEY `idxSync_DownloadcreatedOn` (`createdOn`),
  KEY `idxSync_DownloadcreatedBy` (`createdBy`(10)),
  KEY `idxSync_DownloadeditedOn` (`editedOn`),
  KEY `idxSync_DownloadeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Sync_Message`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sync_Message` (
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
-- Table structure for table `Sync_Notice`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sync_Notice` (
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
  KEY `idxSync_NoticeUID` (`UID`(10)),
  KEY `idxSync_Noticesource` (`source`(10)),
  KEY `idxSync_NoticecreatedOn` (`createdOn`),
  KEY `idxSync_NoticecreatedBy` (`createdBy`(10)),
  KEY `idxSync_NoticeeditedOn` (`editedOn`),
  KEY `idxSync_NoticeeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Sync_Server`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sync_Server` (
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
  KEY `idxSync_ServerUID` (`UID`(10)),
  KEY `idxSync_ServercreatedOn` (`createdOn`),
  KEY `idxSync_ServercreatedBy` (`createdBy`(10)),
  KEY `idxSync_ServereditedOn` (`editedOn`),
  KEY `idxSync_ServereditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users_Friendship`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users_Friendship` (
  `UID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `friendUID` varchar(33) DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxUsers_FriendshipUID` (`UID`(10)),
  KEY `idxUsers_FriendshipcreatedOn` (`createdOn`),
  KEY `idxUsers_FriendshipcreatedBy` (`createdBy`(10)),
  KEY `idxUsers_FriendshipeditedOn` (`editedOn`),
  KEY `idxUsers_FriendshipeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users_FriendshipUsers_Friendship`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users_FriendshipUsers_Friendship` (
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
-- Table structure for table `Users_Notification`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users_Notification` (
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
-- Table structure for table `Users_Role`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users_Role` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `permissions` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxUsers_RoleUID` (`UID`(10)),
  KEY `idxUsers_RolecreatedOn` (`createdOn`),
  KEY `idxUsers_RolecreatedBy` (`createdBy`(10)),
  KEY `idxUsers_RoleeditedOn` (`editedOn`),
  KEY `idxUsers_RoleeditedBy` (`editedBy`(10)),
  KEY `idxUsers_Rolealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users_RoleUsers_Role`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users_RoleUsers_Role` (
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
-- Table structure for table `Users_Session`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users_Session` (
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
-- Table structure for table `Users_User`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users_User` (
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
  KEY `idxUsers_UserUID` (`UID`(10)),
  KEY `idxUsers_Userusername` (`username`(10)),
  KEY `idxUsers_UsercreatedOn` (`createdOn`),
  KEY `idxUsers_UsercreatedBy` (`createdBy`(10)),
  KEY `idxUsers_UsereditedOn` (`editedOn`),
  KEY `idxUsers_UsereditedBy` (`editedBy`(10)),
  KEY `idxUsers_Useralias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Wiki_Article`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Wiki_Article` (
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
  KEY `idxWiki_ArticleUID` (`UID`(10)),
  KEY `idxWiki_ArticlecreatedOn` (`createdOn`),
  KEY `idxWiki_ArticlecreatedBy` (`createdBy`(10)),
  KEY `idxWiki_ArticleeditedOn` (`editedOn`),
  KEY `idxWiki_ArticleeditedBy` (`editedBy`(10)),
  KEY `idxWiki_Articlealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Wiki_ArticleWiki_Article`
--

--
-- Table structure for table `Wiki_Category`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Wiki_Category` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `parent` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` text,
  KEY `idxWiki_CategoryUID` (`UID`(10)),
  KEY `idxWiki_Categorytitle` (`title`(10)),
  KEY `idxWiki_Categoryparent` (`parent`(10)),
  KEY `idxWiki_CategorycreatedOn` (`createdOn`),
  KEY `idxWiki_CategorycreatedBy` (`createdBy`(10)),
  KEY `idxWiki_CategoryeditedOn` (`editedOn`),
  KEY `idxWiki_CategoryeditedBy` (`editedBy`(10)),
  KEY `idxWiki_Categoryalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Wiki_Revision`
--


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Wiki_Revision` (
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
  KEY `idxWiki_RevisionUID` (`UID`(10)),
  KEY `idxWiki_RevisioncreatedOn` (`createdOn`),
  KEY `idxWiki_RevisioncreatedBy` (`createdBy`(10)),
  KEY `idxWiki_RevisioneditedOn` (`editedOn`),
  KEY `idxWiki_RevisioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Wiki_Revision`
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
