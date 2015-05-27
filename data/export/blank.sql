-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: awarenet
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.1

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

USE `awarenet`;

--
-- Table structure for table `abuse_report`
--

DROP TABLE IF EXISTS `abuse_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abuse_report` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `comment` mediumtext,
  `notes` mediumtext,
  `status` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `fromurl` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxAbuse_ReportUID` (`UID`(10)),
  KEY `idxAbuse_ReportrefModule` (`refModule`(10)),
  KEY `idxAbuse_ReportrefModel` (`refModel`(10)),
  KEY `idxAbuse_ReportrefUID` (`refUID`(10)),
  KEY `idxAbuse_ReportcreatedOn` (`createdOn`),
  KEY `idxAbuse_ReportcreatedBy` (`createdBy`(10)),
  KEY `idxAbuse_ReporteditedOn` (`editedOn`),
  KEY `idxAbuse_ReporteditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aliases_alias`
--

DROP TABLE IF EXISTS `aliases_alias`;
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
-- Table structure for table `announcements_announcement`
--

DROP TABLE IF EXISTS `announcements_announcement`;
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
-- Table structure for table `badges_badge`
--

DROP TABLE IF EXISTS `badges_badge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badges_badge` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxbadges_badgeUID` (`UID`(10)),
  KEY `idxbadges_badgename` (`name`(10)),
  KEY `idxbadges_badgecreatedOn` (`createdOn`),
  KEY `idxbadges_badgecreatedBy` (`createdBy`(10)),
  KEY `idxbadges_badgeeditedOn` (`editedOn`),
  KEY `idxbadges_badgeeditedBy` (`editedBy`(10)),
  KEY `idxbadges_badgealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badges_userindex`
--

DROP TABLE IF EXISTS `badges_userindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badges_userindex` (
  `UID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `badgeUID` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxBadges_UserIndexUID` (`UID`(10)),
  KEY `idxBadges_UserIndexuserUID` (`userUID`(10)),
  KEY `idxBadges_UserIndexbadgeUID` (`badgeUID`(10)),
  KEY `idxBadges_UserIndexcreatedOn` (`createdOn`),
  KEY `idxBadges_UserIndexcreatedBy` (`createdBy`(10)),
  KEY `idxBadges_UserIndexeditedOn` (`editedOn`),
  KEY `idxBadges_UserIndexeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_entry`
--

DROP TABLE IF EXISTS `cache_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_entry` (
  `UID` varchar(30) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `area` varchar(10) DEFAULT NULL,
  `content` mediumtext,
  `channel` varchar(100) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxcache_entryUID` (`UID`(10)),
  KEY `idxcache_entrytag` (`tag`(30)),
  KEY `idxcache_entrychannel` (`channel`(10)),
  KEY `idxcache_entrycreatedOn` (`createdOn`),
  KEY `idxcache_entrycreatedBy` (`createdBy`(10)),
  KEY `idxcache_entryeditedOn` (`editedOn`),
  KEY `idxcache_entryeditedBy` (`editedBy`(10)),
  KEY `idxcache_entryshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_entry`
--

DROP TABLE IF EXISTS `calendar_entry`;
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
  KEY `idxCalendar_EntryUID` (`UID`(10)),
  KEY `idxCalendar_EntrycreatedOn` (`createdOn`),
  KEY `idxCalendar_EntrycreatedBy` (`createdBy`(10)),
  KEY `idxCalendar_EntryeditedOn` (`editedOn`),
  KEY `idxCalendar_EntryeditedBy` (`editedBy`(10)),
  KEY `idxCalendar_Entryalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_template`
--

DROP TABLE IF EXISTS `calendar_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_template` (
  `UID` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `content` text,
  `year` varchar(10) DEFAULT NULL,
  `month` varchar(10) DEFAULT NULL,
  `day` varchar(10) DEFAULT NULL,
  `eventStart` varchar(50) DEFAULT NULL,
  `eventEnd` varchar(50) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxcalendar_templateUID` (`UID`(10)),
  KEY `idxcalendar_templatecategory` (`category`(10)),
  KEY `idxcalendar_templatecreatedOn` (`createdOn`),
  KEY `idxcalendar_templatecreatedBy` (`createdBy`(10)),
  KEY `idxcalendar_templateeditedOn` (`editedOn`),
  KEY `idxcalendar_templateeditedBy` (`editedBy`(10)),
  KEY `idxcalendar_templatealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_discussion`
--

DROP TABLE IF EXISTS `chat_discussion`;
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
-- Table structure for table `chat_hash`
--

DROP TABLE IF EXISTS `chat_hash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_hash` (
  `UID` varchar(30) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `hash` varchar(50) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchat_hashUID` (`UID`(10)),
  KEY `idxchat_hashcreatedOn` (`createdOn`),
  KEY `idxchat_hashcreatedBy` (`createdBy`(10)),
  KEY `idxchat_hasheditedOn` (`editedOn`),
  KEY `idxchat_hasheditedBy` (`editedBy`(10)),
  KEY `idxchat_hashshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_inbox`
--

DROP TABLE IF EXISTS `chat_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_inbox` (
  `UID` varchar(30) DEFAULT NULL,
  `room` varchar(30) DEFAULT NULL,
  `fromUser` varchar(30) DEFAULT NULL,
  `toUser` varchar(30) DEFAULT NULL,
  `message` text,
  `delivered` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchat_inboxUID` (`UID`(10)),
  KEY `idxchat_inboxroom` (`room`(10)),
  KEY `idxchat_inboxtoUser` (`toUser`(10)),
  KEY `idxchat_inboxdelivered` (`delivered`(2)),
  KEY `idxchat_inboxcreatedOn` (`createdOn`),
  KEY `idxchat_inboxcreatedBy` (`createdBy`(10)),
  KEY `idxchat_inboxeditedOn` (`editedOn`),
  KEY `idxchat_inboxeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_membership`
--

DROP TABLE IF EXISTS `chat_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_membership` (
  `UID` varchar(30) DEFAULT NULL,
  `user` varchar(30) DEFAULT NULL,
  `room` varchar(30) DEFAULT NULL,
  `role` varchar(10) DEFAULT NULL,
  `state` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchat_membershipUID` (`UID`(10)),
  KEY `idxchat_membershipuser` (`user`(10)),
  KEY `idxchat_membershiproom` (`room`(10)),
  KEY `idxchat_membershipstate` (`state`(3)),
  KEY `idxchat_membershipcreatedOn` (`createdOn`),
  KEY `idxchat_membershipcreatedBy` (`createdBy`(10)),
  KEY `idxchat_membershipeditedOn` (`editedOn`),
  KEY `idxchat_membershipeditedBy` (`editedBy`(10)),
  KEY `idxchat_membershipshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_messageout`
--

DROP TABLE IF EXISTS `chat_messageout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_messageout` (
  `UID` varchar(30) DEFAULT NULL,
  `fromUser` varchar(30) DEFAULT NULL,
  `toRoom` varchar(30) DEFAULT NULL,
  `toUser` varchar(30) DEFAULT NULL,
  `message` text,
  `sent` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchat_messageoutUID` (`UID`(10)),
  KEY `idxchat_messageoutfromUser` (`fromUser`(10)),
  KEY `idxchat_messageouttoRoom` (`toRoom`(10)),
  KEY `idxchat_messageouttoUser` (`toUser`(10)),
  KEY `idxchat_messageoutsent` (`sent`(2)),
  KEY `idxchat_messageoutcreatedOn` (`createdOn`),
  KEY `idxchat_messageoutcreatedBy` (`createdBy`(10)),
  KEY `idxchat_messageouteditedOn` (`editedOn`),
  KEY `idxchat_messageouteditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_peer`
--

DROP TABLE IF EXISTS `chat_peer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_peer` (
  `UID` varchar(30) DEFAULT NULL,
  `peerUID` varchar(30) DEFAULT NULL,
  `peerUrl` varchar(255) DEFAULT NULL,
  `peerName` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchat_peerUID` (`UID`(10)),
  KEY `idxchat_peerpeerUID` (`peerUID`(10)),
  KEY `idxchat_peercreatedOn` (`createdOn`),
  KEY `idxchat_peercreatedBy` (`createdBy`(10)),
  KEY `idxchat_peereditedOn` (`editedOn`),
  KEY `idxchat_peereditedBy` (`editedBy`(10)),
  KEY `idxchat_peershared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_room`
--

DROP TABLE IF EXISTS `chat_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_room` (
  `UID` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `memberCount` bigint(20) DEFAULT NULL,
  `state` varchar(10) DEFAULT NULL,
  `emptyOn` datetime DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchat_roomUID` (`UID`(10)),
  KEY `idxchat_roomcreatedOn` (`createdOn`),
  KEY `idxchat_roomcreatedBy` (`createdBy`(10)),
  KEY `idxchat_roomeditedOn` (`editedOn`),
  KEY `idxchat_roomeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_session`
--

DROP TABLE IF EXISTS `chat_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_session` (
  `UID` varchar(30) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `serverUID` varchar(30) DEFAULT NULL,
  `userUID` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchat_sessionUID` (`UID`(10)),
  KEY `idxchat_sessionstatus` (`status`(3)),
  KEY `idxchat_sessionserverUID` (`serverUID`(10)),
  KEY `idxchat_sessionuserUID` (`userUID`(10)),
  KEY `idxchat_sessioncreatedOn` (`createdOn`),
  KEY `idxchat_sessioncreatedBy` (`createdBy`(10)),
  KEY `idxchat_sessioneditedOn` (`editedOn`),
  KEY `idxchat_sessioneditedBy` (`editedBy`(10)),
  KEY `idxchat_sessionshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatserver_hash`
--

DROP TABLE IF EXISTS `chatserver_hash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatserver_hash` (
  `UID` varchar(30) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `hash` varchar(50) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchatserver_hashUID` (`UID`(10)),
  KEY `idxchatserver_hashlabel` (`label`(10)),
  KEY `idxchatserver_hashcreatedOn` (`createdOn`),
  KEY `idxchatserver_hashcreatedBy` (`createdBy`(10)),
  KEY `idxchatserver_hasheditedOn` (`editedOn`),
  KEY `idxchatserver_hasheditedBy` (`editedBy`(10)),
  KEY `idxchatserver_hashshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatserver_history`
--

DROP TABLE IF EXISTS `chatserver_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatserver_history` (
  `UID` varchar(30) DEFAULT NULL,
  `fromUser` varchar(30) DEFAULT NULL,
  `toUser` varchar(30) DEFAULT NULL,
  `message` mediumtext,
  `event` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchatserver_historyUID` (`UID`(10)),
  KEY `idxchatserver_historyfromUser` (`fromUser`(10)),
  KEY `idxchatserver_historytoUser` (`toUser`(10)),
  KEY `idxchatserver_historycreatedOn` (`createdOn`),
  KEY `idxchatserver_historycreatedBy` (`createdBy`(10)),
  KEY `idxchatserver_historyeditedOn` (`editedOn`),
  KEY `idxchatserver_historyeditedBy` (`editedBy`(10)),
  KEY `idxchatserver_historyshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatserver_membership`
--

DROP TABLE IF EXISTS `chatserver_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatserver_membership` (
  `UID` varchar(30) DEFAULT NULL,
  `user` varchar(30) DEFAULT NULL,
  `room` varchar(30) DEFAULT NULL,
  `role` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchatserver_membershipUID` (`UID`(10)),
  KEY `idxchatserver_membershipuser` (`user`(10)),
  KEY `idxchatserver_membershiproom` (`room`(10)),
  KEY `idxchatserver_membershipcreatedOn` (`createdOn`),
  KEY `idxchatserver_membershipcreatedBy` (`createdBy`(10)),
  KEY `idxchatserver_membershipeditedOn` (`editedOn`),
  KEY `idxchatserver_membershipeditedBy` (`editedBy`(10)),
  KEY `idxchatserver_membershipshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatserver_outbox`
--

DROP TABLE IF EXISTS `chatserver_outbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatserver_outbox` (
  `UID` varchar(30) DEFAULT NULL,
  `room` varchar(30) DEFAULT NULL,
  `fromUser` varchar(30) DEFAULT NULL,
  `toUser` varchar(30) DEFAULT NULL,
  `message` mediumtext,
  `delivered` varchar(10) DEFAULT NULL,
  `peer` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchatserver_outboxUID` (`UID`(10)),
  KEY `idxchatserver_outboxroom` (`room`(10)),
  KEY `idxchatserver_outboxtoUser` (`toUser`(10)),
  KEY `idxchatserver_outboxdelivered` (`delivered`(3)),
  KEY `idxchatserver_outboxpeer` (`peer`(4)),
  KEY `idxchatserver_outboxcreatedOn` (`createdOn`),
  KEY `idxchatserver_outboxcreatedBy` (`createdBy`(10)),
  KEY `idxchatserver_outboxeditedOn` (`editedOn`),
  KEY `idxchatserver_outboxeditedBy` (`editedBy`(10)),
  KEY `idxchatserver_outboxshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatserver_peer`
--

DROP TABLE IF EXISTS `chatserver_peer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatserver_peer` (
  `UID` varchar(30) DEFAULT NULL,
  `peerUID` varchar(30) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `pubkey` mediumtext,
  `shared` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxchatserver_peerUID` (`UID`(10)),
  KEY `idxchatserver_peerpeerUID` (`peerUID`(10)),
  KEY `idxchatserver_peercreatedOn` (`createdOn`),
  KEY `idxchatserver_peercreatedBy` (`createdBy`(10)),
  KEY `idxchatserver_peereditedOn` (`editedOn`),
  KEY `idxchatserver_peereditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatserver_room`
--

DROP TABLE IF EXISTS `chatserver_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatserver_room` (
  `UID` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `memberCount` text,
  `emptyOn` text,
  `status` varchar(10) DEFAULT NULL,
  `revision` bigint(15) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchatserver_roomUID` (`UID`(10)),
  KEY `idxchatserver_roomtitle` (`title`(10)),
  KEY `idxchatserver_roommemberCount` (`memberCount`(10)),
  KEY `idxchatserver_roomstatus` (`status`(3)),
  KEY `idxchatserver_roomcreatedOn` (`createdOn`),
  KEY `idxchatserver_roomcreatedBy` (`createdBy`(10)),
  KEY `idxchatserver_roomeditedOn` (`editedOn`),
  KEY `idxchatserver_roomeditedBy` (`editedBy`(10)),
  KEY `idxchatserver_roomshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatserver_session`
--

DROP TABLE IF EXISTS `chatserver_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatserver_session` (
  `UID` varchar(30) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `serverUID` varchar(30) DEFAULT NULL,
  `userUID` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxchatserver_sessionUID` (`UID`(10)),
  KEY `idxchatserver_sessionstatus` (`status`(3)),
  KEY `idxchatserver_sessionserverUID` (`serverUID`(10)),
  KEY `idxchatserver_sessionuserUID` (`userUID`(10)),
  KEY `idxchatserver_sessioncreatedOn` (`createdOn`),
  KEY `idxchatserver_sessioncreatedBy` (`createdBy`(10)),
  KEY `idxchatserver_sessioneditedOn` (`editedOn`),
  KEY `idxchatserver_sessioneditedBy` (`editedBy`(10)),
  KEY `idxchatserver_sessionshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `code_bug`
--

DROP TABLE IF EXISTS `code_bug`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_bug` (
  `UID` varchar(30) DEFAULT NULL,
  `package` varchar(30) DEFAULT NULL,
  `memberType` varchar(10) DEFAULT NULL,
  `guestName` varchar(255) DEFAULT NULL,
  `guestEmail` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `status` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxcode_bugUID` (`UID`(10)),
  KEY `idxcode_bugpackage` (`package`(10)),
  KEY `idxcode_bugstatus` (`status`(5)),
  KEY `idxcode_bugcreatedOn` (`createdOn`),
  KEY `idxcode_bugcreatedBy` (`createdBy`(10)),
  KEY `idxcode_bugeditedOn` (`editedOn`),
  KEY `idxcode_bugeditedBy` (`editedBy`(10)),
  KEY `idxcode_bugalias` (`alias`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `code_change`
--

DROP TABLE IF EXISTS `code_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_change` (
  `UID` varchar(30) DEFAULT NULL,
  `package` varchar(30) DEFAULT NULL,
  `message` text,
  `files` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxcode_changeUID` (`UID`(10)),
  KEY `idxcode_changepackage` (`package`(10)),
  KEY `idxcode_changecreatedOn` (`createdOn`),
  KEY `idxcode_changecreatedBy` (`createdBy`(10)),
  KEY `idxcode_changeeditedOn` (`editedOn`),
  KEY `idxcode_changeeditedBy` (`editedBy`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `code_file`
--

DROP TABLE IF EXISTS `code_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_file` (
  `UID` varchar(30) DEFAULT NULL,
  `package` varchar(30) DEFAULT NULL,
  `parent` varchar(30) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `revision` varchar(100) DEFAULT NULL,
  `description` text,
  `content` mediumtext,
  `message` text,
  `size` varchar(20) DEFAULT NULL,
  `hash` varchar(50) DEFAULT NULL,
  `isBinary` varchar(10) DEFAULT NULL,
  `fileName` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxcode_fileUID` (`UID`(10)),
  KEY `idxcode_filepackage` (`package`(10)),
  KEY `idxcode_fileparent` (`parent`(10)),
  KEY `idxcode_filecreatedOn` (`createdOn`),
  KEY `idxcode_filecreatedBy` (`createdBy`(10)),
  KEY `idxcode_fileeditedOn` (`editedOn`),
  KEY `idxcode_fileeditedBy` (`editedBy`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `code_package`
--

DROP TABLE IF EXISTS `code_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_package` (
  `UID` varchar(30) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `version` varchar(100) DEFAULT NULL,
  `revision` varchar(100) DEFAULT NULL,
  `includes` text,
  `excludes` text,
  `installFile` varchar(255) DEFAULT NULL,
  `installFn` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` datetime DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxcode_packageUID` (`UID`(10)),
  KEY `idxcode_packagename` (`name`(10)),
  KEY `idxcode_packagecreatedOn` (`createdOn`),
  KEY `idxcode_packagecreatedBy` (`createdBy`(10)),
  KEY `idxcode_packageeditedOn` (`editedOn`),
  KEY `idxcode_packageeditedBy` (`editedBy`),
  KEY `idxcode_packagealias` (`alias`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `code_revision`
--

DROP TABLE IF EXISTS `code_revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_revision` (
  `UID` varchar(30) DEFAULT NULL,
  `fileUID` varchar(30) DEFAULT NULL,
  `package` varchar(30) DEFAULT NULL,
  `parent` varchar(30) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `revision` varchar(100) DEFAULT NULL,
  `description` text,
  `content` text,
  `message` text,
  `hash` varchar(50) DEFAULT NULL,
  `isBinary` varchar(10) DEFAULT NULL,
  `fileName` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` datetime DEFAULT NULL,
  KEY `idxcode_revisionUID` (`UID`(10)),
  KEY `idxcode_revisionpackage` (`package`(10)),
  KEY `idxcode_revisionparent` (`parent`(10)),
  KEY `idxcode_revisioncreatedOn` (`createdOn`),
  KEY `idxcode_revisioncreatedBy` (`createdBy`(10)),
  KEY `idxcode_revisioneditedOn` (`editedOn`),
  KEY `idxcode_revisioneditedBy` (`editedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `code_userindex`
--

DROP TABLE IF EXISTS `code_userindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_userindex` (
  `UID` varchar(30) DEFAULT NULL,
  `userUID` varchar(30) DEFAULT NULL,
  `packageUID` varchar(30) DEFAULT NULL,
  `privilege` varchar(100) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` datetime DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxcode_userindexUID` (`UID`(10)),
  KEY `idxcode_userindexcreatedOn` (`createdOn`),
  KEY `idxcode_userindexcreatedBy` (`createdBy`(10)),
  KEY `idxcode_userindexeditedOn` (`editedOn`),
  KEY `idxcode_userindexeditedBy` (`editedBy`),
  KEY `idxcode_userindexalias` (`alias`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments_comment`
--

DROP TABLE IF EXISTS `comments_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments_comment` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(33) DEFAULT NULL,
  `parent` varchar(33) DEFAULT NULL,
  `comment` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxcomments_commentUID` (`UID`(10)),
  KEY `idxcomments_commentrefModule` (`refModule`(10)),
  KEY `idxcomments_commentrefModel` (`refModel`(10)),
  KEY `idxcomments_commentrefUID` (`refUID`(10)),
  KEY `idxcomments_commentparent` (`parent`(10)),
  KEY `idxcomments_commentcreatedOn` (`createdOn`),
  KEY `idxcomments_commentcreatedBy` (`createdBy`(10)),
  KEY `idxcomments_commenteditedOn` (`editedOn`),
  KEY `idxcomments_commenteditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_detail`
--

DROP TABLE IF EXISTS `contact_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_detail` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `value` text,
  `isDefault` varchar(3) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxContact_DetailUID` (`UID`(10)),
  KEY `idxContact_DetailrefModule` (`refModule`(10)),
  KEY `idxContact_DetailrefModel` (`refModel`(10)),
  KEY `idxContact_DetailrefUID` (`refUID`(10)),
  KEY `idxContact_Detailtype` (`type`(10)),
  KEY `idxContact_DetailcreatedOn` (`createdOn`),
  KEY `idxContact_DetailcreatedBy` (`createdBy`(10)),
  KEY `idxContact_DetaileditedOn` (`editedOn`),
  KEY `idxContact_DetaileditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_file`
--

DROP TABLE IF EXISTS `files_file`;
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
  `fileSize` bigint(20) DEFAULT NULL,
  `hash` varchar(50) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `transforms` text,
  `caption` text,
  `weight` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxfiles_fileUID` (`UID`(10)),
  KEY `idxfiles_filerefModule` (`refModule`(10)),
  KEY `idxfiles_filerefModel` (`refModel`(10)),
  KEY `idxfiles_filerefUID` (`refUID`(10)),
  KEY `idxfiles_filecreatedOn` (`createdOn`),
  KEY `idxfiles_filecreatedBy` (`createdBy`(10)),
  KEY `idxfiles_fileeditedOn` (`editedOn`),
  KEY `idxfiles_fileeditedBy` (`editedBy`(10)),
  KEY `idxfiles_fileshared` (`shared`(1)),
  KEY `idxfiles_filealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_folder`
--

DROP TABLE IF EXISTS `files_folder`;
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
-- Table structure for table `forums_board`
--

DROP TABLE IF EXISTS `forums_board`;
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
-- Table structure for table `forums_reply`
--

DROP TABLE IF EXISTS `forums_reply`;
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
-- Table structure for table `forums_thread`
--

DROP TABLE IF EXISTS `forums_thread`;
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
  KEY `idxForums_ThreadUID` (`UID`(10)),
  KEY `idxForums_Threadboard` (`board`(10)),
  KEY `idxForums_Threadsticky` (`sticky`(2)),
  KEY `idxForums_Threadupdated` (`updated`(10)),
  KEY `idxForums_ThreadcreatedOn` (`createdOn`),
  KEY `idxForums_ThreadcreatedBy` (`createdBy`(10)),
  KEY `idxForums_ThreadeditedOn` (`editedOn`),
  KEY `idxForums_ThreadeditedBy` (`editedBy`(10)),
  KEY `idxForums_Threadalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gallery_gallery`
--

DROP TABLE IF EXISTS `gallery_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery_gallery` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `imagecount` bigint(20) DEFAULT NULL,
  `ownerName` varchar(255) DEFAULT NULL,
  `schoolUID` varchar(40) DEFAULT NULL,
  `schoolName` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxgallery_galleryUID` (`UID`(10)),
  KEY `idxgallery_gallerytitle` (`title`(10)),
  KEY `idxgallery_galleryimagecount` (`imagecount`),
  KEY `idxgallery_galleryownerName` (`ownerName`(10)),
  KEY `idxgallery_galleryschoolUID` (`schoolUID`(10)),
  KEY `idxgallery_galleryschoolName` (`schoolName`(10)),
  KEY `idxgallery_gallerycreatedOn` (`createdOn`),
  KEY `idxgallery_gallerycreatedBy` (`createdBy`(10)),
  KEY `idxgallery_galleryeditedOn` (`editedOn`),
  KEY `idxgallery_galleryeditedBy` (`editedBy`(10)),
  KEY `idxgallery_galleryalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_group`
--

DROP TABLE IF EXISTS `groups_group`;
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
  KEY `idxGroups_GroupUID` (`UID`(10)),
  KEY `idxGroups_GroupcreatedOn` (`createdOn`),
  KEY `idxGroups_GroupcreatedBy` (`createdBy`(10)),
  KEY `idxGroups_GroupeditedOn` (`editedOn`),
  KEY `idxGroups_GroupeditedBy` (`editedBy`(10)),
  KEY `idxGroups_Groupalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups_membership`
--

DROP TABLE IF EXISTS `groups_membership`;
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
-- Table structure for table `groups_schoolindex`
--

DROP TABLE IF EXISTS `groups_schoolindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_schoolindex` (
  `UID` varchar(30) DEFAULT NULL,
  `groupUID` varchar(30) DEFAULT NULL,
  `schoolUID` varchar(255) DEFAULT NULL,
  `memberCount` bigint(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxgroups_schoolindexUID` (`UID`(10)),
  KEY `idxgroups_schoolindexcreatedOn` (`createdOn`),
  KEY `idxgroups_schoolindexcreatedBy` (`createdBy`(10)),
  KEY `idxgroups_schoolindexeditedOn` (`editedOn`),
  KEY `idxgroups_schoolindexeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `home_static`
--

DROP TABLE IF EXISTS `home_static`;
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
  KEY `idxHome_StaticUID` (`UID`(10)),
  KEY `idxHome_StaticcreatedOn` (`createdOn`),
  KEY `idxHome_StaticcreatedBy` (`createdBy`(10)),
  KEY `idxHome_StaticeditedOn` (`editedOn`),
  KEY `idxHome_StaticeditedBy` (`editedBy`(10)),
  KEY `idxHome_Staticalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images_image`
--

DROP TABLE IF EXISTS `images_image`;
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
  `hash` varchar(50) DEFAULT NULL,
  `format` varchar(30) DEFAULT NULL,
  `transforms` text,
  `caption` text,
  `category` varchar(100) DEFAULT NULL,
  `weight` bigint(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  `revision` bigint(20) DEFAULT NULL,
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
-- Table structure for table `like_something`
--

DROP TABLE IF EXISTS `like_something`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `like_something` (
  `UID` varchar(30) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(30) DEFAULT NULL,
  `emotion` varchar(10) DEFAULT NULL,
  `cancelled` varchar(3) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxlike_somethingUID` (`UID`(10)),
  KEY `idxlike_somethingrefModule` (`refModule`(10)),
  KEY `idxlike_somethingrefModel` (`refModel`(10)),
  KEY `idxlike_somethingrefUID` (`refUID`(10)),
  KEY `idxlike_somethingcreatedOn` (`createdOn`),
  KEY `idxlike_somethingcreatedBy` (`createdBy`(10)),
  KEY `idxlike_somethingeditedOn` (`editedOn`),
  KEY `idxlike_somethingeditedBy` (`editedBy`(10)),
  KEY `idxlike_somethingshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_chat`
--

DROP TABLE IF EXISTS `live_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_chat` (
  `UID` varchar(33) DEFAULT NULL,
  `fromUID` varchar(33) DEFAULT NULL,
  `toUID` varchar(33) DEFAULT NULL,
  `ownerUID` varchar(33) DEFAULT NULL,
  `msg` mediumtext,
  `sent` varchar(30) DEFAULT NULL,
  `state` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxLive_ChatUID` (`UID`(10)),
  KEY `idxLive_ChatfromUID` (`fromUID`(10)),
  KEY `idxLive_ChattoUID` (`toUID`(10)),
  KEY `idxLive_ChatcreatedOn` (`createdOn`),
  KEY `idxLive_ChatcreatedBy` (`createdBy`(10)),
  KEY `idxLive_ChateditedOn` (`editedOn`),
  KEY `idxLive_ChateditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_mailbox`
--

DROP TABLE IF EXISTS `live_mailbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_mailbox` (
  `UID` varchar(33) DEFAULT NULL,
  `pageUID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `messages` mediumtext,
  `lastChecked` bigint(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxlive_mailboxUID` (`UID`(10)),
  KEY `idxlive_mailboxpageUID` (`pageUID`(10)),
  KEY `idxlive_mailboxuserUID` (`userUID`(10)),
  KEY `idxlive_mailboxlastChecked` (`lastChecked`),
  KEY `idxlive_mailboxcreatedOn` (`createdOn`),
  KEY `idxlive_mailboxcreatedBy` (`createdBy`(10)),
  KEY `idxlive_mailboxeditedOn` (`editedOn`),
  KEY `idxlive_mailboxeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_trigger`
--

DROP TABLE IF EXISTS `live_trigger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_trigger` (
  `UID` varchar(33) DEFAULT NULL,
  `pageUID` varchar(33) DEFAULT NULL,
  `module` varchar(100) DEFAULT NULL,
  `channel` varchar(50) DEFAULT NULL,
  `block` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` char(3) DEFAULT NULL,
  KEY `idxlive_triggerUID` (`UID`(10)),
  KEY `idxlive_triggerpageUID` (`pageUID`(10)),
  KEY `idxlive_triggermodule` (`module`(10)),
  KEY `idxlive_triggerchannel` (`channel`(10)),
  KEY `idxlive_triggercreatedOn` (`createdOn`),
  KEY `idxlive_triggercreatedBy` (`createdBy`(10)),
  KEY `idxlive_triggereditedOn` (`editedOn`),
  KEY `idxlive_triggereditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages_message`
--

DROP TABLE IF EXISTS `messages_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages_message` (
  `UID` varchar(33) DEFAULT NULL,
  `owner` varchar(33) DEFAULT NULL,
  `folder` varchar(33) DEFAULT NULL,
  `fromUID` varchar(33) DEFAULT NULL,
  `fromName` varchar(255) DEFAULT NULL,
  `toUID` varchar(33) DEFAULT NULL,
  `toName` varchar(255) DEFAULT NULL,
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
-- Table structure for table `moblog_post`
--

DROP TABLE IF EXISTS `moblog_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `moblog_post` (
  `UID` varchar(33) DEFAULT NULL,
  `school` varchar(33) DEFAULT NULL,
  `grade` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `published` varchar(3) DEFAULT NULL,
  `commentCount` bigint(20) DEFAULT NULL,
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

DROP TABLE IF EXISTS `moblog_postmoblog_post`;
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

DROP TABLE IF EXISTS `notifications_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications_notification` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `refEvent` varchar(255) DEFAULT NULL,
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
  KEY `idxnotifications_notificationrefEvent` (`refEvent`(10)),
  KEY `idxnotifications_notificationcreatedOn` (`createdOn`),
  KEY `idxnotifications_notificationcreatedBy` (`createdBy`(10)),
  KEY `idxnotifications_notificationeditedOn` (`editedOn`),
  KEY `idxnotifications_notificationeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications_userindex`
--

DROP TABLE IF EXISTS `notifications_userindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications_userindex` (
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
-- Table structure for table `p2p_deleted`
--

DROP TABLE IF EXISTS `p2p_deleted`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p2p_deleted` (
  `UID` varchar(30) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(30) DEFAULT NULL,
  `content` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(10) DEFAULT NULL,
  KEY `idxp2p_deletedUID` (`UID`(10)),
  KEY `idxp2p_deletedrefModule` (`refModule`(10)),
  KEY `idxp2p_deletedrefModel` (`refModel`(10)),
  KEY `idxp2p_deletedrefUID` (`refUID`(10)),
  KEY `idxp2p_deletedshared` (`shared`(3)),
  KEY `idxp2p_deletedcreatedOn` (`createdOn`),
  KEY `idxp2p_deletedcreatedBy` (`createdBy`(10)),
  KEY `idxp2p_deletededitedOn` (`editedOn`),
  KEY `idxp2p_deletededitedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p2p_gift`
--

DROP TABLE IF EXISTS `p2p_gift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p2p_gift` (
  `UID` varchar(30) DEFAULT NULL,
  `peer` varchar(30) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `refModel` varchar(100) DEFAULT NULL,
  `refUID` varchar(50) DEFAULT NULL,
  `fileName` varchar(255) DEFAULT NULL,
  `hash` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(10) DEFAULT NULL,
  KEY `idxp2p_giftUID` (`UID`(10)),
  KEY `idxp2p_giftpeer` (`peer`(10)),
  KEY `idxp2p_gifttype` (`type`(5)),
  KEY `idxp2p_giftrefModel` (`refModel`(10)),
  KEY `idxp2p_giftrefUID` (`refUID`(10)),
  KEY `idxp2p_giftstatus` (`status`(5)),
  KEY `idxp2p_giftcreatedOn` (`createdOn`),
  KEY `idxp2p_giftcreatedBy` (`createdBy`(10)),
  KEY `idxp2p_gifteditedOn` (`editedOn`),
  KEY `idxp2p_gifteditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p2p_peer`
--

DROP TABLE IF EXISTS `p2p_peer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p2p_peer` (
  `UID` varchar(30) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `firewalled` varchar(10) DEFAULT NULL,
  `pubkey` text,
  `status` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(10) DEFAULT NULL,
  KEY `idxp2p_peerUID` (`UID`(10)),
  KEY `idxp2p_peername` (`name`(10)),
  KEY `idxp2p_peercreatedOn` (`createdOn`),
  KEY `idxp2p_peercreatedBy` (`createdBy`(10)),
  KEY `idxp2p_peereditedOn` (`editedOn`),
  KEY `idxp2p_peereditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `polls_answer`
--

DROP TABLE IF EXISTS `polls_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls_answer` (
  `UID` varchar(30) DEFAULT NULL,
  `question` varchar(30) DEFAULT NULL,
  `weight` text,
  `content` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxpolls_answerUID` (`UID`(10)),
  KEY `idxpolls_answerquestion` (`question`(10)),
  KEY `idxpolls_answercreatedOn` (`createdOn`),
  KEY `idxpolls_answercreatedBy` (`createdBy`(10)),
  KEY `idxpolls_answereditedOn` (`editedOn`),
  KEY `idxpolls_answereditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `polls_question`
--

DROP TABLE IF EXISTS `polls_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls_question` (
  `UID` varchar(30) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(30) DEFAULT NULL,
  `content` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxpolls_questionUID` (`UID`(10)),
  KEY `idxpolls_questionrefModule` (`refModule`(10)),
  KEY `idxpolls_questionrefModel` (`refModel`(10)),
  KEY `idxpolls_questionrefUID` (`refUID`(10)),
  KEY `idxpolls_questioncreatedOn` (`createdOn`),
  KEY `idxpolls_questioncreatedBy` (`createdBy`(10)),
  KEY `idxpolls_questioneditedOn` (`editedOn`),
  KEY `idxpolls_questioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `polls_vote`
--

DROP TABLE IF EXISTS `polls_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls_vote` (
  `UID` varchar(30) DEFAULT NULL,
  `question` varchar(30) DEFAULT NULL,
  `answer` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxpolls_voteUID` (`UID`(10)),
  KEY `idxpolls_votequestion` (`question`(10)),
  KEY `idxpolls_voteanswer` (`answer`(10)),
  KEY `idxpolls_votecreatedOn` (`createdOn`),
  KEY `idxpolls_votecreatedBy` (`createdBy`(10)),
  KEY `idxpolls_voteeditedOn` (`editedOn`),
  KEY `idxpolls_voteeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `popular_ladder`
--

DROP TABLE IF EXISTS `popular_ladder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popular_ladder` (
  `UID` varchar(30) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `entries` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(10) DEFAULT NULL,
  KEY `idxpopular_ladderUID` (`UID`(10)),
  KEY `idxpopular_laddername` (`name`(10)),
  KEY `idxpopular_laddercreatedOn` (`createdOn`),
  KEY `idxpopular_laddercreatedBy` (`createdBy`(10)),
  KEY `idxpopular_laddereditedOn` (`editedOn`),
  KEY `idxpopular_laddereditedBy` (`editedBy`(10)),
  KEY `idxpopular_laddershared` (`shared`(3))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_change`
--

DROP TABLE IF EXISTS `projects_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_change` (
  `UID` varchar(30) DEFAULT NULL,
  `projectUID` varchar(30) DEFAULT NULL,
  `sectionUID` varchar(30) DEFAULT NULL,
  `changed` varchar(10) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `value` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxprojects_changeUID` (`UID`(10)),
  KEY `idxprojects_changeprojectUID` (`projectUID`(10)),
  KEY `idxprojects_changesectionUID` (`sectionUID`(10)),
  KEY `idxprojects_changecreatedOn` (`createdOn`),
  KEY `idxprojects_changecreatedBy` (`createdBy`(10)),
  KEY `idxprojects_changeeditedOn` (`editedOn`),
  KEY `idxprojects_changeeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_membership`
--

DROP TABLE IF EXISTS `projects_membership`;
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
-- Table structure for table `projects_project`
--

DROP TABLE IF EXISTS `projects_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_project` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `abstract` text,
  `content` text,
  `status` varchar(10) DEFAULT NULL,
  `membership` varchar(10) DEFAULT NULL,
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

DROP TABLE IF EXISTS `projects_revision`;
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
  KEY `idxProjects_RevisionUID` (`UID`(10)),
  KEY `idxProjects_RevisionprojectUID` (`projectUID`(10)),
  KEY `idxProjects_RevisioncreatedOn` (`createdOn`),
  KEY `idxProjects_RevisioncreatedBy` (`createdBy`(10)),
  KEY `idxProjects_RevisioneditedOn` (`editedOn`),
  KEY `idxProjects_RevisioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_section`
--

DROP TABLE IF EXISTS `projects_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_section` (
  `UID` varchar(30) DEFAULT NULL,
  `projectUID` varchar(30) DEFAULT NULL,
  `parentUID` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `weight` bigint(20) DEFAULT NULL,
  `lockedOn` datetime DEFAULT NULL,
  `hidden` varchar(10) DEFAULT NULL,
  `lockedBy` varchar(30) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  KEY `idxprojects_sectionUID` (`UID`(10)),
  KEY `idxprojects_sectionparentUID` (`parentUID`(10)),
  KEY `idxprojects_sectionlockedOn` (`lockedOn`),
  KEY `idxprojects_sectionlockedBy` (`lockedBy`(10)),
  KEY `idxprojects_sectioncreatedOn` (`createdOn`),
  KEY `idxprojects_sectioncreatedBy` (`createdBy`(10)),
  KEY `idxprojects_sectioneditedOn` (`editedOn`),
  KEY `idxprojects_sectioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `revisions_deleted`
--

DROP TABLE IF EXISTS `revisions_deleted`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revisions_deleted` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `owner` varchar(33) DEFAULT NULL,
  `content` text,
  `status` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxrevisions_deletedUID` (`UID`(10)),
  KEY `idxrevisions_deletedrefModule` (`refModule`(10)),
  KEY `idxrevisions_deletedrefModel` (`refModel`(10)),
  KEY `idxrevisions_deletedrefUID` (`refUID`(10)),
  KEY `idxrevisions_deletedowner` (`owner`(10)),
  KEY `idxrevisions_deletedcreatedOn` (`createdOn`),
  KEY `idxrevisions_deletedcreatedBy` (`createdBy`(10)),
  KEY `idxrevisions_deletededitedOn` (`editedOn`),
  KEY `idxrevisions_deletededitedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `revisions_deleteditem`
--

DROP TABLE IF EXISTS `revisions_deleteditem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revisions_deleteditem` (
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

DROP TABLE IF EXISTS `revisions_migrate`;
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

DROP TABLE IF EXISTS `revisions_revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revisions_revision` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `content` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxrevisions_revisionUID` (`UID`(10)),
  KEY `idxrevisions_revisionrefModule` (`refModule`(10)),
  KEY `idxrevisions_revisionrefModel` (`refModel`(10)),
  KEY `idxrevisions_revisionrefUID` (`refUID`(10)),
  KEY `idxrevisions_revisioncreatedOn` (`createdOn`),
  KEY `idxrevisions_revisioncreatedBy` (`createdBy`(10)),
  KEY `idxrevisions_revisioneditedOn` (`editedOn`),
  KEY `idxrevisions_revisioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schools_school`
--

DROP TABLE IF EXISTS `schools_school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schools_school` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `geocode` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `hidden` varchar(3) DEFAULT NULL,
  `lastBump` datetime DEFAULT NULL,
  `notifyAll` varchar(10) DEFAULT NULL,
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
-- Table structure for table `sync_server`
--

DROP TABLE IF EXISTS `sync_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_server` (
  `UID` varchar(33) DEFAULT NULL,
  `servername` varchar(255) DEFAULT NULL,
  `serverurl` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `direction` varchar(30) DEFAULT NULL,
  `active` varchar(10) DEFAULT NULL,
  `lastsync` varchar(22) DEFAULT NULL,
  `publickey` text,
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
-- Table structure for table `tags_index`
--

DROP TABLE IF EXISTS `tags_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags_index` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `tagUID` varchar(33) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxtags_indexUID` (`UID`(10)),
  KEY `idxtags_indexrefModule` (`refModule`(10)),
  KEY `idxtags_indexrefModel` (`refModel`(10)),
  KEY `idxtags_indexrefUID` (`refUID`(10)),
  KEY `idxtags_indexstatus` (`status`(5)),
  KEY `idxtags_indextagUID` (`tagUID`(10)),
  KEY `idxtags_indexcreatedOn` (`createdOn`),
  KEY `idxtags_indexcreatedBy` (`createdBy`(10)),
  KEY `idxtags_indexeditedOn` (`editedOn`),
  KEY `idxtags_indexeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags_tag`
--

DROP TABLE IF EXISTS `tags_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags_tag` (
  `UID` varchar(33) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `namelc` varchar(255) DEFAULT NULL,
  `objectCount` bigint(20) DEFAULT NULL,
  `embedCount` bigint(20) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  KEY `idxtags_tagUID` (`UID`(10)),
  KEY `idxtags_tagname` (`name`(10)),
  KEY `idxtags_tagobjectCount` (`objectCount`),
  KEY `idxtags_tagcreatedOn` (`createdOn`),
  KEY `idxtags_tagcreatedBy` (`createdBy`(10)),
  KEY `idxtags_tageditedOn` (`editedOn`),
  KEY `idxtags_tageditedBy` (`editedBy`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tmp_code_package_mnujn9mpl35al5nkmp`
--

DROP TABLE IF EXISTS `tmp_code_package_mnujn9mpl35al5nkmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmp_code_package_mnujn9mpl35al5nkmp` (
  `UID` varchar(30) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `version` varchar(100) DEFAULT NULL,
  `revision` varchar(100) DEFAULT NULL,
  `includes` text,
  `excludes` text,
  `installFile` varchar(255) DEFAULT NULL,
  `installFn` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tmp_code_package_wnp8wllyeyug8ek40m`
--

DROP TABLE IF EXISTS `tmp_code_package_wnp8wllyeyug8ek40m`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmp_code_package_wnp8wllyeyug8ek40m` (
  `UID` varchar(30) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `version` varchar(100) DEFAULT NULL,
  `revision` varchar(100) DEFAULT NULL,
  `includes` text,
  `excludes` text,
  `installFile` varchar(255) DEFAULT NULL,
  `installFn` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twitter_tweet`
--

DROP TABLE IF EXISTS `twitter_tweet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `twitter_tweet` (
  `UID` varchar(30) DEFAULT NULL,
  `refModule` varchar(50) DEFAULT NULL,
  `refModel` varchar(50) DEFAULT NULL,
  `refUID` varchar(30) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxtwitter_tweetUID` (`UID`(10)),
  KEY `idxtwitter_tweetrefModule` (`refModule`(10)),
  KEY `idxtwitter_tweetrefModel` (`refModel`(10)),
  KEY `idxtwitter_tweetrefUID` (`refUID`(10)),
  KEY `idxtwitter_tweetstatus` (`status`(5)),
  KEY `idxtwitter_tweetcreatedOn` (`createdOn`),
  KEY `idxtwitter_tweetcreatedBy` (`createdBy`(10)),
  KEY `idxtwitter_tweeteditedOn` (`editedOn`),
  KEY `idxtwitter_tweeteditedBy` (`editedBy`(10)),
  KEY `idxtwitter_tweetalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_friendship`
--

DROP TABLE IF EXISTS `users_friendship`;
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
  KEY `idxUsers_FriendshipUID` (`UID`(10)),
  KEY `idxUsers_FriendshipcreatedOn` (`createdOn`),
  KEY `idxUsers_FriendshipcreatedBy` (`createdBy`(10)),
  KEY `idxUsers_FriendshipeditedOn` (`editedOn`),
  KEY `idxUsers_FriendshipeditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_login`
--

DROP TABLE IF EXISTS `users_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_login` (
  `UID` varchar(33) DEFAULT NULL,
  `userUID` varchar(33) DEFAULT NULL,
  `serverUID` varchar(255) DEFAULT NULL,
  `serverName` varchar(255) DEFAULT NULL,
  `logintime` datetime DEFAULT NULL,
  `lastseen` datetime DEFAULT NULL,
  `status` datetime DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  `revision` bigint(20) DEFAULT NULL,
  KEY `idxusers_loginUID` (`UID`(10)),
  KEY `idxusers_loginuserUID` (`userUID`(10)),
  KEY `idxusers_logincreatedOn` (`createdOn`),
  KEY `idxusers_logincreatedBy` (`createdBy`(10)),
  KEY `idxusers_logineditedOn` (`editedOn`),
  KEY `idxusers_logineditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_notification`
--

DROP TABLE IF EXISTS `users_notification`;
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
-- Table structure for table `users_preset`
--

DROP TABLE IF EXISTS `users_preset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_preset` (
  `UID` varchar(30) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `cat` varchar(10) DEFAULT NULL,
  `settings` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxusers_presetUID` (`UID`(10)),
  KEY `idxusers_presettitle` (`title`(10)),
  KEY `idxusers_presetcreatedOn` (`createdOn`),
  KEY `idxusers_presetcreatedBy` (`createdBy`(10)),
  KEY `idxusers_preseteditedOn` (`editedOn`),
  KEY `idxusers_preseteditedBy` (`editedBy`(10)),
  KEY `idxusers_presetshared` (`shared`(1)),
  KEY `idxusers_presetalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_registry`
--

DROP TABLE IF EXISTS `users_registry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_registry` (
  `UID` varchar(30) DEFAULT NULL,
  `userUID` varchar(30) DEFAULT NULL,
  `settings` mediumtext,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(30) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxusers_registryUID` (`UID`(10)),
  KEY `idxusers_registryuserUID` (`userUID`(10)),
  KEY `idxusers_registrycreatedOn` (`createdOn`),
  KEY `idxusers_registrycreatedBy` (`createdBy`(10)),
  KEY `idxusers_registryeditedOn` (`editedOn`),
  KEY `idxusers_registryeditedBy` (`editedBy`(10)),
  KEY `idxusers_registryshared` (`shared`(1))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_role`
--

DROP TABLE IF EXISTS `users_role`;
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
  KEY `idxUsers_RoleUID` (`UID`(10)),
  KEY `idxUsers_RolecreatedOn` (`createdOn`),
  KEY `idxUsers_RolecreatedBy` (`createdBy`(10)),
  KEY `idxUsers_RoleeditedOn` (`editedOn`),
  KEY `idxUsers_RoleeditedBy` (`editedBy`(10)),
  KEY `idxUsers_Rolealias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_roleusers_role`
--

DROP TABLE IF EXISTS `users_roleusers_role`;
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

DROP TABLE IF EXISTS `users_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_session` (
  `UID` varchar(33) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `serverUID` varchar(255) DEFAULT NULL,
  `serverName` varchar(255) DEFAULT NULL,
  `serverURL` varchar(255) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  KEY `idxusers_sessionUID` (`UID`(10)),
  KEY `idxusers_sessionstatus` (`status`(3)),
  KEY `idxusers_sessioncreatedOn` (`createdOn`),
  KEY `idxusers_sessioncreatedBy` (`createdBy`(10)),
  KEY `idxusers_sessioneditedOn` (`editedOn`),
  KEY `idxusers_sessioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_user`
--

DROP TABLE IF EXISTS `users_user`;
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
  `settings` varchar(33) DEFAULT NULL,
  `lastOnline` varchar(255) DEFAULT NULL,
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
-- Table structure for table `videos_gallery`
--

DROP TABLE IF EXISTS `videos_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos_gallery` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `videocount` text,
  `origin` varchar(10) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  `revision` bigint(20) DEFAULT NULL,
  `alias` text,
  KEY `idxvideos_galleryUID` (`UID`(10)),
  KEY `idxvideos_galleryorigin` (`origin`(3)),
  KEY `idxvideos_gallerycreatedOn` (`createdOn`),
  KEY `idxvideos_gallerycreatedBy` (`createdBy`(10)),
  KEY `idxvideos_galleryeditedOn` (`editedOn`),
  KEY `idxvideos_galleryeditedBy` (`editedBy`(10)),
  KEY `idxvideos_galleryalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `videos_video`
--

DROP TABLE IF EXISTS `videos_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos_video` (
  `UID` varchar(33) DEFAULT NULL,
  `refModule` text,
  `refModel` text,
  `refUID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `licence` varchar(50) DEFAULT NULL,
  `attribName` varchar(255) DEFAULT NULL,
  `attribUrl` varchar(255) DEFAULT NULL,
  `fileName` varchar(255) DEFAULT NULL,
  `hash` varchar(50) DEFAULT NULL,
  `format` varchar(30) DEFAULT NULL,
  `transforms` mediumtext,
  `caption` text,
  `category` varchar(100) DEFAULT NULL,
  `weight` text,
  `length` text,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  `revision` bigint(20) DEFAULT NULL,
  `alias` text,
  KEY `idxvideos_videoUID` (`UID`(10)),
  KEY `idxvideos_videorefModule` (`refModule`(10)),
  KEY `idxvideos_videorefModel` (`refModel`(10)),
  KEY `idxvideos_videorefUID` (`refUID`(10)),
  KEY `idxvideos_videocreatedOn` (`createdOn`),
  KEY `idxvideos_videocreatedBy` (`createdBy`(10)),
  KEY `idxvideos_videoeditedOn` (`editedOn`),
  KEY `idxvideos_videoeditedBy` (`editedBy`(10)),
  KEY `idxvideos_videoalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_article`
--

DROP TABLE IF EXISTS `wiki_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_article` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `nav` text,
  `locked` varchar(30) DEFAULT NULL,
  `namespace` varchar(33) DEFAULT NULL,
  `talkFor` varchar(33) DEFAULT NULL,
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
-- Table structure for table `wiki_category`
--

DROP TABLE IF EXISTS `wiki_category`;
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
-- Table structure for table `wiki_mwimport`
--

DROP TABLE IF EXISTS `wiki_mwimport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_mwimport` (
  `UID` varchar(33) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `wikiUrl` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `categories` text,
  `assets` text,
  `status` varchar(30) DEFAULT NULL,
  `pageid` varchar(50) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(33) DEFAULT NULL,
  `editedOn` datetime DEFAULT NULL,
  `editedBy` varchar(33) DEFAULT NULL,
  `shared` varchar(3) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  KEY `idxwiki_mwimportUID` (`UID`(10)),
  KEY `idxwiki_mwimportpageid` (`pageid`(10)),
  KEY `idxwiki_mwimportcreatedOn` (`createdOn`),
  KEY `idxwiki_mwimportcreatedBy` (`createdBy`(10)),
  KEY `idxwiki_mwimporteditedOn` (`editedOn`),
  KEY `idxwiki_mwimporteditedBy` (`editedBy`(10)),
  KEY `idxwiki_mwimportalias` (`alias`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_revision`
--

DROP TABLE IF EXISTS `wiki_revision`;
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
  KEY `idxWiki_RevisionUID` (`UID`(10)),
  KEY `idxWiki_RevisioncreatedOn` (`createdOn`),
  KEY `idxWiki_RevisioncreatedBy` (`createdBy`(10)),
  KEY `idxWiki_RevisioneditedOn` (`editedOn`),
  KEY `idxWiki_RevisioneditedBy` (`editedBy`(10))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-05-27  4:30:19
