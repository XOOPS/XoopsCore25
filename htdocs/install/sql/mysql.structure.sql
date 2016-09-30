#
# Table structure for table `avatar`
#

CREATE TABLE avatar (
  avatar_id mediumint(8) unsigned NOT NULL auto_increment,
  avatar_file varchar(30) NOT NULL default '',
  avatar_name varchar(100) NOT NULL default '',
  avatar_mimetype varchar(30) NOT NULL default '',
  avatar_created int(10) NOT NULL default '0',
  avatar_display tinyint(1) unsigned NOT NULL default '0',
  avatar_weight smallint(5) unsigned NOT NULL default '0',
  avatar_type char(1) NOT NULL default '',
  PRIMARY KEY  (avatar_id),
  KEY avatar_type (avatar_type, avatar_display)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `avatar_user_link`
#

CREATE TABLE avatar_user_link (
  avatar_id mediumint(8) unsigned NOT NULL default '0',
  user_id mediumint(8) unsigned NOT NULL default '0',
  KEY avatar_user_id (avatar_id,user_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `banner`
#

CREATE TABLE banner (
  bid smallint(5) unsigned NOT NULL auto_increment,
  cid tinyint(3) unsigned NOT NULL default '0',
  imptotal int(10) unsigned NOT NULL default '0',
  impmade mediumint(8) unsigned NOT NULL default '0',
  clicks mediumint(8) unsigned NOT NULL default '0',
  imageurl varchar(255) NOT NULL default '',
  clickurl varchar(255) NOT NULL default '',
  date int(10) NOT NULL default '0',
  htmlbanner tinyint(1) NOT NULL default '0',
  htmlcode text,
  PRIMARY KEY  (bid),
  KEY idxbannercid (cid),
  KEY idxbannerbidcid (bid,cid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `bannerclient`
#

CREATE TABLE bannerclient (
  cid smallint(5) unsigned NOT NULL auto_increment,
  name varchar(60) NOT NULL default '',
  contact varchar(60) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  login varchar(10) NOT NULL default '',
  passwd varchar(10) NOT NULL default '',
  extrainfo text,
  PRIMARY KEY  (cid),
  KEY login (login)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `bannerfinish`
#

CREATE TABLE bannerfinish (
  bid smallint(5) unsigned NOT NULL auto_increment,
  cid smallint(5) unsigned NOT NULL default '0',
  impressions mediumint(8) unsigned NOT NULL default '0',
  clicks mediumint(8) unsigned NOT NULL default '0',
  datestart int(10) unsigned NOT NULL default '0',
  dateend int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (bid),
  KEY cid (cid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `block_module_link`
#

CREATE TABLE block_module_link (
  block_id mediumint(8) unsigned NOT NULL default '0',
  module_id smallint(5) NOT NULL default '0',
  PRIMARY KEY (`module_id`, `block_id`)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `comments`
#

CREATE TABLE xoopscomments (
  `com_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `com_pid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `com_rootid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `com_modid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `com_itemid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `com_icon` varchar(25) NOT NULL DEFAULT '',
  `com_created` int(10) unsigned NOT NULL DEFAULT '0',
  `com_modified` int(10) unsigned NOT NULL DEFAULT '0',
  `com_uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `com_user` varchar(60) NOT NULL DEFAULT '',
  `com_email` varchar(60) NOT NULL DEFAULT '',
  `com_url` varchar(60) NOT NULL DEFAULT '',
  `com_ip` varchar(45) NOT NULL DEFAULT '',
  `com_title` varchar(255) NOT NULL DEFAULT '',
  `com_text` text,
  `com_sig` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `com_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `com_exparams` varchar(255) NOT NULL DEFAULT '',
  `dohtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dosmiley` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `doxcode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `doimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dobr` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`com_id`),
  KEY `com_pid` (`com_pid`),
  KEY `com_itemid` (`com_itemid`),
  KEY `com_uid` (`com_uid`),
  KEY `com_title` (`com_title`(40)),
  KEY `com_status` (`com_status`),
  KEY `com_user` (`com_user`),
  KEY `com_email` (`com_email`)
) ENGINE=MyISAM;
# --------------------------------------------------------

# RMV-NOTIFY
# Table structure for table `notifications`
#

CREATE TABLE xoopsnotifications (
  not_id mediumint(8) unsigned NOT NULL auto_increment,
  not_modid smallint(5) unsigned NOT NULL default '0',
  not_itemid mediumint(8) unsigned NOT NULL default '0',
  not_category varchar(30) NOT NULL default '',
  not_event varchar(30) NOT NULL default '',
  not_uid mediumint(8) unsigned NOT NULL default '0',
  not_mode tinyint(1) NOT NULL default 0,
  PRIMARY KEY (not_id),
  KEY not_modid (not_modid),
  KEY not_itemid (not_itemid),
  KEY not_class (not_category),
  KEY not_uid (not_uid),
  KEY not_event (not_event)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `config`
#

CREATE TABLE config (
  conf_id smallint(5) unsigned NOT NULL auto_increment,
  conf_modid smallint(5) unsigned NOT NULL default '0',
  conf_catid smallint(5) unsigned NOT NULL default '0',
  conf_name varchar(25) NOT NULL default '',
  conf_title varchar(255) NOT NULL default '',
  conf_value text,
  conf_desc varchar(255) NOT NULL default '',
  conf_formtype varchar(15) NOT NULL default '',
  conf_valuetype varchar(10) NOT NULL default '',
  conf_order smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (conf_id),
  KEY conf_mod_cat_id (conf_modid, conf_catid),
  KEY conf_order (conf_order)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `configcategory`
#

CREATE TABLE configcategory (
  confcat_id smallint(5) unsigned NOT NULL auto_increment,
  confcat_name varchar(255) NOT NULL default '',
  confcat_order smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (confcat_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `configoption`
#

CREATE TABLE configoption (
  confop_id mediumint(8) unsigned NOT NULL auto_increment,
  confop_name varchar(255) NOT NULL default '',
  confop_value varchar(255) NOT NULL default '',
  conf_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (confop_id),
  KEY conf_id (conf_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `groups`
#

CREATE TABLE groups (
  groupid smallint(5) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  description text,
  group_type varchar(10) NOT NULL default '',

  PRIMARY KEY  (groupid),
  KEY group_type (group_type)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `group_permission`
#

CREATE TABLE group_permission (
  gperm_id int(10) unsigned NOT NULL auto_increment,
  gperm_groupid smallint(5) unsigned NOT NULL default '0',
  gperm_itemid mediumint(8) unsigned NOT NULL default '0',
  gperm_modid mediumint(5) unsigned NOT NULL default '0',
  gperm_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (gperm_id),
  KEY groupid (gperm_groupid),
  KEY itemid (gperm_itemid),
  KEY gperm_modid (gperm_modid,gperm_name(10))
) ENGINE=MyISAM;
# --------------------------------------------------------


#
# Table structure for table `groups_users_link`
#

CREATE TABLE groups_users_link (
  linkid mediumint(8) unsigned NOT NULL auto_increment,
  groupid smallint(5) unsigned NOT NULL default '0',
  uid mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (linkid),
  KEY groupid_uid (groupid,uid),
  KEY uid (uid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `image`
#

CREATE TABLE image (
  image_id mediumint(8) unsigned NOT NULL auto_increment,
  image_name varchar(30) NOT NULL default '',
  image_nicename varchar(255) NOT NULL default '',
  image_mimetype varchar(30) NOT NULL default '',
  image_created int(10) unsigned NOT NULL default '0',
  image_display tinyint(1) unsigned NOT NULL default '0',
  image_weight smallint(5) unsigned NOT NULL default '0',
  imgcat_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (image_id),
  KEY imgcat_id (imgcat_id),
  KEY image_display (image_display)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `imagebody`
#

CREATE TABLE imagebody (
  image_id mediumint(8) unsigned NOT NULL default '0',
  image_body mediumblob,
  KEY image_id (image_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `imagecategory`
#

CREATE TABLE imagecategory (
  imgcat_id smallint(5) unsigned NOT NULL auto_increment,
  imgcat_name varchar(100) NOT NULL default '',
  imgcat_maxsize int(8) unsigned NOT NULL default '0',
  imgcat_maxwidth smallint(3) unsigned NOT NULL default '0',
  imgcat_maxheight smallint(3) unsigned NOT NULL default '0',
  imgcat_display tinyint(1) unsigned NOT NULL default '0',
  imgcat_weight smallint(3) unsigned NOT NULL default '0',
  imgcat_type char(1) NOT NULL default '',
  imgcat_storetype varchar(5) NOT NULL default '',
  PRIMARY KEY  (imgcat_id),
  KEY imgcat_display (imgcat_display)
) ENGINE=MyISAM;
# --------------------------------------------------------


#
# Table structure for table `imgset`
#

CREATE TABLE imgset (
  imgset_id smallint(5) unsigned NOT NULL auto_increment,
  imgset_name varchar(50) NOT NULL default '',
  imgset_refid mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (imgset_id),
  KEY imgset_refid (imgset_refid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `imgset_tplset_link`
#

CREATE TABLE imgset_tplset_link (
  imgset_id smallint(5) unsigned NOT NULL default '0',
  tplset_name varchar(50) NOT NULL default '',
  KEY tplset_name (tplset_name(10))
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `imgsetimg`
#

CREATE TABLE imgsetimg (
  imgsetimg_id mediumint(8) unsigned NOT NULL auto_increment,
  imgsetimg_file varchar(50) NOT NULL default '',
  imgsetimg_body blob,
  imgsetimg_imgset smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (imgsetimg_id),
  KEY imgsetimg_imgset (imgsetimg_imgset)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `modules`
#

CREATE TABLE modules (
  mid smallint(5) unsigned NOT NULL auto_increment,
  name varchar(150) NOT NULL default '',
  version smallint(5) unsigned NOT NULL default '100',
  last_update int(10) unsigned NOT NULL default '0',
  weight smallint(3) unsigned NOT NULL default '0',
  isactive tinyint(1) unsigned NOT NULL default '0',
  dirname varchar(25) NOT NULL default '',
  hasmain tinyint(1) unsigned NOT NULL default '0',
  hasadmin tinyint(1) unsigned NOT NULL default '0',
  hassearch tinyint(1) unsigned NOT NULL default '0',
  hasconfig tinyint(1) unsigned NOT NULL default '0',
  hascomments tinyint(1) unsigned NOT NULL default '0',
  hasnotification tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (mid),
  KEY hasmain (hasmain),
  KEY hasadmin (hasadmin),
  KEY hassearch (hassearch),
  KEY hasnotification (hasnotification),
  KEY dirname (dirname),
  KEY name (name(15)),
  KEY isactive (isactive),
  KEY weight (weight),
  KEY hascomments (hascomments)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `newblocks`
#

CREATE TABLE newblocks (
  bid mediumint(8) unsigned NOT NULL auto_increment,
  mid smallint(5) unsigned NOT NULL default '0',
  func_num tinyint(3) unsigned NOT NULL default '0',
  options varchar(255) NOT NULL default '',
  name varchar(150) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  content text,
  side tinyint(1) unsigned NOT NULL default '0',
  weight smallint(5) unsigned NOT NULL default '0',
  visible tinyint(1) unsigned NOT NULL default '0',
  block_type char(1) NOT NULL default '',
  c_type char(1) NOT NULL default '',
  isactive tinyint(1) unsigned NOT NULL default '0',
  dirname varchar(50) NOT NULL default '',
  func_file varchar(50) NOT NULL default '',
  show_func varchar(50) NOT NULL default '',
  edit_func varchar(50) NOT NULL default '',
  template varchar(50) NOT NULL default '',
  bcachetime int(10) unsigned NOT NULL default '0',
  last_modified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (bid),
  KEY mid (mid),
  KEY visible (visible),
  KEY isactive_visible_mid (isactive,visible,mid),
  KEY mid_funcnum (mid,func_num)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `online`
#

CREATE TABLE online (
  online_uid mediumint(8) unsigned NOT NULL default '0',
  online_uname varchar(25) NOT NULL default '',
  online_updated int(10) unsigned NOT NULL default '0',
  online_module smallint(5) unsigned NOT NULL default '0',
  online_ip varchar(45) NOT NULL default '',
  KEY online_module (online_module),
  KEY online_updated (online_updated),
  KEY online_uid (online_uid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `priv_msgs`
#

CREATE TABLE priv_msgs (
  msg_id mediumint(8) unsigned NOT NULL auto_increment,
  msg_image varchar(100) default NULL,
  subject varchar(255) NOT NULL default '',
  from_userid mediumint(8) unsigned NOT NULL default '0',
  to_userid mediumint(8) unsigned NOT NULL default '0',
  msg_time int(10) unsigned NOT NULL default '0',
  msg_text text,
  read_msg tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (msg_id),
  KEY to_userid (to_userid),
  KEY touseridreadmsg (to_userid,read_msg),
  KEY msgidfromuserid (from_userid, msg_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `ranks`
#

CREATE TABLE ranks (
  rank_id smallint(5) unsigned NOT NULL auto_increment,
  rank_title varchar(50) NOT NULL default '',
  rank_min mediumint(8) unsigned NOT NULL default '0',
  rank_max mediumint(8) unsigned NOT NULL default '0',
  rank_special tinyint(1) unsigned NOT NULL default '0',
  rank_image varchar(255) default NULL,
  PRIMARY KEY  (rank_id),
  KEY rank_min (rank_min),
  KEY rank_max (rank_max),
  KEY rankminrankmaxranspecial (rank_min,rank_max,rank_special),
  KEY rankspecial (rank_special)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `session`
#

CREATE TABLE session (
  sess_id varchar(256) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL default '',
  sess_updated int(10) unsigned NOT NULL default '0',
  sess_ip varchar(45) NOT NULL default '',
  sess_data text,
  PRIMARY KEY  (sess_id),
  KEY updated (sess_updated)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `smiles`
#

CREATE TABLE smiles (
  id smallint(5) unsigned NOT NULL auto_increment,
  code varchar(50) NOT NULL default '',
  smile_url varchar(100) NOT NULL default '',
  emotion varchar(75) NOT NULL default '',
  display tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `tplset`
#

CREATE TABLE tplset (
  tplset_id int(7) unsigned NOT NULL auto_increment,
  tplset_name varchar(50) NOT NULL default '',
  tplset_desc varchar(255) NOT NULL default '',
  tplset_credits text,
  tplset_created int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (tplset_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `tplfile`
#
# irmtfan bug fix: solve templates duplicate issue
CREATE TABLE tplfile (
  tpl_id mediumint(7) unsigned NOT NULL auto_increment,
  tpl_refid smallint(5) unsigned NOT NULL default '0',
  tpl_module varchar(25) NOT NULL default '',
  tpl_tplset varchar(50) NOT NULL default '',
  tpl_file varchar(50) NOT NULL default '',
  tpl_desc varchar(255) NOT NULL default '',
  tpl_lastmodified int(10) unsigned NOT NULL default '0',
  tpl_lastimported int(10) unsigned NOT NULL default '0',
  tpl_type varchar(20) NOT NULL default '',
  PRIMARY KEY  (tpl_id),
  KEY tpl_refid (tpl_refid,tpl_type),
  KEY tpl_tplset (tpl_tplset,tpl_file(10)) ,
  UNIQUE tpl_refid_module_set_file_type (tpl_refid, tpl_module, tpl_tplset, tpl_file, tpl_type)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `tplsource`
#

CREATE TABLE tplsource (
  tpl_id mediumint(7) unsigned NOT NULL default '0',
  tpl_source mediumtext,
  KEY tpl_id (tpl_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

# Table structure for table `users`
#

CREATE TABLE users (
  uid mediumint(8) unsigned NOT NULL auto_increment,
  name varchar(60) NOT NULL default '',
  uname varchar(25) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  url varchar(100) NOT NULL default '',
  user_avatar varchar(30) NOT NULL default 'blank.gif',
  user_regdate int(10) unsigned NOT NULL default '0',
  user_icq varchar(15) NOT NULL default '',
  user_from varchar(100) NOT NULL default '',
  user_sig tinytext,
  user_viewemail tinyint(1) unsigned NOT NULL default '0',
  actkey varchar(8) NOT NULL default '',
  user_aim varchar(18) NOT NULL default '',
  user_yim varchar(25) NOT NULL default '',
  user_msnm varchar(100) NOT NULL default '',
  pass varchar(255) NOT NULL default '',
  posts mediumint(8) unsigned NOT NULL default '0',
  attachsig tinyint(1) unsigned NOT NULL default '0',
  rank smallint(5) unsigned NOT NULL default '0',
  level tinyint(3) unsigned NOT NULL default '1',
  theme varchar(100) NOT NULL default '',
  timezone_offset float(3,1) NOT NULL default '0.0',
  last_login int(10) unsigned NOT NULL default '0',
  umode varchar(10) NOT NULL default '',
  uorder tinyint(1) unsigned NOT NULL default '0',
  notify_method tinyint(1) NOT NULL default '1',
  notify_mode tinyint(1) NOT NULL default '0',
  user_occ varchar(100) NOT NULL default '',
  bio tinytext,
  user_intrest varchar(150) NOT NULL default '',
  user_mailok tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (uid),
  KEY uname (uname),
  KEY email (email),
  KEY uiduname (uid,uname),
  KEY level (level)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `cache_model`
#

CREATE TABLE cache_model (
  `cache_key`     varchar(64)     NOT NULL default '',
  `cache_expires` int(10)         unsigned NOT NULL default '0',
  `cache_data`    text,

  PRIMARY KEY  (`cache_key`),
  KEY `cache_expires` (`cache_expires`)
) ENGINE=MyISAM;
# --------------------------------------------------------
