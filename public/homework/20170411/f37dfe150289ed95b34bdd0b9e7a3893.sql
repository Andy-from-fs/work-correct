-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017-04-10 10:02:46
-- 服务器版本： 5.7.9
-- PHP Version: 7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `work`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `userid` int(100) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `identity` varchar(100) NOT NULL,
  `telephone` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=gbk;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`userid`, `username`, `identity`, `telephone`, `password`) VALUES
(1, 'Andy', '学生', '12345678910', '123'),
(2, 'test', '教师', '12345678914', '123'),
(3, 'k', '教师', '12345678911', '123'),
(4, 'Mia', '学生', '12345678912', '123'),
(5, 'Allen', '学生', '12345678913', '123'),
(13, 'haha', '学生', '12345678910', '123'),
(14, 'haha', '教师', '12345678910', '123'),
(15, 'haha', '学生', '12345678910', '123'),
(16, 'hahahh', '学生', '123485678910', '123'),
(17, 'oooo', '学生', '12345678912', '123');

-- --------------------------------------------------------

--
-- 表的结构 `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `courseid` int(100) NOT NULL AUTO_INCREMENT,
  `teacherid` int(100) NOT NULL,
  `classname` varchar(100) NOT NULL,
  `introduce` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=gbk;

--
-- 转存表中的数据 `course`
--

INSERT INTO `course` (`courseid`, `teacherid`, `classname`, `introduce`) VALUES
(1, 2, '高等数学（上）', '高等数学（上）是一门重要的基础学科。'),
(2, 2, '高等数学（下）', '高等数学（下）是一门重要的基础学科。'),
(3, 3, '大学英语', '大学英语是一门基础学科。'),
(4, 3, 'C++程序设计', 'C++程序设计是一门计算机入门学科。'),
(5, 2, '测试成功+17', '测试成功+17');

-- --------------------------------------------------------

--
-- 表的结构 `selectcourse`
--

DROP TABLE IF EXISTS `selectcourse`;
CREATE TABLE IF NOT EXISTS `selectcourse` (
  `studentid` int(100) NOT NULL,
  `courseid` int(100) NOT NULL,
  `selectcourseid` int(100) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`selectcourseid`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=gbk;

--
-- 转存表中的数据 `selectcourse`
--

INSERT INTO `selectcourse` (`studentid`, `courseid`, `selectcourseid`) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 4, 3),
(4, 1, 4),
(4, 2, 5),
(4, 3, 6),
(4, 4, 7),
(5, 1, 8),
(5, 2, 9),
(5, 3, 10),
(15, 5, 14),
(16, 5, 15),
(13, 5, 26);

-- --------------------------------------------------------

--
-- 表的结构 `work`
--

DROP TABLE IF EXISTS `work`;
CREATE TABLE IF NOT EXISTS `work` (
  `workid` int(100) NOT NULL AUTO_INCREMENT,
  `teacherid` int(100) NOT NULL,
  `workname` varchar(100) NOT NULL,
  `content` varchar(200) NOT NULL,
  `answer` varchar(200) DEFAULT NULL,
  `answername` varchar(200) DEFAULT NULL,
  `answerextension` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`workid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=gbk;

--
-- 转存表中的数据 `work`
--

INSERT INTO `work` (`workid`, `teacherid`, `workname`, `content`, `answer`, `answername`, `answerextension`) VALUES
(1, 2, '强化练习1', '完成书上的强化练习1', 'D:\\wamp64\\www\\work\\public\\uploads\\20170313\\822ae451e8a6eb0e4206d3fdca653c3b.docx', '强化练习1答案', '.docx'),
(3, 2, '强化练习2', '完成书上的强化练习2', 'D:\\wamp64\\www\\work\\public\\uploads\\20170313\\dbb85a302855e8bf2396872ef0422a05.xlsx', '强化练习2答案', '.xlsx'),
(4, 2, '基础练习1', '完成书上的基础练习1', 'D:\\wamp64\\www\\work\\public\\uploads\\20170313\\16c2e111ca3061685172e4c7bd2a9433', '基础练习1答案', '.txt'),
(5, 3, '阅读理解', '完成书本上的阅读理解', 'D:\\wamp64\\www\\work\\public\\answer\\20170409\\080d9b23d314f4f133daaee45fafbfa1.docx', '阅读理解答案', '.docx'),
(6, 3, 'C++基础题1', '完成书上C++基础题1', 'D:\\wamp64\\www\\work\\public\\uploads\\20170409\\f7dd7b5a6bbcb53dad646c64c6a3e37e.docx', 'C++基础题答案', '.docx');

-- --------------------------------------------------------

--
-- 表的结构 `worktostudent`
--

DROP TABLE IF EXISTS `worktostudent`;
CREATE TABLE IF NOT EXISTS `worktostudent` (
  `studentid` int(100) NOT NULL,
  `workid` int(100) NOT NULL,
  `document` varchar(200) DEFAULT NULL,
  `documentname` varchar(200) CHARACTER SET gb2312 DEFAULT NULL,
  `documentextension` varchar(100) DEFAULT NULL,
  `message` varchar(200) DEFAULT NULL,
  `grade` int(10) DEFAULT NULL,
  `worktostuid` int(100) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`worktostuid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=gbk;

--
-- 转存表中的数据 `worktostudent`
--

INSERT INTO `worktostudent` (`studentid`, `workid`, `document`, `documentname`, `documentextension`, `message`, `grade`, `worktostuid`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, 1),
(4, 1, 'D:\\wamp64\\www\\work\\public\\homework\\20170410\\21f26496baa5622ee50e0afcade57358.docx', '数据交互记忆', '.docx', '6666', 99, 2),
(5, 1, NULL, NULL, NULL, NULL, NULL, 3),
(1, 3, NULL, NULL, NULL, NULL, NULL, 7),
(4, 3, 'D:\\wamp64\\www\\work\\public\\homework\\20170410\\d2e732def14c4264f1b2ff6d77d29bcd.docx', '数据交互记忆', '.docx', NULL, NULL, 8),
(5, 3, NULL, NULL, NULL, NULL, NULL, 9),
(1, 4, NULL, NULL, NULL, NULL, NULL, 10),
(4, 4, NULL, NULL, NULL, NULL, NULL, 11),
(5, 4, NULL, NULL, NULL, NULL, NULL, 12),
(4, 5, NULL, NULL, NULL, NULL, NULL, 13),
(5, 5, NULL, NULL, NULL, NULL, NULL, 14),
(1, 6, NULL, NULL, NULL, NULL, NULL, 16),
(4, 6, NULL, NULL, NULL, NULL, NULL, 17);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
