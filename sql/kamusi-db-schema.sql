-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: kamusi
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.14.04.2

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
-- Table structure for table `ISO_639_3`
--

DROP TABLE IF EXISTS `ISO_639_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ISO_639_3` (
  `Id` char(3) NOT NULL,
  `Part2B` char(3) DEFAULT NULL,
  `Part2T` char(3) DEFAULT NULL,
  `Part1` char(2) DEFAULT NULL,
  `Scope` char(1) NOT NULL,
  `Type` char(1) NOT NULL,
  `Ref_Name` varchar(150) NOT NULL,
  `Comment` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TweetContext`
--

DROP TABLE IF EXISTS `TweetContext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TweetContext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TweetID` varchar(20) DEFAULT NULL,
  `Text` varchar(150) DEFAULT NULL,
  `Author` varchar(200) DEFAULT NULL,
  `UserID` varchar(64) DEFAULT NULL,
  `WordID` int(11) DEFAULT NULL,
  `Good` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UserID` (`UserID`),
  KEY `wordidkey` (`WordID`),
  CONSTRAINT `TweetContext_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `wordidkey` FOREIGN KEY (`WordID`) REFERENCES `words` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2704 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tweets`
--

DROP TABLE IF EXISTS `Tweets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tweets` (
  `TweetID` varchar(20) NOT NULL,
  `Text` varchar(150) DEFAULT NULL,
  `Author` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`TweetID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `WordTweet`
--

DROP TABLE IF EXISTS `WordTweet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WordTweet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `WordID` int(11) DEFAULT NULL,
  `TweetID` varchar(20) DEFAULT NULL,
  `UserID` varchar(64) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `WordID` (`WordID`,`TweetID`),
  KEY `TweetID` (`TweetID`),
  CONSTRAINT `wordidkeywordtweet` FOREIGN KEY (`WordID`) REFERENCES `words` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `Alias` varchar(64) DEFAULT NULL,
  `Email` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app`
--

DROP TABLE IF EXISTS `app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app` (
  `app_id` varchar(64) DEFAULT NULL,
  `app_secret` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `definitions`
--

DROP TABLE IF EXISTS `definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `definitions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) DEFAULT NULL,
  `Definition` varchar(256) DEFAULT NULL,
  `UserID` varchar(64) DEFAULT NULL,
  `Votes` int(11) DEFAULT '0',
  `reference` varchar(256) DEFAULT NULL,
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `UserID` (`UserID`),
  KEY `GroupID` (`GroupID`),
  KEY `language` (`language`),
  CONSTRAINT `definitions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `definitions_ibfk_2` FOREIGN KEY (`language`) REFERENCES `ISO_639_3` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=262161 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game4context`
--

DROP TABLE IF EXISTS `game4context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game4context` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) DEFAULT NULL,
  `sentenceid` int(11) DEFAULT NULL,
  `wordid` int(11) DEFAULT NULL,
  `good` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `sentenceid` (`sentenceid`),
  CONSTRAINT `game4context_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`UserID`),
  CONSTRAINT `sentenceids2` FOREIGN KEY (`sentenceid`) REFERENCES `game4sentences` (`sentenceid`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game4pointer`
--

DROP TABLE IF EXISTS `game4pointer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game4pointer` (
  `lemma` varchar(64) NOT NULL,
  `pointer` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`lemma`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game4sentences`
--

DROP TABLE IF EXISTS `game4sentences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game4sentences` (
  `sentenceid` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(64) DEFAULT NULL,
  `sentence` varchar(2000) DEFAULT NULL,
  `author` varchar(2000) DEFAULT NULL,
  `fileinfo` varchar(1000) DEFAULT NULL,
  `used` int(11) DEFAULT '0',
  PRIMARY KEY (`sentenceid`)
) ENGINE=InnoDB AUTO_INCREMENT=3152 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gamelanguages`
--

DROP TABLE IF EXISTS `gamelanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gamelanguages` (
  `LanguageID` char(3) NOT NULL,
  `GameID` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL,
  PRIMARY KEY (`LanguageID`,`GameID`),
  KEY `GameID` (`GameID`),
  CONSTRAINT `gamelanguages_ibfk_1` FOREIGN KEY (`LanguageID`) REFERENCES `ISO_639_3` (`Id`),
  CONSTRAINT `gamelanguages_ibfk_2` FOREIGN KEY (`GameID`) REFERENCES `gamelist` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gamelist`
--

DROP TABLE IF EXISTS `gamelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gamelist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) DEFAULT NULL,
  `game` int(11) DEFAULT '1',
  `position` int(11) DEFAULT '33',
  `offset` int(11) DEFAULT '1',
  `submissions` int(11) DEFAULT '0',
  `submissionsweek` int(11) DEFAULT '0',
  `submissionsmonth` int(11) DEFAULT '0',
  `points` int(11) DEFAULT '0',
  `pointsweek` int(11) DEFAULT '0',
  `pointsmonth` int(11) DEFAULT '0',
  `pendingpoints` int(11) DEFAULT '0',
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game` (`game`)
) ENGINE=InnoDB AUTO_INCREMENT=21821 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `interfacelanguages`
--

DROP TABLE IF EXISTS `interfacelanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interfacelanguages` (
  `LanguageID` char(3) NOT NULL,
  `locale` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`LanguageID`),
  CONSTRAINT `interfacelanguages_ibfk_1` FOREIGN KEY (`LanguageID`) REFERENCES `ISO_639_3` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pointtime`
--

DROP TABLE IF EXISTS `pointtime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pointtime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) DEFAULT NULL,
  `game` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT '1',
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `language` (`language`),
  CONSTRAINT `pointtime_ibfk_1` FOREIGN KEY (`language`) REFERENCES `ISO_639_3` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos`
--

DROP TABLE IF EXISTS `pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Code` varchar(64) DEFAULT NULL,
  `Full` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rankedwords`
--

DROP TABLE IF EXISTS `rankedwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rankedwords` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `Word` varchar(64) DEFAULT NULL,
  `PartOfSpeech` varchar(16) DEFAULT NULL,
  `Rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RankIndex` (`Rank`),
  KEY `WORD_INDEX` (`Word`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seengames`
--

DROP TABLE IF EXISTS `seengames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seengames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) DEFAULT NULL,
  `game` int(11) DEFAULT NULL,
  `wordid` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seengames_words` (`wordid`),
  KEY `game` (`game`),
  KEY `game_2` (`game`),
  KEY `language_2` (`language`),
  CONSTRAINT `seengames_ibfk_1` FOREIGN KEY (`language`) REFERENCES `ISO_639_3` (`Id`),
  CONSTRAINT `seengames_words` FOREIGN KEY (`wordid`) REFERENCES `words` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=875 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submissiontime`
--

DROP TABLE IF EXISTS `submissiontime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissiontime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) DEFAULT NULL,
  `game` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT '1',
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `language` (`language`),
  CONSTRAINT `submissiontime_ibfk_1` FOREIGN KEY (`language`) REFERENCES `ISO_639_3` (`Id`),
  CONSTRAINT `submissiontime_ibfk_2` FOREIGN KEY (`language`) REFERENCES `ISO_639_3` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tempLanguageMapping`
--

DROP TABLE IF EXISTS `tempLanguageMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempLanguageMapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charId` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WordID` int(11) DEFAULT NULL,
  `UserID` varchar(64) DEFAULT NULL,
  `Translation` varchar(64) DEFAULT NULL,
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `WordID` (`WordID`),
  KEY `UserID` (`UserID`),
  KEY `language` (`language`),
  CONSTRAINT `translations_ibfk_1` FOREIGN KEY (`WordID`) REFERENCES `words` (`ID`),
  CONSTRAINT `translations_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `translations_ibfk_3` FOREIGN KEY (`language`) REFERENCES `ISO_639_3` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `UserID` varchar(64) NOT NULL DEFAULT '',
  `Username` varchar(64) DEFAULT NULL,
  `LastNotification` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `NewPointsSinceLastNotification` int(11) DEFAULT '0',
  `NotificationTimeUnit` char(1) DEFAULT NULL,
  `DoPost` int(1) DEFAULT NULL,
  `WordTweetsSinceLastPost` int(11) DEFAULT '0',
  `PostTimeUnit` char(1) DEFAULT NULL,
  `Rating` int(11) DEFAULT '0',
  `Notify` int(11) DEFAULT '0',
  `Mute` int(11) DEFAULT '0',
  `NumReports` int(11) DEFAULT '0',
  `firsttime` tinyint(1) DEFAULT '1',
  `NotificationFrequency` int(11) DEFAULT NULL,
  `gamelanguage` char(3) DEFAULT NULL,
  `interfacelanguage` char(3) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  KEY `gamelanguage` (`gamelanguage`),
  KEY `interfacelanguage` (`interfacelanguage`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`gamelanguage`) REFERENCES `ISO_639_3` (`Id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`interfacelanguage`) REFERENCES `ISO_639_3` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `worddefinitions`
--

DROP TABLE IF EXISTS `worddefinitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worddefinitions` (
  `wordnetid` varchar(64) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headword` varchar(80) DEFAULT NULL,
  `kamusipos` varchar(64) DEFAULT NULL,
  `wordnetpos` varchar(64) DEFAULT NULL,
  `definition` varchar(512) DEFAULT NULL,
  `wordnetreference` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=197005 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wordfailure`
--

DROP TABLE IF EXISTS `wordfailure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wordfailure` (
  `wordid` int(11) NOT NULL,
  `failures` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`wordid`),
  CONSTRAINT `woridkey` FOREIGN KEY (`wordid`) REFERENCES `words` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wordnet`
--

DROP TABLE IF EXISTS `wordnet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wordnet` (
  `ID` int(11) DEFAULT NULL,
  `Word` varchar(64) DEFAULT NULL,
  `Definition` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `words` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Word` varchar(80) DEFAULT NULL,
  `PartOfSpeech` varchar(64) DEFAULT NULL,
  `DefinitionID` int(11) DEFAULT NULL,
  `reference` varchar(256) DEFAULT NULL,
  `senttokamusi` tinyint(4) DEFAULT '0',
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `WORD_INDEX` (`Word`),
  KEY `DefinitionID` (`DefinitionID`),
  KEY `language` (`language`),
  CONSTRAINT `words_ibfk_1` FOREIGN KEY (`language`) REFERENCES `ISO_639_3` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=197005 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wordsentence`
--

DROP TABLE IF EXISTS `wordsentence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wordsentence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) DEFAULT NULL,
  `wordid` int(11) DEFAULT NULL,
  `sentenceid` int(11) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wordid` (`wordid`,`sentenceid`),
  KEY `sentenceid` (`sentenceid`),
  CONSTRAINT `sentenceids` FOREIGN KEY (`sentenceid`) REFERENCES `game4sentences` (`sentenceid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wordtranslation`
--

DROP TABLE IF EXISTS `wordtranslation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wordtranslation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wordid` int(11) DEFAULT NULL,
  `translation` varchar(300) DEFAULT NULL,
  `userid` varchar(64) DEFAULT NULL,
  `language` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-11-09 14:45:02
