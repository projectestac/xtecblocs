-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Temps de generació: 14-03-2017 a les 13:55:51
-- Versió del servidor: 5.5.54-0ubuntu0.14.04.1
-- Versió de PHP: 5.6.30-7+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de dades: `xtec_blocs_global`
--

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_blogs`
--

CREATE TABLE IF NOT EXISTS `wp_blogs` (
  `blog_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL DEFAULT '0',
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  `registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `public` tinyint(2) NOT NULL DEFAULT '1',
  `archived` tinyint(2) NOT NULL DEFAULT '0',
  `mature` tinyint(2) NOT NULL DEFAULT '0',
  `spam` tinyint(2) NOT NULL DEFAULT '0',
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  `lang_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`blog_id`),
  KEY `domain` (`domain`(50),`path`(5)),
  KEY `lang_id` (`lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57 ;

--
-- Bolcant dades de la taula `wp_blogs`
--

INSERT INTO `wp_blogs` (`blog_id`, `site_id`, `domain`, `path`, `registered`, `last_updated`, `public`, `archived`, `mature`, `spam`, `deleted`, `lang_id`) VALUES
(1, 1, 'agora', '/blocs/', '2012-12-19 13:08:45', '2015-03-11 10:50:12', 1, 0, 0, 0, 0, 0),
(5, 1, 'agora', '/blocs/lestortugues/', '2012-12-20 11:12:26', '2015-04-20 12:15:47', 1, 0, 0, 0, 0, 1),
(3, 1, 'agora', '/blocs/elsdofins/', '2012-12-20 08:51:02', '2015-04-20 12:16:10', 1, 0, 0, 0, 0, 0),
(4, 1, 'agora', '/blocs/elscargols/', '2012-12-20 09:20:19', '2015-04-20 12:16:16', 1, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_blog_versions`
--

CREATE TABLE IF NOT EXISTS `wp_blog_versions` (
  `blog_id` bigint(20) NOT NULL DEFAULT '0',
  `db_version` varchar(20) NOT NULL DEFAULT '',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`blog_id`),
  KEY `db_version` (`db_version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Bolcant dades de la taula `wp_blog_versions`
--

INSERT INTO `wp_blog_versions` (`blog_id`, `db_version`, `last_updated`) VALUES
(1, '36686', '2014-01-10 14:15:36'),
(6, '29630', '2014-01-10 14:26:31'),
(5, '36686', '2014-01-10 14:26:33'),
(4, '36686', '2014-01-10 14:26:34'),
(3, '36686', '2014-01-10 14:26:35');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_cas_count`
--

CREATE TABLE IF NOT EXISTS `wp_cas_count` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_cas_image`
--

CREATE TABLE IF NOT EXISTS `wp_cas_image` (
  `id` int(10) NOT NULL,
  `createtime` int(10) NOT NULL,
  `word` varchar(20) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_commentmeta`
--

CREATE TABLE IF NOT EXISTS `wp_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_comments`
--

CREATE TABLE IF NOT EXISTS `wp_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT '',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_delblocs`
--

CREATE TABLE IF NOT EXISTS `wp_delblocs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(60) NOT NULL,
  `site_path` varchar(100) NOT NULL,
  `blogname` varchar(255) NOT NULL,
  `del_date` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_delblocs_users`
--

CREATE TABLE IF NOT EXISTS `wp_delblocs_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_login` varchar(60) NOT NULL,
  `display_name` varchar(60) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `meta_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_descriptors`
--

CREATE TABLE IF NOT EXISTS `wp_descriptors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descriptor` varchar(50) NOT NULL DEFAULT '',
  `number` int(11) NOT NULL DEFAULT '0',
  `blogs` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `descriptor` (`descriptor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_descriptors_pre`
--

CREATE TABLE IF NOT EXISTS `wp_descriptors_pre` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `descriptor` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `descriptor` (`descriptor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- Bolcant dades de la taula `wp_descriptors_pre`
--

INSERT INTO `wp_descriptors_pre` (`id`, `descriptor`) VALUES
(1, 'matemàtiques'),
(2, 'socials'),
(3, 'català'),
(4, 'castellà'),
(5, 'descoberta'),
(6, 'comunicació'),
(7, 'literatura'),
(8, 'aranès'),
(9, 'idiomes'),
(10, 'naturals'),
(11, 'música'),
(12, 'art'),
(13, 'visual'),
(14, 'plàstica'),
(15, 'física'),
(16, 'drets'),
(17, 'ciutadania'),
(18, 'tutoria'),
(19, 'religió'),
(20, 'tecnologia'),
(21, 'clàssiques'),
(22, 'filosofia'),
(23, 'història'),
(24, 'biologia'),
(25, 'química'),
(26, 'dibuix'),
(27, 'economia'),
(28, 'organització'),
(29, 'empresa'),
(30, 'geografia'),
(31, 'grec'),
(32, 'contemporani'),
(33, 'món'),
(34, 'electrotècnia'),
(35, 'llatí'),
(36, 'industrial'),
(37, 'mecànica'),
(38, 'disseny'),
(39, 'imatge'),
(40, 'expressió'),
(41, 'volum'),
(42, 'recerca'),
(43, 'primària'),
(44, 'batxillerat'),
(45, 'secundària'),
(46, 'cicles');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_globalposts`
--

CREATE TABLE IF NOT EXISTS `wp_globalposts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `blogId` int(10) NOT NULL DEFAULT '0',
  `time` varchar(20) NOT NULL DEFAULT '',
  `postType` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Bolcant dades de la taula `wp_globalposts`
--

INSERT INTO `wp_globalposts` (`id`, `blogId`, `time`, `postType`) VALUES
(2, 6, '1424337300', 1),
(3, 3, '1424347939', 1),
(4, 3, '1424348206', 1),
(5, 18, '1425287276', 1),
(6, 1, '1425887281', 1),
(7, 1, '1425887377', 1),
(8, 3, '1426064756', 1),
(9, 1, '1426071012', 1),
(10, 3, '1426604451', 1),
(11, 3, '1427783806', 1),
(12, 4, '1427783812', 1),
(13, 5, '1427783821', 1),
(14, 4, '1428389390', 1),
(15, 52, '1428389399', 1),
(16, 3, '1428389407', 1),
(17, 5, '1428389429', 1);

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_links`
--

CREATE TABLE IF NOT EXISTS `wp_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Bolcant dades de la taula `wp_links`
--

INSERT INTO `wp_links` (`link_id`, `link_url`, `link_name`, `link_image`, `link_target`, `link_description`, `link_visible`, `link_owner`, `link_rating`, `link_updated`, `link_rel`, `link_notes`, `link_rss`) VALUES
(1, 'http://codex.wordpress.org/', 'Documentation', '', '', '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '', ''),
(2, 'http://wordpress.org/news/', 'WordPress Blog', '', '', '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '', 'http://wordpress.org/news/feed/'),
(3, 'http://wordpress.org/extend/ideas/', 'Suggest Ideas', '', '', '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '', ''),
(4, 'http://wordpress.org/support/', 'Support Forum', '', '', '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '', ''),
(5, 'http://wordpress.org/extend/plugins/', 'Plugins', '', '', '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '', ''),
(6, 'http://wordpress.org/extend/themes/', 'Themes', '', '', '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '', ''),
(7, 'http://planet.wordpress.org/', 'WordPress Planet', '', '', '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_options`
--

CREATE TABLE IF NOT EXISTS `wp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) DEFAULT NULL,
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1100 ;

--
-- Bolcant dades de la taula `wp_options`
--

INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1, 'siteurl', 'http://agora/blocs', 'yes'),
(2, 'blogname', 'XTECBlocs', 'yes'),
(3, 'blogdescription', 'Portal XTECBlocs', 'yes'),
(4, 'users_can_register', '0', 'yes'),
(5, 'admin_email', 'admin@blocs.xtec.cat', 'yes'),
(6, 'start_of_week', '1', 'yes'),
(7, 'use_balanceTags', '0', 'yes'),
(8, 'use_smilies', '1', 'yes'),
(9, 'require_name_email', '', 'yes'),
(10, 'comments_notify', '1', 'yes'),
(11, 'posts_per_rss', '10', 'yes'),
(12, 'rss_use_excerpt', '0', 'yes'),
(13, 'mailserver_url', 'mail.example.com', 'yes'),
(14, 'mailserver_login', 'login@example.com', 'yes'),
(15, 'mailserver_pass', 'password', 'yes'),
(16, 'mailserver_port', '110', 'yes'),
(17, 'default_category', '1', 'yes'),
(18, 'default_comment_status', 'closed', 'yes'),
(19, 'default_ping_status', 'open', 'yes'),
(20, 'default_pingback_flag', '', 'yes'),
(22, 'posts_per_page', '10', 'yes'),
(23, 'date_format', 'd/M/y', 'yes'),
(24, 'time_format', 'g:i a', 'yes'),
(25, 'links_updated_date_format', 'j F Y G:i', 'yes'),
(29, 'comment_moderation', '', 'yes'),
(30, 'moderation_notify', '1', 'yes'),
(31, 'permalink_structure', '/blog/%year%/%monthnum%/%day%/%postname%/', 'yes'),
(33, 'hack_file', '0', 'yes'),
(34, 'blog_charset', 'UTF-8', 'yes'),
(35, 'moderation_keys', '', 'no'),
(36, 'active_plugins', 'a:4:{i:0;s:25:"add-to-any/add-to-any.php";i:1;s:49:"google-calendar-events/google-calendar-events.php";i:2;s:57:"multisite-clone-duplicator/multisite-clone-duplicator.php";i:4;s:33:"xtec-weekblog2/xtec-weekblog2.php";}', 'yes'),
(37, 'home', 'http://agora/blocs', 'yes'),
(38, 'category_base', '', 'yes'),
(39, 'ping_sites', 'http://rpc.pingomatic.com/', 'yes'),
(41, 'comment_max_links', '2', 'yes'),
(42, 'gmt_offset', '', 'yes'),
(43, 'default_email_category', '1', 'yes'),
(44, 'recently_edited', 'a:3:{i:0;s:51:"/srv/www/blocs/src/wp-content/themes/home/style.css";i:1;s:71:"/srv/www/blocs/src/wp-content/plugins/addthis/addthis_social_widget.php";i:2;s:0:"";}', 'no'),
(45, 'template', 'xtecblocsdefault', 'yes'),
(46, 'stylesheet', 'xtecblocsdefault', 'yes'),
(47, 'comment_whitelist', '1', 'yes'),
(48, 'blacklist_keys', '', 'no'),
(49, 'comment_registration', '1', 'yes'),
(51, 'html_type', 'text/html', 'yes'),
(52, 'use_trackback', '0', 'yes'),
(53, 'default_role', 'subscriber', 'yes'),
(54, 'db_version', '36686', 'yes'),
(55, 'uploads_use_yearmonth_folders', '1', 'yes'),
(56, 'upload_path', 'wp-content/uploads', 'yes'),
(57, 'blog_public', '1', 'yes'),
(58, 'default_link_category', '2', 'yes'),
(59, 'show_on_front', 'posts', 'yes'),
(60, 'tag_base', '', 'yes'),
(61, 'show_avatars', '1', 'yes'),
(62, 'avatar_rating', 'G', 'yes'),
(63, 'upload_url_path', '', 'yes'),
(64, 'thumbnail_size_w', '150', 'yes'),
(65, 'thumbnail_size_h', '150', 'yes'),
(66, 'thumbnail_crop', '1', 'yes'),
(67, 'medium_size_w', '300', 'yes'),
(68, 'medium_size_h', '300', 'yes'),
(69, 'avatar_default', 'mystery', 'yes'),
(72, 'large_size_w', '1024', 'yes'),
(73, 'large_size_h', '1024', 'yes'),
(74, 'image_default_link_type', 'file', 'yes'),
(75, 'image_default_size', '', 'yes'),
(76, 'image_default_align', '', 'yes'),
(77, 'close_comments_for_old_posts', '', 'yes'),
(78, 'close_comments_days_old', '14', 'yes'),
(79, 'thread_comments', '', 'yes'),
(80, 'thread_comments_depth', '5', 'yes'),
(81, 'page_comments', '1', 'yes'),
(82, 'comments_per_page', '50', 'yes'),
(83, 'default_comments_page', 'newest', 'yes'),
(84, 'comment_order', 'asc', 'yes'),
(85, 'sticky_posts', 'a:0:{}', 'yes'),
(86, 'widget_categories', 'a:2:{i:2;a:4:{s:5:"title";s:0:"";s:5:"count";i:0;s:12:"hierarchical";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}', 'yes'),
(87, 'widget_text', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(88, 'widget_rss', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(89, 'timezone_string', 'Europe/Madrid', 'yes'),
(91, 'embed_size_w', '', 'yes'),
(92, 'embed_size_h', '600', 'yes'),
(93, 'page_for_posts', '0', 'yes'),
(94, 'page_on_front', '0', 'yes'),
(95, 'default_post_format', '0', 'yes'),
(96, 'wp_user_roles', 'a:5:{s:13:"administrator";a:2:{s:4:"name";s:13:"Administrator";s:12:"capabilities";a:64:{s:13:"switch_themes";b:1;s:11:"edit_themes";b:1;s:16:"activate_plugins";b:1;s:12:"edit_plugins";b:1;s:10:"edit_users";b:1;s:10:"edit_files";b:1;s:14:"manage_options";b:1;s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:6:"import";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:8:"level_10";b:1;s:7:"level_9";b:1;s:7:"level_8";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;s:12:"delete_users";b:1;s:12:"create_users";b:1;s:17:"unfiltered_upload";b:1;s:14:"edit_dashboard";b:1;s:14:"update_plugins";b:1;s:14:"delete_plugins";b:1;s:15:"install_plugins";b:1;s:13:"update_themes";b:1;s:14:"install_themes";b:1;s:11:"update_core";b:1;s:10:"list_users";b:1;s:12:"remove_users";b:1;s:13:"promote_users";b:1;s:18:"edit_theme_options";b:1;s:13:"delete_themes";b:1;s:6:"export";b:1;s:45:"slideshow-jquery-image-gallery-add-slideshows";b:1;s:46:"slideshow-jquery-image-gallery-edit-slideshows";b:1;s:48:"slideshow-jquery-image-gallery-delete-slideshows";b:1;}}s:6:"editor";a:2:{s:4:"name";s:6:"Editor";s:12:"capabilities";a:37:{s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;s:45:"slideshow-jquery-image-gallery-add-slideshows";b:1;s:46:"slideshow-jquery-image-gallery-edit-slideshows";b:1;s:48:"slideshow-jquery-image-gallery-delete-slideshows";b:1;}}s:6:"author";a:2:{s:4:"name";s:6:"Author";s:12:"capabilities";a:13:{s:12:"upload_files";b:1;s:10:"edit_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:4:"read";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;s:22:"delete_published_posts";b:1;s:45:"slideshow-jquery-image-gallery-add-slideshows";b:1;s:46:"slideshow-jquery-image-gallery-edit-slideshows";b:1;s:48:"slideshow-jquery-image-gallery-delete-slideshows";b:1;}}s:11:"contributor";a:2:{s:4:"name";s:11:"Contributor";s:12:"capabilities";a:6:{s:10:"edit_posts";b:1;s:4:"read";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;s:12:"upload_files";b:1;}}s:10:"subscriber";a:2:{s:4:"name";s:10:"Subscriber";s:12:"capabilities";a:2:{s:4:"read";b:1;s:7:"level_0";b:1;}}}', 'yes'),
(97, 'widget_search', 'a:2:{i:2;a:1:{s:5:"title";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(98, 'widget_recent-posts', 'a:2:{i:2;a:2:{s:5:"title";s:0:"";s:6:"number";i:5;}s:12:"_multiwidget";i:1;}', 'yes'),
(99, 'widget_recent-comments', 'a:2:{i:2;a:2:{s:5:"title";s:0:"";s:6:"number";i:5;}s:12:"_multiwidget";i:1;}', 'yes'),
(100, 'widget_archives', 'a:2:{i:2;a:3:{s:5:"title";s:0:"";s:5:"count";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}', 'yes'),
(101, 'widget_meta', 'a:2:{i:2;a:1:{s:5:"title";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(102, 'sidebars_widgets', 'a:2:{s:19:"wp_inactive_widgets";a:13:{i:0;s:7:"pages-2";i:1;s:10:"calendar-2";i:2;s:7:"links-2";i:3;s:6:"text-2";i:4;s:5:"rss-2";i:5;s:11:"tag_cloud-2";i:6;s:10:"nav_menu-2";i:7;s:8:"search-2";i:8;s:14:"recent-posts-2";i:9;s:17:"recent-comments-2";i:10;s:10:"archives-2";i:11;s:12:"categories-2";i:12;s:6:"meta-2";}s:13:"array_version";i:3;}', 'yes'),
(185, '$xtec_descriptors_db_version', '1.0', 'yes'),
(103, 'cron', 'a:6:{i:1489496717;a:3:{s:16:"wp_update_themes";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:16:"wp_version_check";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:17:"wp_update_plugins";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}}i:1489496802;a:1:{s:19:"wp_scheduled_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1489497102;a:1:{s:21:"update_network_counts";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}}i:1489499049;a:1:{s:11:"wp_cache_gc";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:2:{s:8:"schedule";b:0;s:4:"args";a:0:{}}}}i:1489564075;a:1:{s:30:"wp_scheduled_auto_draft_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}s:7:"version";i:2;}', 'yes'),
(1077, 'slideshow-plugin-updated-from-v2-to-v2-1-20', 'updated', 'no'),
(1075, 'simple-calendar_settings_calendars', 'a:1:{s:7:"general";s:9:"post,page";}', 'yes'),
(1076, 'slideshow-plugin-updated-from-v1-x-x-to-v2-0-1', 'updated', 'no'),
(265, 'theme_mods_twentytwelve', 'a:2:{i:0;b:0;s:16:"sidebars_widgets";a:2:{s:4:"time";i:1389360134;s:4:"data";a:2:{s:19:"wp_inactive_widgets";a:13:{i:0;s:7:"pages-2";i:1;s:10:"calendar-2";i:2;s:7:"links-2";i:3;s:6:"text-2";i:4;s:5:"rss-2";i:5;s:11:"tag_cloud-2";i:6;s:10:"nav_menu-2";i:7;s:8:"search-2";i:8;s:14:"recent-posts-2";i:9;s:17:"recent-comments-2";i:10;s:10:"archives-2";i:11;s:12:"categories-2";i:12;s:6:"meta-2";}s:9:"sidebar-1";N;}}}', 'yes'),
(105, '_site_transient_update_core', 'O:8:"stdClass":3:{s:7:"updates";a:2:{i:0;O:8:"stdClass":7:{s:8:"response";s:7:"upgrade";s:3:"url";s:24:"http://ca.wordpress.org/";s:7:"package";s:44:"http://ca.wordpress.org/wordpress-3.5-ca.zip";s:7:"current";s:3:"3.5";s:6:"locale";s:2:"ca";s:11:"php_version";s:5:"5.2.4";s:13:"mysql_version";s:3:"5.0";}i:1;O:8:"stdClass":7:{s:8:"response";s:7:"upgrade";s:3:"url";s:30:"http://wordpress.org/download/";s:7:"package";s:38:"http://wordpress.org/wordpress-3.5.zip";s:7:"current";s:3:"3.5";s:6:"locale";s:5:"en_US";s:11:"php_version";s:5:"5.2.4";s:13:"mysql_version";s:3:"5.0";}}s:12:"last_checked";i:1355993076;s:15:"version_checked";s:5:"3.1.4";}', 'yes'),
(106, '_site_transient_update_plugins', 'O:8:"stdClass":3:{s:12:"last_checked";i:1355993076;s:7:"checked";a:32:{s:33:"addthis/addthis_social_widget.php";s:5:"2.3.0";s:37:"blogger-importer/blogger-importer.php";s:3:"0.5";s:51:"creative-commons-configurator-1/cc-configurator.php";s:5:"1.4.0";s:43:"google-analyticator/google-analyticator.php";s:5:"6.1.3";s:43:"multisite-plugin-manager/plugin-manager.php";s:5:"3.1.2";s:50:"peters-custom-anti-spam-image/custom_anti_spam.php";s:5:"3.1.4";s:32:"simpler-ipaper/scribd-ipaper.php";s:5:"1.3.1";s:97:"simple-trackback-validation-with-topsy-blocker/simple-trackback-validation-with-topsy-blocker.php";s:3:"0.7";s:25:"slideshare/slideshare.php";s:5:"1.7.2";s:49:"vipers-video-quicktags/vipers-video-quicktags.php";s:5:"6.3.0";s:41:"wordpress-importer/wordpress-importer.php";s:3:"0.6";s:29:"wp-recaptcha/wp-recaptcha.php";s:5:"3.1.6";s:27:"wp-super-cache/wp-cache.php";s:3:"1.2";s:37:"xtec-allowedTags/xtec-allowedTags.php";s:3:"1.0";s:21:"xtec-api/xtec-api.php";s:3:"1.0";s:37:"xtec-descriptors/xtec-descriptors.php";s:3:"1.1";s:33:"xtec-favorites/xtec-favorites.php";s:3:"1.0";s:23:"xtec-info/xtec-info.php";s:3:"1.1";s:31:"xtec-iso2utf8/xtec-iso2utf8.php";s:3:"1.1";s:41:"xtec-lastest-posts/xtec-lastest-posts.php";s:3:"1.0";s:35:"xtec-ldap-login/xtec-ldap-login.php";s:3:"1.1";s:37:"xtec-link-player/xtec-link-player.php";s:3:"1.1";s:23:"xtec-mail/xtec-mail.php";s:3:"2.0";s:37:"xtec-maintenance/xtec-maintenance.php";s:3:"1.1";s:35:"xtec-real-media/xtec-real-media.php";s:3:"1.0";s:27:"xtec-search/xtec-search.php";s:3:"1.1";s:31:"xtec-settings/xtec-settings.php";s:3:"1.1";s:27:"xtec-signup/xtec-signup.php";s:3:"1.1";s:28:"xtec-tinymce/xtectinymce.php";s:3:"1.0";s:25:"xtec-users/xtec-users.php";s:3:"1.1";s:35:"xtec-viquiatles/xtec-viquiatles.php";s:3:"1.1";s:33:"xtec-weekblog2/xtec-weekblog2.php";s:3:"1.0";}s:8:"response";a:4:{s:33:"addthis/addthis_social_widget.php";O:8:"stdClass":6:{s:2:"id";s:4:"5710";s:4:"slug";s:7:"addthis";s:11:"new_version";s:5:"3.0.2";s:14:"upgrade_notice";s:10:"Bug fixes.";s:3:"url";s:44:"http://wordpress.org/extend/plugins/addthis/";s:7:"package";s:55:"http://downloads.wordpress.org/plugin/addthis.3.0.2.zip";}s:97:"simple-trackback-validation-with-topsy-blocker/simple-trackback-validation-with-topsy-blocker.php";O:8:"stdClass":5:{s:2:"id";s:5:"14827";s:4:"slug";s:46:"simple-trackback-validation-with-topsy-blocker";s:11:"new_version";s:5:"1.1.5";s:3:"url";s:83:"http://wordpress.org/extend/plugins/simple-trackback-validation-with-topsy-blocker/";s:7:"package";s:88:"http://downloads.wordpress.org/plugin/simple-trackback-validation-with-topsy-blocker.zip";}s:25:"slideshare/slideshare.php";O:8:"stdClass":5:{s:2:"id";s:4:"1569";s:4:"slug";s:10:"slideshare";s:11:"new_version";s:3:"1.8";s:3:"url";s:47:"http://wordpress.org/extend/plugins/slideshare/";s:7:"package";s:58:"http://downloads.wordpress.org/plugin/slideshare.1.8.1.zip";}s:49:"vipers-video-quicktags/vipers-video-quicktags.php";O:8:"stdClass":6:{s:2:"id";s:3:"530";s:4:"slug";s:22:"vipers-video-quicktags";s:11:"new_version";s:5:"6.4.4";s:14:"upgrade_notice";s:108:"Updates to support new version of jQuery UI that is included in WordPress 3.5. Fixes dialog box not opening.";s:3:"url";s:59:"http://wordpress.org/extend/plugins/vipers-video-quicktags/";s:7:"package";s:70:"http://downloads.wordpress.org/plugin/vipers-video-quicktags.6.4.4.zip";}}}', 'yes'),
(107, '_transient_random_seed', '0deba9f27f5e55fbbbfa3772847167ae', 'yes'),
(108, 'auth_salt', 'Y,z|6YO7Z+GV7C$-DD}gCu |7/IV3-gX*(dO9;DF}u$>FC&MI(IDBe |w#Vd?/K4', 'yes'),
(109, 'logged_in_salt', '[fYsPk&Ugm@;bc[&)Vg Zl#z!5}/<-k$=$xjJ`/uzzf-5KKX~Uxxaa0.MzlV*phA', 'yes'),
(110, 'widget_pages', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(111, 'widget_calendar', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(112, 'widget_links', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(113, 'widget_tag_cloud', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(114, 'widget_nav_menu', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(117, '_site_transient_update_themes', 'O:8:"stdClass":3:{s:12:"last_checked";i:1355993077;s:7:"checked";a:28:{s:13:"almost-spring";s:3:"1.0";s:7:"anarchy";s:3:"1.1";s:9:"andreas09";s:3:"2.1";s:11:"big-blue-01";s:3:"0.1";s:7:"classic";s:3:"1.5";s:7:"default";s:5:"1.7.2";s:10:"digg-3-col";s:5:"1.0.1";s:4:"flex";s:3:"1.0";s:9:"freshy-10";s:3:"1.0";s:7:"freshy2";s:5:"2.1.2";s:11:"gentle_calm";s:3:"1.0";s:14:"glossyblue-1-2";s:3:"1.2";s:4:"home";s:3:"1.0";s:12:"home_hipolit";s:3:"1.0";s:8:"light-10";s:3:"1.0";s:7:"mandigo";s:5:"1.7.1";s:17:"manycolorsidea-10";s:3:"2.1";s:10:"newsportal";s:3:"1.0";s:14:"quadruple-blue";s:3:"1.0";s:6:"simpla";s:4:"1.01";s:12:"stardust-v10";s:3:"2.7";s:5:"steam";s:3:"1.5";s:14:"tranquility-10";s:3:"1.2";s:9:"twentyten";s:3:"1.2";s:11:"whiteasmilk";s:3:"1.8";s:9:"xtec-v1.1";s:3:"1.1";s:4:"xtec";s:3:"1.0";s:14:"xtec898_encurs";s:4:"v2.0";}s:8:"response";a:1:{s:9:"twentyten";a:3:{s:11:"new_version";s:3:"1.5";s:3:"url";s:44:"http://wordpress.org/extend/themes/twentyten";s:7:"package";s:61:"http://wordpress.org/extend/themes/download/twentyten.1.5.zip";}}}', 'yes'),
(118, 'dashboard_widget_options', 'a:4:{s:25:"dashboard_recent_comments";a:1:{s:5:"items";i:5;}s:24:"dashboard_incoming_links";a:5:{s:4:"home";s:18:"http://agora/blocs";s:4:"link";s:94:"http://blogsearch.google.com/blogsearch?scoring=d&partner=wordpress&q=link:http://agora/blocs/";s:3:"url";s:127:"http://blogsearch.google.com/blogsearch_feeds?scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:http://agora/blocs/";s:5:"items";i:10;s:9:"show_date";b:0;}s:17:"dashboard_primary";a:7:{s:4:"link";s:26:"http://wordpress.org/news/";s:3:"url";s:31:"http://wordpress.org/news/feed/";s:5:"title";s:18:"Bloc del WordPress";s:5:"items";i:2;s:12:"show_summary";i:1;s:11:"show_author";i:0;s:9:"show_date";i:1;}s:19:"dashboard_secondary";a:7:{s:4:"link";s:28:"http://planet.wordpress.org/";s:3:"url";s:33:"http://planet.wordpress.org/feed/";s:5:"title";s:30:"Altres notícies del WordPress";s:5:"items";i:5;s:12:"show_summary";i:0;s:11:"show_author";i:0;s:9:"show_date";i:0;}}', 'yes'),
(119, 'nonce_salt', '6faUl0psNyW2>W _ygM;=QqfW@2O0LK[%1X ?bPR@<HMl;He4eAFFEw/:MO.6#_s', 'yes'),
(183, 'current_theme', 'XTECBlocs Default', 'yes'),
(178, 'recently_activated', 'a:1:{s:42:"wordpress-social-login/wp-social-login.php";i:1489495650;}', 'yes'),
(1062, '_transient_timeout_plugin_slugs', '1489582203', 'no'),
(1063, '_transient_plugin_slugs', 'a:34:{i:0;s:25:"add-to-any/add-to-any.php";i:1;s:23:"anti-spam/anti-spam.php";i:2;s:37:"blogger-importer/blogger-importer.php";i:3;s:43:"google-analyticator/google-analyticator.php";i:4;s:29:"link-manager/link-manager.php";i:5;s:57:"multisite-clone-duplicator/multisite-clone-duplicator.php";i:6;s:43:"multisite-plugin-manager/plugin-manager.php";i:7;s:34:"scribd-doc-embedder/scribd_doc.php";i:8;s:49:"google-calendar-events/google-calendar-events.php";i:9;s:45:"simple-local-avatars/simple-local-avatars.php";i:10;s:32:"simpler-ipaper/scribd-ipaper.php";i:11;s:25:"slideshare/slideshare.php";i:12;s:44:"slideshow-jquery-image-gallery/slideshow.php";i:13;s:37:"tinymce-advanced/tinymce-advanced.php";i:14;s:49:"vipers-video-quicktags/vipers-video-quicktags.php";i:15;s:41:"wordpress-importer/wordpress-importer.php";i:16;s:41:"wordpress-php-info/wordpress-php-info.php";i:17;s:42:"wordpress-social-login/wp-social-login.php";i:18;s:29:"wp-recaptcha/wp-recaptcha.php";i:19;s:27:"wp-super-cache/wp-cache.php";i:20;s:21:"xtec-api/xtec-api.php";i:21;s:37:"xtec-descriptors/xtec-descriptors.php";i:22;s:33:"xtec-favorites/xtec-favorites.php";i:23;s:41:"xtec-lastest-posts/xtec-lastest-posts.php";i:24;s:35:"xtec-ldap-login/xtec-ldap-login.php";i:25;s:37:"xtec-link-player/xtec-link-player.php";i:26;s:23:"xtec-mail/xtec-mail.php";i:27;s:37:"xtec-maintenance/xtec-maintenance.php";i:28;s:35:"xtec-ms-manager/xtec-ms-manager.php";i:29;s:31:"xtec-settings/xtec-settings.php";i:30;s:27:"xtec-signup/xtec-signup.php";i:31;s:25:"xtec-users/xtec-users.php";i:32;s:33:"xtec-weekblog2/xtec-weekblog2.php";i:33;s:47:"xtec-widget-data-users/xtec-class-data-user.php";}', 'no'),
(148, 'fileupload_url', 'http://agora/blocs/wp-content/uploads', 'yes'),
(427, 'ossdl_https', '1', 'yes'),
(625, 'rewrite_rules', 'a:167:{s:11:"^wp-json/?$";s:22:"index.php?rest_route=/";s:14:"^wp-json/(.*)?";s:33:"index.php?rest_route=/$matches[1]";s:17:"blog/slideshow/?$";s:29:"index.php?post_type=slideshow";s:47:"blog/slideshow/feed/(feed|rdf|rss|rss2|atom)/?$";s:46:"index.php?post_type=slideshow&feed=$matches[1]";s:42:"blog/slideshow/(feed|rdf|rss|rss2|atom)/?$";s:46:"index.php?post_type=slideshow&feed=$matches[1]";s:34:"blog/slideshow/page/([0-9]{1,})/?$";s:47:"index.php?post_type=slideshow&paged=$matches[1]";s:20:"blog/xtecweekblog/?$";s:32:"index.php?post_type=xtecweekblog";s:50:"blog/xtecweekblog/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?post_type=xtecweekblog&feed=$matches[1]";s:45:"blog/xtecweekblog/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?post_type=xtecweekblog&feed=$matches[1]";s:37:"blog/xtecweekblog/page/([0-9]{1,})/?$";s:50:"index.php?post_type=xtecweekblog&paged=$matches[1]";s:52:"blog/category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:47:"blog/category/(.+?)/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:28:"blog/category/(.+?)/embed/?$";s:46:"index.php?category_name=$matches[1]&embed=true";s:40:"blog/category/(.+?)/page/?([0-9]{1,})/?$";s:53:"index.php?category_name=$matches[1]&paged=$matches[2]";s:22:"blog/category/(.+?)/?$";s:35:"index.php?category_name=$matches[1]";s:49:"blog/tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:44:"blog/tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:25:"blog/tag/([^/]+)/embed/?$";s:36:"index.php?tag=$matches[1]&embed=true";s:37:"blog/tag/([^/]+)/page/?([0-9]{1,})/?$";s:43:"index.php?tag=$matches[1]&paged=$matches[2]";s:19:"blog/tag/([^/]+)/?$";s:25:"index.php?tag=$matches[1]";s:50:"blog/type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:45:"blog/type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:26:"blog/type/([^/]+)/embed/?$";s:44:"index.php?post_format=$matches[1]&embed=true";s:38:"blog/type/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?post_format=$matches[1]&paged=$matches[2]";s:20:"blog/type/([^/]+)/?$";s:33:"index.php?post_format=$matches[1]";s:59:"blog/calendar_feed/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?calendar_feed=$matches[1]&feed=$matches[2]";s:54:"blog/calendar_feed/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?calendar_feed=$matches[1]&feed=$matches[2]";s:35:"blog/calendar_feed/([^/]+)/embed/?$";s:46:"index.php?calendar_feed=$matches[1]&embed=true";s:47:"blog/calendar_feed/([^/]+)/page/?([0-9]{1,})/?$";s:53:"index.php?calendar_feed=$matches[1]&paged=$matches[2]";s:29:"blog/calendar_feed/([^/]+)/?$";s:35:"index.php?calendar_feed=$matches[1]";s:59:"blog/calendar_type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?calendar_type=$matches[1]&feed=$matches[2]";s:54:"blog/calendar_type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?calendar_type=$matches[1]&feed=$matches[2]";s:35:"blog/calendar_type/([^/]+)/embed/?$";s:46:"index.php?calendar_type=$matches[1]&embed=true";s:47:"blog/calendar_type/([^/]+)/page/?([0-9]{1,})/?$";s:53:"index.php?calendar_type=$matches[1]&paged=$matches[2]";s:29:"blog/calendar_type/([^/]+)/?$";s:35:"index.php?calendar_type=$matches[1]";s:63:"blog/calendar_category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:56:"index.php?calendar_category=$matches[1]&feed=$matches[2]";s:58:"blog/calendar_category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:56:"index.php?calendar_category=$matches[1]&feed=$matches[2]";s:39:"blog/calendar_category/([^/]+)/embed/?$";s:50:"index.php?calendar_category=$matches[1]&embed=true";s:51:"blog/calendar_category/([^/]+)/page/?([0-9]{1,})/?$";s:57:"index.php?calendar_category=$matches[1]&paged=$matches[2]";s:33:"blog/calendar_category/([^/]+)/?$";s:39:"index.php?calendar_category=$matches[1]";s:36:"calendar/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:46:"calendar/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:66:"calendar/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:61:"calendar/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:61:"calendar/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:42:"calendar/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:25:"calendar/([^/]+)/embed/?$";s:41:"index.php?calendar=$matches[1]&embed=true";s:29:"calendar/([^/]+)/trackback/?$";s:35:"index.php?calendar=$matches[1]&tb=1";s:37:"calendar/([^/]+)/page/?([0-9]{1,})/?$";s:48:"index.php?calendar=$matches[1]&paged=$matches[2]";s:44:"calendar/([^/]+)/comment-page-([0-9]{1,})/?$";s:48:"index.php?calendar=$matches[1]&cpage=$matches[2]";s:33:"calendar/([^/]+)(?:/([0-9]+))?/?$";s:47:"index.php?calendar=$matches[1]&page=$matches[2]";s:25:"calendar/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:35:"calendar/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:55:"calendar/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:50:"calendar/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:50:"calendar/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:31:"calendar/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:42:"blog/slideshow/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:52:"blog/slideshow/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:72:"blog/slideshow/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:67:"blog/slideshow/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:67:"blog/slideshow/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:48:"blog/slideshow/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:31:"blog/slideshow/([^/]+)/embed/?$";s:42:"index.php?slideshow=$matches[1]&embed=true";s:35:"blog/slideshow/([^/]+)/trackback/?$";s:36:"index.php?slideshow=$matches[1]&tb=1";s:55:"blog/slideshow/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:48:"index.php?slideshow=$matches[1]&feed=$matches[2]";s:50:"blog/slideshow/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:48:"index.php?slideshow=$matches[1]&feed=$matches[2]";s:43:"blog/slideshow/([^/]+)/page/?([0-9]{1,})/?$";s:49:"index.php?slideshow=$matches[1]&paged=$matches[2]";s:50:"blog/slideshow/([^/]+)/comment-page-([0-9]{1,})/?$";s:49:"index.php?slideshow=$matches[1]&cpage=$matches[2]";s:39:"blog/slideshow/([^/]+)(?:/([0-9]+))?/?$";s:48:"index.php?slideshow=$matches[1]&page=$matches[2]";s:31:"blog/slideshow/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:41:"blog/slideshow/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:61:"blog/slideshow/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:56:"blog/slideshow/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:56:"blog/slideshow/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:37:"blog/slideshow/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:45:"blog/xtecweekblog/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:55:"blog/xtecweekblog/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:75:"blog/xtecweekblog/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:70:"blog/xtecweekblog/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:70:"blog/xtecweekblog/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:51:"blog/xtecweekblog/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:34:"blog/xtecweekblog/([^/]+)/embed/?$";s:45:"index.php?xtecweekblog=$matches[1]&embed=true";s:38:"blog/xtecweekblog/([^/]+)/trackback/?$";s:39:"index.php?xtecweekblog=$matches[1]&tb=1";s:58:"blog/xtecweekblog/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:51:"index.php?xtecweekblog=$matches[1]&feed=$matches[2]";s:53:"blog/xtecweekblog/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:51:"index.php?xtecweekblog=$matches[1]&feed=$matches[2]";s:46:"blog/xtecweekblog/([^/]+)/page/?([0-9]{1,})/?$";s:52:"index.php?xtecweekblog=$matches[1]&paged=$matches[2]";s:53:"blog/xtecweekblog/([^/]+)/comment-page-([0-9]{1,})/?$";s:52:"index.php?xtecweekblog=$matches[1]&cpage=$matches[2]";s:42:"blog/xtecweekblog/([^/]+)(?:/([0-9]+))?/?$";s:51:"index.php?xtecweekblog=$matches[1]&page=$matches[2]";s:34:"blog/xtecweekblog/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:44:"blog/xtecweekblog/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:64:"blog/xtecweekblog/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:59:"blog/xtecweekblog/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:59:"blog/xtecweekblog/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:40:"blog/xtecweekblog/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:48:".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$";s:18:"index.php?feed=old";s:20:".*wp-app\\.php(/.*)?$";s:19:"index.php?error=403";s:16:".*wp-signup.php$";s:21:"index.php?signup=true";s:18:".*wp-activate.php$";s:23:"index.php?activate=true";s:18:".*wp-register.php$";s:23:"index.php?register=true";s:32:"feed/(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:27:"(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:8:"embed/?$";s:21:"index.php?&embed=true";s:20:"page/?([0-9]{1,})/?$";s:28:"index.php?&paged=$matches[1]";s:41:"comments/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:36:"comments/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:17:"comments/embed/?$";s:21:"index.php?&embed=true";s:44:"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:39:"search/(.+)/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:20:"search/(.+)/embed/?$";s:34:"index.php?s=$matches[1]&embed=true";s:32:"search/(.+)/page/?([0-9]{1,})/?$";s:41:"index.php?s=$matches[1]&paged=$matches[2]";s:14:"search/(.+)/?$";s:23:"index.php?s=$matches[1]";s:52:"blog/author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:47:"blog/author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:28:"blog/author/([^/]+)/embed/?$";s:44:"index.php?author_name=$matches[1]&embed=true";s:40:"blog/author/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?author_name=$matches[1]&paged=$matches[2]";s:22:"blog/author/([^/]+)/?$";s:33:"index.php?author_name=$matches[1]";s:74:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:69:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:50:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$";s:74:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true";s:62:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:81:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]";s:44:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$";s:63:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]";s:61:"blog/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:56:"blog/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:37:"blog/([0-9]{4})/([0-9]{1,2})/embed/?$";s:58:"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true";s:49:"blog/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:65:"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]";s:31:"blog/([0-9]{4})/([0-9]{1,2})/?$";s:47:"index.php?year=$matches[1]&monthnum=$matches[2]";s:48:"blog/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:43:"blog/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:24:"blog/([0-9]{4})/embed/?$";s:37:"index.php?year=$matches[1]&embed=true";s:36:"blog/([0-9]{4})/page/?([0-9]{1,})/?$";s:44:"index.php?year=$matches[1]&paged=$matches[2]";s:18:"blog/([0-9]{4})/?$";s:26:"index.php?year=$matches[1]";s:63:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:73:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:93:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:88:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:88:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:69:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:58:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$";s:91:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true";s:62:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$";s:85:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1";s:82:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]";s:77:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]";s:70:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$";s:98:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]";s:77:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$";s:98:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]";s:66:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]";s:52:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:62:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:82:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:77:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:77:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:58:"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:69:"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$";s:81:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]";s:56:"blog/([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$";s:65:"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]";s:43:"blog/([0-9]{4})/comment-page-([0-9]{1,})/?$";s:44:"index.php?year=$matches[1]&cpage=$matches[2]";s:27:".?.+?/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:37:".?.+?/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:57:".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:33:".?.+?/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:16:"(.?.+?)/embed/?$";s:41:"index.php?pagename=$matches[1]&embed=true";s:20:"(.?.+?)/trackback/?$";s:35:"index.php?pagename=$matches[1]&tb=1";s:40:"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:35:"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:28:"(.?.+?)/page/?([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&paged=$matches[2]";s:35:"(.?.+?)/comment-page-([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&cpage=$matches[2]";s:24:"(.?.+?)(?:/([0-9]+))?/?$";s:47:"index.php?pagename=$matches[1]&page=$matches[2]";}', 'yes'),
(181, 'wpsupercache_gc_time', '1489495449', 'yes'),
(182, 'allowedthemes', 'a:1:{s:4:"home";b:1;}', 'yes'),
(184, 'theme_mods_home', 'a:2:{i:0;b:0;s:16:"sidebars_widgets";a:2:{s:4:"time";i:1389360121;s:4:"data";a:1:{s:19:"wp_inactive_widgets";a:13:{i:0;s:7:"pages-2";i:1;s:10:"calendar-2";i:2;s:7:"links-2";i:3;s:6:"text-2";i:4;s:5:"rss-2";i:5;s:11:"tag_cloud-2";i:6;s:10:"nav_menu-2";i:7;s:8:"search-2";i:8;s:14:"recent-posts-2";i:9;s:17:"recent-comments-2";i:10;s:10:"archives-2";i:11;s:12:"categories-2";i:12;s:6:"meta-2";}}}}', 'yes'),
(186, '$xtec_favorites_db_version', '1.0', 'yes'),
(187, 'ga_status', 'disabled', 'yes'),
(188, 'ga_uid', 'XX-XXXXX-X', 'yes'),
(189, 'ga_admin_status', 'enabled', 'yes'),
(190, 'ga_admin_disable', 'remove', 'yes'),
(191, 'ga_admin_role', 'a:1:{i:0;s:13:"administrator";}', 'yes'),
(192, 'ga_dashboard_role', 'a:1:{i:0;s:13:"administrator";}', 'yes'),
(193, 'ga_adsense', '', 'yes'),
(194, 'ga_extra', '', 'yes'),
(195, 'ga_extra_after', '', 'yes'),
(196, 'ga_event', 'enabled', 'yes'),
(197, 'ga_outbound', 'enabled', 'yes'),
(198, 'ga_outbound_prefix', 'outgoing', 'yes'),
(199, 'ga_downloads', '', 'yes'),
(200, 'ga_downloads_prefix', 'download', 'yes'),
(201, 'ga_profileid', '', 'yes'),
(202, 'ga_widgets', 'enabled', 'yes'),
(203, 'ga_google_token', '', 'yes'),
(204, 'ga_compatibility', 'off', 'yes'),
(205, 'widget_googlestats', 'a:2:{i:2;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(206, 'vvq_options', 'a:2:{i:0;b:0;s:7:"version";s:5:"6.3.0";}', 'yes'),
(207, 'ossdl_off_cdn_url', 'http://agora/blocs', 'yes'),
(208, 'ossdl_off_include_dirs', 'wp-content,wp-includes', 'yes'),
(209, 'ossdl_off_exclude', '.php', 'yes'),
(210, 'ossdl_cname', '', 'yes'),
(262, 'uninstall_plugins', 'a:4:{i:0;b:0;s:27:"wp-super-cache/wp-cache.php";s:22:"wpsupercache_uninstall";s:57:"multisite-clone-duplicator/multisite-clone-duplicator.php";a:2:{i:0;s:4:"MUCD";i:1;s:9:"uninstall";}s:45:"simple-local-avatars/simple-local-avatars.php";s:30:"simple_local_avatars_uninstall";}', 'no'),
(212, '$xtec_maintenance_db_version', '1.0', 'yes'),
(213, '$xtec_sea_db_version', '', 'yes'),
(303, 'tadv_settings', 'a:6:{s:7:"options";s:15:"menubar,advlist";s:9:"toolbar_1";s:117:"bold,italic,blockquote,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,table,fullscreen,undo,redo,wp_adv";s:9:"toolbar_2";s:121:"formatselect,alignjustify,strikethrough,outdent,indent,pastetext,removeformat,charmap,wp_more,emoticons,forecolor,wp_help";s:9:"toolbar_3";s:0:"";s:9:"toolbar_4";s:0:"";s:7:"plugins";s:107:"anchor,code,insertdatetime,nonbreaking,print,searchreplace,table,visualblocks,visualchars,emoticons,advlist";}', 'yes'),
(304, 'tadv_admin_settings', 'a:1:{s:7:"options";a:0:{}}', 'yes'),
(1058, '_transient_timeout_feed_b9388c83948825c1edaef0d856b7b109', '1489537719', 'no');
INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1059, '_transient_feed_b9388c83948825c1edaef0d856b7b109', 'a:4:{s:5:"child";a:1:{s:0:"";a:1:{s:3:"rss";a:1:{i:0;a:6:{s:4:"data";s:3:"\n	\n";s:7:"attribs";a:1:{s:0:"";a:1:{s:7:"version";s:3:"2.0";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:1:{s:0:"";a:1:{s:7:"channel";a:1:{i:0;a:6:{s:4:"data";s:117:"\n		\n		\n		\n		\n		\n		\n				\n\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n		\n\n	";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:7:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:34:"WordPress Plugins » View: Popular";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:45:"https://wordpress.org/plugins/browse/popular/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:34:"WordPress Plugins » View: Popular";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:8:"language";a:1:{i:0;a:5:{s:4:"data";s:5:"en-US";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Tue, 14 Mar 2017 12:16:22 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:9:"generator";a:1:{i:0;a:5:{s:4:"data";s:25:"http://bbpress.org/?v=1.1";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"item";a:30:{i:0;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:35:"UpdraftPlus WordPress Backup Plugin";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:53:"https://wordpress.org/plugins/updraftplus/#post-38058";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 21 May 2012 15:14:11 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"38058@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:148:"Backup and restoration made easy. Complete backups; manual or scheduled (backup to S3, Dropbox, Google Drive, Rackspace, FTP, SFTP, email + others).";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:14:"David Anderson";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:1;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:14:"Duplicate Post";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:55:"https://wordpress.org/plugins/duplicate-post/#post-2646";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Wed, 05 Dec 2007 17:40:03 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"2646@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:22:"Clone posts and pages.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:4:"Lopo";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:2;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:21:"Regenerate Thumbnails";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:62:"https://wordpress.org/plugins/regenerate-thumbnails/#post-6743";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Sat, 23 Aug 2008 14:38:58 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"6743@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:76:"Allows you to regenerate your thumbnails after changing the thumbnail sizes.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:25:"Alex Mills (Viper007Bond)";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:3;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:14:"WP Super Cache";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:55:"https://wordpress.org/plugins/wp-super-cache/#post-2572";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 05 Nov 2007 11:40:04 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"2572@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:73:"A very fast caching engine for WordPress that produces static html files.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:16:"Donncha O Caoimh";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:4;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:11:"WooCommerce";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:53:"https://wordpress.org/plugins/woocommerce/#post-29860";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 05 Sep 2011 08:13:36 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"29860@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:97:"WooCommerce is a powerful, extendable eCommerce plugin that helps you sell anything. Beautifully.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:9:"WooThemes";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:5;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:49:"Google Analytics for WordPress by MonsterInsights";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:71:"https://wordpress.org/plugins/google-analytics-for-wordpress/#post-2316";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 14 Sep 2007 12:15:27 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"2316@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:125:"The best Google Analytics plugin for WordPress. See how visitors find and use your website, so you can keep them coming back.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:11:"Syed Balkhi";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:6;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:18:"Wordfence Security";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:51:"https://wordpress.org/plugins/wordfence/#post-29832";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Sun, 04 Sep 2011 03:13:51 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"29832@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:149:"Secure your website with the most comprehensive WordPress security plugin. Firewall, malware scan, blocking, live traffic, login security &#38; more.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:9:"Wordfence";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:7;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:42:"NextGEN Gallery - WordPress Gallery Plugin";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:56:"https://wordpress.org/plugins/nextgen-gallery/#post-1169";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 23 Apr 2007 20:08:06 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"1169@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:123:"The most popular WordPress gallery plugin and one of the most popular plugins of all time with over 16.5 million downloads.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:9:"Alex Rabe";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:8;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:26:"Page Builder by SiteOrigin";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:59:"https://wordpress.org/plugins/siteorigin-panels/#post-51888";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 11 Apr 2013 10:36:42 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"51888@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:111:"Build responsive page layouts using the widgets you know and love using this simple drag and drop page builder.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:11:"Greg Priday";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:9;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:9:"Yoast SEO";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:54:"https://wordpress.org/plugins/wordpress-seo/#post-8321";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 01 Jan 2009 20:34:44 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"8321@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:118:"Improve your WordPress SEO: Write better content and have a fully optimized WordPress site using the Yoast SEO plugin.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:13:"Joost de Valk";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:10;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:7:"Akismet";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:46:"https://wordpress.org/plugins/akismet/#post-15";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 09 Mar 2007 22:11:30 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:32:"15@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:146:"Akismet checks your comments and contact form submissions against our global database of spam to protect you and your site from malicious content.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:14:"Matt Mullenweg";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:11;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:19:"Google XML Sitemaps";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:64:"https://wordpress.org/plugins/google-sitemap-generator/#post-132";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 09 Mar 2007 22:31:32 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:33:"132@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:105:"This plugin will generate a special XML sitemap which will help search engines to better index your blog.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:14:"Arne Brachhold";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:12;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:11:"WP-PageNavi";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:51:"https://wordpress.org/plugins/wp-pagenavi/#post-363";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 09 Mar 2007 23:17:57 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:33:"363@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:49:"Adds a more advanced paging navigation interface.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:11:"Lester Chan";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:13;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:19:"All in One SEO Pack";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:59:"https://wordpress.org/plugins/all-in-one-seo-pack/#post-753";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 30 Mar 2007 20:08:18 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:33:"753@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:83:"The original SEO plugin for WordPress, downloaded over 30,000,000 times since 2007.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:8:"uberdose";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:14;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:21:"Really Simple CAPTCHA";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:62:"https://wordpress.org/plugins/really-simple-captcha/#post-9542";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 09 Mar 2009 02:17:35 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"9542@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:138:"Really Simple CAPTCHA is a CAPTCHA module intended to be called from other plugins. It is originally created for my Contact Form 7 plugin.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:16:"Takayuki Miyoshi";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:15;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:24:"Jetpack by WordPress.com";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:49:"https://wordpress.org/plugins/jetpack/#post-23862";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 20 Jan 2011 02:21:38 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"23862@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:148:"The one plugin you need for stats, related posts, search engine optimization, social sharing, protection, backups, speed, and email list management.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:10:"Automattic";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:16;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:16:"TinyMCE Advanced";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:57:"https://wordpress.org/plugins/tinymce-advanced/#post-2082";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Wed, 27 Jun 2007 15:00:26 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"2082@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:58:"Extends and enhances TinyMCE, the WordPress Visual Editor.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:10:"Andrew Ozz";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:17;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:11:"Hello Dolly";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:52:"https://wordpress.org/plugins/hello-dolly/#post-5790";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 29 May 2008 22:11:34 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"5790@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:150:"This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:14:"Matt Mullenweg";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:18;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:18:"WordPress Importer";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:60:"https://wordpress.org/plugins/wordpress-importer/#post-18101";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 20 May 2010 17:42:45 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"18101@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:101:"Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:14:"Brian Colinger";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:19;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:22:"Advanced Custom Fields";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:64:"https://wordpress.org/plugins/advanced-custom-fields/#post-25254";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 17 Mar 2011 04:07:30 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"25254@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:68:"Customise WordPress with powerful, professional and intuitive fields";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:12:"elliotcondon";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:20;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:14:"Contact Form 7";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:55:"https://wordpress.org/plugins/contact-form-7/#post-2141";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 02 Aug 2007 12:45:03 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"2141@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:54:"Just another contact form plugin. Simple but flexible.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:16:"Takayuki Miyoshi";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:21;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:14:"W3 Total Cache";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:56:"https://wordpress.org/plugins/w3-total-cache/#post-12073";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Wed, 29 Jul 2009 18:46:31 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"12073@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:144:"Search Engine (SEO) &#38; Performance Optimization (WPO) via caching. Integrated caching: CDN, Minify, Page, Object, Fragment, Database support.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:16:"Frederick Townes";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:22;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:33:"Google Analytics Dashboard for WP";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:75:"https://wordpress.org/plugins/google-analytics-dashboard-for-wp/#post-50539";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Sun, 10 Mar 2013 17:07:11 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"50539@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:125:"Displays Google Analytics stats in your WordPress Dashboard. Inserts the latest Google Analytics tracking code in your pages.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:10:"Alin Marcu";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:23;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:10:"Duplicator";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:52:"https://wordpress.org/plugins/duplicator/#post-26607";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 16 May 2011 12:15:41 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"26607@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:88:"Duplicate, clone, backup, move and transfer an entire site from one location to another.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:10:"Cory Lamle";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:24;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:16:"Disable Comments";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:58:"https://wordpress.org/plugins/disable-comments/#post-26907";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 27 May 2011 04:42:58 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"26907@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:150:"Allows administrators to globally disable comments on their site. Comments can be disabled according to post type. Multisite friendly. Provides tool t";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:10:"Samir Shah";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:25;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:18:"WP Multibyte Patch";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:60:"https://wordpress.org/plugins/wp-multibyte-patch/#post-28395";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 14 Jul 2011 12:22:53 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"28395@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:71:"Multibyte functionality enhancement for the WordPress Japanese package.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:13:"plugin-master";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:26;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:27:"Black Studio TinyMCE Widget";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:69:"https://wordpress.org/plugins/black-studio-tinymce-widget/#post-31973";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 10 Nov 2011 15:06:14 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"31973@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:39:"The visual editor widget for Wordpress.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:12:"Marco Chiesi";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:27;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:25:"SiteOrigin Widgets Bundle";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:59:"https://wordpress.org/plugins/so-widgets-bundle/#post-67824";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Sat, 24 May 2014 14:27:05 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"67824@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:128:"A collection of all widgets, neatly bundled into a single plugin. It&#039;s also a framework to code your own widgets on top of.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:11:"Greg Priday";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:28;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:46:"iThemes Security (formerly Better WP Security)";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:60:"https://wordpress.org/plugins/better-wp-security/#post-21738";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 22 Oct 2010 22:06:05 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"21738@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:146:"Take the guesswork out of WordPress security. iThemes Security offers 30+ ways to lock down WordPress in an easy-to-use WordPress security plugin.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:7:"iThemes";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:29;a:6:{s:4:"data";s:30:"\n			\n			\n			\n			\n			\n			\n					";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:11:"Ninja Forms";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:53:"https://wordpress.org/plugins/ninja-forms/#post-33147";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Tue, 20 Dec 2011 18:11:48 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:35:"33147@http://wordpress.org/plugins/";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:147:"Drag and drop fields in an intuitive UI to create create contact forms, email subscription forms, order forms, payment forms, send emails and more!";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:12:"Kevin Stover";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}}}s:27:"http://www.w3.org/2005/Atom";a:1:{s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:0:"";s:7:"attribs";a:1:{s:0:"";a:3:{s:4:"href";s:46:"https://wordpress.org/plugins/rss/view/popular";s:3:"rel";s:4:"self";s:4:"type";s:19:"application/rss+xml";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}}}}}}}}s:4:"type";i:128;s:7:"headers";a:9:{s:6:"server";s:5:"nginx";s:4:"date";s:29:"Tue, 14 Mar 2017 12:28:39 GMT";s:12:"content-type";s:23:"text/xml; charset=UTF-8";s:10:"connection";s:5:"close";s:4:"vary";s:15:"Accept-Encoding";s:25:"strict-transport-security";s:11:"max-age=360";s:13:"last-modified";s:29:"Mon, 21 May 2012 15:14:11 GMT";s:15:"x-frame-options";s:10:"SAMEORIGIN";s:4:"x-nc";s:11:"HIT lax 249";}s:5:"build";s:14:"20170310133323";}', 'no'),
(732, 'avatar_default_wp_user_avatar', '', 'yes'),
(733, 'wp_user_avatar_allow_upload', '0', 'yes'),
(734, 'wp_user_avatar_disable_gravatar', '0', 'yes'),
(735, 'wp_user_avatar_edit_avatar', '1', 'yes'),
(736, 'wp_user_avatar_resize_crop', '0', 'yes'),
(737, 'wp_user_avatar_resize_h', '96', 'yes'),
(738, 'wp_user_avatar_resize_upload', '0', 'yes'),
(739, 'wp_user_avatar_resize_w', '96', 'yes'),
(740, 'wp_user_avatar_tinymce', '1', 'yes'),
(741, 'wp_user_avatar_upload_size_limit', '0', 'yes'),
(742, 'wp_user_avatar_default_avatar_updated', '1', 'yes'),
(743, 'wp_user_avatar_users_updated', '1', 'yes'),
(744, 'wp_user_avatar_media_updated', '1', 'yes'),
(902, 'recaptcha_options', 'a:5:{s:8:"site_key";s:40:"6LdeRAUTAAAAAElOIZz-mWS21zDs6pe43Uhg4Btg";s:6:"secret";s:40:"6LdeRAUTAAAAADdO3-Odt7C097AzBOMHGO1I6zeL";s:14:"comments_theme";s:8:"standard";s:18:"recaptcha_language";s:2:"ca";s:17:"no_response_error";s:58:"<strong>ERROR</strong>: Please fill in the reCAPTCHA form.";}', 'yes'),
(1050, '_transient_timeout_feed_ac0b00fe65abe10e0c5b588f3ed8c7ca', '1489537715', 'no');
INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1051, '_transient_feed_ac0b00fe65abe10e0c5b588f3ed8c7ca', 'a:4:{s:5:"child";a:1:{s:0:"";a:1:{s:3:"rss";a:1:{i:0;a:6:{s:4:"data";s:3:"\n\n\n";s:7:"attribs";a:1:{s:0:"";a:1:{s:7:"version";s:3:"2.0";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:1:{s:0:"";a:1:{s:7:"channel";a:1:{i:0;a:6:{s:4:"data";s:49:"\n	\n	\n	\n	\n	\n	\n	\n	\n	\n	\n		\n		\n		\n		\n		\n		\n		\n		\n		\n	";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:4:{s:0:"";a:7:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:14:"WordPress News";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:26:"https://wordpress.org/news";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:14:"WordPress News";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:13:"lastBuildDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Fri, 10 Mar 2017 18:14:55 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:8:"language";a:1:{i:0;a:5:{s:4:"data";s:5:"en-US";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:9:"generator";a:1:{i:0;a:5:{s:4:"data";s:40:"https://wordpress.org/?v=4.8-alpha-40284";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"item";a:10:{i:0;a:6:{s:4:"data";s:39:"\n		\n		\n		\n		\n				\n		\n		\n\n		\n		\n				\n			";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:4:{s:0:"";a:6:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:48:"WordPress 4.7.3 Security and Maintenance Release";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:84:"https://wordpress.org/news/2017/03/wordpress-4-7-3-security-and-maintenance-release/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 06 Mar 2017 17:53:30 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:8:"category";a:3:{i:0;a:5:{s:4:"data";s:8:"Releases";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:1;a:5:{s:4:"data";s:8:"Security";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:2;a:5:{s:4:"data";s:3:"4.7";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"https://wordpress.org/news/?p=4696";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:396:"WordPress 4.7.3 is now available. This is a security release for all previous versions and we strongly encourage you to update your sites immediately. WordPress versions 4.7.2 and earlier are affected by six security issues: Cross-site scripting (XSS) via media file metadata.  Reported by Chris Andrè Dale, Yorick Koster, and Simon P. Briggs. Control characters can trick redirect [&#8230;]";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:11:"James Nylen";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:40:"http://purl.org/rss/1.0/modules/content/";a:1:{s:7:"encoded";a:1:{i:0;a:5:{s:4:"data";s:6191:"<p>WordPress 4.7.3 is now available. This is a <strong>security release</strong> for all previous versions and we strongly encourage you to update your sites immediately.</p>\n<p>WordPress versions 4.7.2 and earlier are affected by six security issues:</p>\n<ol>\n<li>Cross-site scripting (XSS) via media file metadata.  Reported by <a href="https://www.securesolutions.no/">Chris Andrè Dale</a>, <a href="https://twitter.com/yorickkoster">Yorick Koster</a>, and Simon P. Briggs.</li>\n<li>Control characters can trick redirect URL validation.  Reported by <a href="http://www.danielchatfield.com/">Daniel Chatfield</a>.</li>\n<li>Unintended files can be deleted by administrators using the plugin deletion functionality.  Reported by <a href="https://hackerone.com/triginc">TrigInc</a> and <a href="http://b.360.cn/">xuliang</a>.</li>\n<li>Cross-site scripting (XSS) via video URL in YouTube embeds.  Reported by <a href="https://twitter.com/marcs0h">Marc Montpas</a>.</li>\n<li>Cross-site scripting (XSS) via taxonomy term names.  Reported by <a href="https://profiles.wordpress.org/deltamgm2">Delta</a>.</li>\n<li>Cross-site request forgery (CSRF) in Press This leading to excessive use of server resources.  Reported by Sipke Mellema.</li>\n</ol>\n<p>Thank you to the reporters for practicing <a href="https://make.wordpress.org/core/handbook/testing/reporting-security-vulnerabilities/">responsible disclosure</a>.</p>\n<p>In addition to the security issues above, WordPress 4.7.3 contains 39 maintenance fixes to the 4.7 release series. For more information, see the <a href="https://codex.wordpress.org/Version_4.7.3">release notes</a> or consult the <a href="https://core.trac.wordpress.org/query?status=closed&amp;milestone=4.7.3&amp;group=component&amp;col=id&amp;col=summary&amp;col=component&amp;col=status&amp;col=owner&amp;col=type&amp;col=priority&amp;col=keywords&amp;order=priority">list of changes</a>.</p>\n<p><a href="https://wordpress.org/download/">Download WordPress 4.7.3</a> or venture over to Dashboard → Updates and simply click “Update Now.” Sites that support automatic background updates are already beginning to update to WordPress 4.7.3.</p>\n<p>Thanks to everyone who contributed to 4.7.3: <a href="https://profiles.wordpress.org/aaroncampbell/">Aaron D. Campbell</a>, <a href="https://profiles.wordpress.org/adamsilverstein/">Adam Silverstein</a>, <a href="https://profiles.wordpress.org/xknown/">Alex Concha</a>, <a href="https://profiles.wordpress.org/afercia/">Andrea Fercia</a>, <a href="https://profiles.wordpress.org/azaozz/">Andrew Ozz</a>, <a href="https://profiles.wordpress.org/asalce/">asalce</a>, <a href="https://profiles.wordpress.org/blobfolio/">blobfolio</a>, <a href="https://profiles.wordpress.org/gitlost/">bonger</a>, <a href="https://profiles.wordpress.org/boonebgorges/">Boone Gorges</a>, <a href="https://profiles.wordpress.org/bor0/">Boro Sitnikovski</a>, <a href="https://profiles.wordpress.org/bradyvercher/">Brady Vercher</a>, <a href="https://profiles.wordpress.org/drrobotnik/">Brandon Lavigne</a>, <a href="https://profiles.wordpress.org/bhargavbhandari90/">Bunty</a>, <a href="https://profiles.wordpress.org/ccprog/">ccprog</a>, <a href="https://profiles.wordpress.org/ketuchetan/">chetansatasiya</a>, <a href="https://profiles.wordpress.org/davidakennedy/">David A. Kennedy</a>, <a href="https://profiles.wordpress.org/dlh/">David Herrera</a>, <a href="https://profiles.wordpress.org/dhanendran/">Dhanendran</a>, <a href="https://profiles.wordpress.org/dd32/">Dion Hulse</a>, <a href="https://profiles.wordpress.org/ocean90/">Dominik Schilling (ocean90)</a>, <a href="https://profiles.wordpress.org/drivingralle/">Drivingralle</a>, <a href="https://profiles.wordpress.org/iseulde/">Ella Van Dorpe</a>, <a href="https://profiles.wordpress.org/pento/">Gary Pendergast</a>, <a href="https://profiles.wordpress.org/iandunn/">Ian Dunn</a>, <a href="https://profiles.wordpress.org/ipstenu/">Ipstenu (Mika Epstein)</a>, <a href="https://profiles.wordpress.org/jnylen0/">James Nylen</a>, <a href="https://profiles.wordpress.org/jazbek/">jazbek</a>, <a href="https://profiles.wordpress.org/jeremyfelt/">Jeremy Felt</a>, <a href="https://profiles.wordpress.org/jpry/">Jeremy Pry</a>, <a href="https://profiles.wordpress.org/joehoyle/">Joe Hoyle</a>, <a href="https://profiles.wordpress.org/joemcgill/">Joe McGill</a>, <a href="https://profiles.wordpress.org/johnbillion/">John Blackbourn</a>, <a href="https://profiles.wordpress.org/johnjamesjacoby/">John James Jacoby</a>, <a href="https://profiles.wordpress.org/desrosj/">Jonathan Desrosiers</a>, <a href="https://profiles.wordpress.org/ryelle/">Kelly Dwan</a>, <a href="https://profiles.wordpress.org/markoheijnen/">Marko Heijnen</a>, <a href="https://profiles.wordpress.org/matheusgimenez/">MatheusGimenez</a>, <a href="https://profiles.wordpress.org/mnelson4/">Mike Nelson</a>, <a href="https://profiles.wordpress.org/mikeschroder/">Mike Schroder</a>, <a href="https://profiles.wordpress.org/codegeass/">Muhammet Arslan</a>, <a href="https://profiles.wordpress.org/celloexpressions/">Nick Halsey</a>, <a href="https://profiles.wordpress.org/swissspidy/">Pascal Birchler</a>, <a href="https://profiles.wordpress.org/pbearne/">Paul Bearne</a>, <a href="https://profiles.wordpress.org/pavelevap/">pavelevap</a>, <a href="https://profiles.wordpress.org/peterwilsoncc/">Peter Wilson</a>, <a href="https://profiles.wordpress.org/rachelbaker/">Rachel Baker</a>, <a href="https://profiles.wordpress.org/reldev/">reldev</a>, <a href="https://profiles.wordpress.org/sanchothefat/">Robert O&#8217;Rourke</a>, <a href="https://profiles.wordpress.org/welcher/">Ryan Welcher</a>, <a href="https://profiles.wordpress.org/sanketparmar/">Sanket Parmar</a>, <a href="https://profiles.wordpress.org/seanchayes/">Sean Hayes</a>, <a href="https://profiles.wordpress.org/sergeybiryukov/">Sergey Biryukov</a>, <a href="https://profiles.wordpress.org/netweb/">Stephen Edgar</a>, <a href="https://profiles.wordpress.org/triplejumper12/">triplejumper12</a>, <a href="https://profiles.wordpress.org/westonruter/">Weston Ruter</a>, and <a href="https://profiles.wordpress.org/wpfo/">wpfo</a>.</p>\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:30:"com-wordpress:feed-additions:1";a:1:{s:7:"post-id";a:1:{i:0;a:5:{s:4:"data";s:4:"4696";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:1;a:6:{s:4:"data";s:39:"\n		\n		\n		\n		\n				\n		\n		\n\n		\n		\n				\n			";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:4:{s:0:"";a:6:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:32:"WordPress 4.7.2 Security Release";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:68:"https://wordpress.org/news/2017/01/wordpress-4-7-2-security-release/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Thu, 26 Jan 2017 19:34:02 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:8:"category";a:3:{i:0;a:5:{s:4:"data";s:8:"Releases";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:1;a:5:{s:4:"data";s:8:"Security";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:2;a:5:{s:4:"data";s:3:"4.7";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"https://wordpress.org/news/?p=4676";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:357:"WordPress 4.7.2 is now available. This is a security release for all previous versions and we strongly encourage you to update your sites immediately. WordPress versions 4.7.1 and earlier are affected by three security issues: The user interface for assigning taxonomy terms in Press This is shown to users who do not have permissions to use it. [&#8230;]";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:17:"Aaron D. Campbell";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:40:"http://purl.org/rss/1.0/modules/content/";a:1:{s:7:"encoded";a:1:{i:0;a:5:{s:4:"data";s:2142:"<p>WordPress 4.7.2 is now available. This is a <strong>security release</strong> for all previous versions and we strongly encourage you to update your sites immediately.</p>\n<p>WordPress versions 4.7.1 and earlier are affected by three security issues:</p>\n<ol>\n<li>The user interface for assigning taxonomy terms in Press This is shown to users who do not have permissions to use it. Reported by David Herrera of <a href="https://www.alleyinteractive.com/">Alley Interactive</a>.</li>\n<li><code>WP_Query</code> is vulnerable to a SQL injection (SQLi) when passing unsafe data. WordPress core is not directly vulnerable to this issue, but we&#8217;ve added hardening to prevent plugins and themes from accidentally causing a vulnerability. Reported by <a href="https://github.com/mjangda">Mo Jangda</a> (batmoo).</li>\n<li>A cross-site scripting (XSS) vulnerability was discovered in the posts list table. Reported by <a href="https://iandunn.name/">Ian Dunn</a> of the WordPress Security Team.</li>\n<li>An unauthenticated privilege escalation vulnerability was discovered in a REST API endpoint. Reported by <a href="https://twitter.com/MarcS0h">Marc-Alexandre Montpas</a> of Sucuri Security. *</li>\n</ol>\n<p>Thank you to the reporters of these issues for practicing <a href="https://make.wordpress.org/core/handbook/testing/reporting-security-vulnerabilities/">responsible disclosure</a>.</p>\n<p><a href="https://wordpress.org/download/">Download WordPress 4.7.2</a> or venture over to Dashboard → Updates and simply click “Update Now.” Sites that support automatic background updates are already beginning to update to WordPress 4.7.2.</p>\n<p>Thanks to everyone who contributed to 4.7.2.</p>\n<p>* Update: An additional serious vulnerability was fixed in this release and public disclosure was delayed. For more information on this vulnerability, additional mitigation steps taken, and an explanation for why disclosure was delayed, please read <a href="https://make.wordpress.org/core/2017/02/01/disclosure-of-additional-security-fix-in-wordpress-4-7-2/">Disclosure of Additional Security Fix in WordPress 4.7.2</a>.</p>\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:30:"com-wordpress:feed-additions:1";a:1:{s:7:"post-id";a:1:{i:0;a:5:{s:4:"data";s:4:"4676";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:2;a:6:{s:4:"data";s:39:"\n		\n		\n		\n		\n				\n		\n		\n\n		\n		\n				\n			";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:4:{s:0:"";a:6:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:48:"WordPress 4.7.1 Security and Maintenance Release";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:84:"https://wordpress.org/news/2017/01/wordpress-4-7-1-security-and-maintenance-release/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Wed, 11 Jan 2017 03:53:57 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:8:"category";a:3:{i:0;a:5:{s:4:"data";s:8:"Releases";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:1;a:5:{s:4:"data";s:8:"Security";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:2;a:5:{s:4:"data";s:3:"4.7";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"https://wordpress.org/news/?p=4650";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:375:"WordPress 4.7 has been downloaded over 10 million times since its release on December 6, 2016 and we are pleased to announce the immediate availability of WordPress 4.7.1. This is a security release for all previous versions and we strongly encourage you to update your sites immediately. WordPress versions 4.7 and earlier are affected by eight security issues: [&#8230;]";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:17:"Aaron D. Campbell";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:40:"http://purl.org/rss/1.0/modules/content/";a:1:{s:7:"encoded";a:1:{i:0;a:5:{s:4:"data";s:6520:"<p>WordPress 4.7 has been <a href="https://wordpress.org/download/counter/">downloaded over 10 million times</a> since its release on December 6, 2016 and we are pleased to announce the immediate availability of WordPress 4.7.1. This is a <strong>security release</strong> for all previous versions and we strongly encourage you to update your sites immediately.</p>\n<p>WordPress versions 4.7 and earlier are affected by eight security issues:</p>\n<ol>\n<li>Remote code execution (RCE) in PHPMailer &#8211; <em>No specific issue appears to affect WordPress</em> or any of the major plugins we investigated but, out of an abundance of caution, we updated PHPMailer in this release. This issue was fixed in PHPMailer thanks to <a href="https://legalhackers.com/">Dawid Golunski</a> and <a href="https://twitter.com/Zenexer">Paul Buonopane</a>.</li>\n<li>The REST API exposed user data for all users who had authored a post of a public post type. WordPress 4.7.1 limits this to only post types which have specified that they should be shown within the REST API. Reported by <a href="https://poststatus.com/">Krogsgard</a> and <a href="https://ithemes.com/">Chris Jean</a>.</li>\n<li>Cross-site scripting (XSS) via the plugin name or version header on <code>update-core.php</code>. Reported by <a href="https://dominikschilling.de/">Dominik Schilling</a> of the WordPress Security Team.</li>\n<li>Cross-site request forgery (CSRF) bypass via uploading a Flash file. Reported by <a href="https://twitter.com/Abdulahhusam">Abdullah Hussam</a>.</li>\n<li>Cross-site scripting (XSS) via theme name fallback. Reported by <a href="https://pentest.blog/">Mehmet Ince</a>.</li>\n<li>Post via email checks <code>mail.example.com</code> if default settings aren&#8217;t changed. Reported by John Blackbourn of the WordPress Security Team.</li>\n<li>A cross-site request forgery (CSRF) was discovered in the accessibility mode of widget editing. Reported by <a href="https://dk.linkedin.com/in/ronni-skansing-36143b65">Ronnie Skansing</a>.</li>\n<li>Weak cryptographic security for multisite activation key. Reported by <a href="https://itsjack.cc/">Jack</a>.</li>\n</ol>\n<p>Thank you to the reporters for practicing <a href="https://make.wordpress.org/core/handbook/testing/reporting-security-vulnerabilities/">responsible disclosure</a>.</p>\n<p>In addition to the security issues above, WordPress 4.7.1 fixes 62 bugs from 4.7. For more information, see the <a href="https://codex.wordpress.org/Version_4.7.1">release notes</a> or consult the <a href="https://core.trac.wordpress.org/query?milestone=4.7.1">list of changes</a>.</p>\n<p><a href="https://wordpress.org/download/">Download WordPress 4.7.1</a> or venture over to Dashboard → Updates and simply click “Update Now.” Sites that support automatic background updates are already beginning to update to WordPress 4.7.1.</p>\n<p>Thanks to everyone who contributed to 4.7.1: <a href="https://profiles.wordpress.org/aaroncampbell/">Aaron D. Campbell</a>, <a href="https://profiles.wordpress.org/jorbin/">Aaron Jorbin</a>, <a href="https://profiles.wordpress.org/adamsilverstein/">Adam Silverstein</a>, <a href="https://profiles.wordpress.org/afercia/">Andrea Fercia</a>, <a href="https://profiles.wordpress.org/azaozz/">Andrew Ozz</a>, <a href="https://profiles.wordpress.org/gitlost/">bonger</a>, <a href="https://profiles.wordpress.org/boonebgorges/">Boone Gorges</a>, <a href="https://profiles.wordpress.org/chandrapatel/">Chandra Patel</a>, <a href="https://profiles.wordpress.org/christian1012/">Christian Chung</a>, <a href="https://profiles.wordpress.org/dlh/">David Herrera</a>, <a href="https://profiles.wordpress.org/dshanske/">David Shanske</a>, <a href="https://profiles.wordpress.org/dd32/">Dion Hulse</a>, <a href="https://profiles.wordpress.org/ocean90/">Dominik Schilling (ocean90)</a>, <a href="https://profiles.wordpress.org/dreamon11/">DreamOn11</a>, <a href="https://profiles.wordpress.org/chopinbach/">Edwin Cromley</a>, <a href="https://profiles.wordpress.org/iseulde/">Ella van Dorpe</a>, <a href="https://profiles.wordpress.org/pento/">Gary Pendergast</a>, <a href="https://profiles.wordpress.org/hristo-sg/">Hristo Pandjarov</a>, <a href="https://profiles.wordpress.org/jnylen0/">James Nylen</a>, <a href="https://profiles.wordpress.org/jblz/">Jeff Bowen</a>, <a href="https://profiles.wordpress.org/jeremyfelt/">Jeremy Felt</a>, <a href="https://profiles.wordpress.org/jpry/">Jeremy Pry</a>, <a href="https://profiles.wordpress.org/joehoyle/">Joe Hoyle</a>, <a href="https://profiles.wordpress.org/joemcgill/">Joe McGill</a>, <a href="https://profiles.wordpress.org/johnbillion/">John Blackbourn</a>, <a href="https://profiles.wordpress.org/kkoppenhaver/">Keanan Koppenhaver</a>, <a href="https://profiles.wordpress.org/obenland/">Konstantin Obenland</a>, <a href="https://profiles.wordpress.org/laurelfulford/">laurelfulford</a>, <a href="https://profiles.wordpress.org/tyxla/">Marin Atanasov</a>, <a href="https://profiles.wordpress.org/mattyrob/">mattyrob</a>, <a href="https://profiles.wordpress.org/monikarao/">monikarao</a>, <a href="https://profiles.wordpress.org/natereist/">Nate Reist</a>, <a href="https://profiles.wordpress.org/celloexpressions/">Nick Halsey</a>, <a href="https://profiles.wordpress.org/nikschavan/">Nikhil Chavan</a>, <a href="https://profiles.wordpress.org/nullvariable/">nullvariable</a>, <a href="https://profiles.wordpress.org/sirbrillig/">Payton Swick</a>, <a href="https://profiles.wordpress.org/peterwilsoncc/">Peter Wilson</a>, <a href="https://profiles.wordpress.org/presskopp/">Presskopp</a>, <a href="https://profiles.wordpress.org/rachelbaker/">Rachel Baker</a>, <a href="https://profiles.wordpress.org/rmccue/">Ryan McCue</a>, <a href="https://profiles.wordpress.org/sanketparmar/">Sanket Parmar</a>, <a href="https://profiles.wordpress.org/sebastianpisula/">Sebastian Pisula</a>, <a href="https://profiles.wordpress.org/sfpt/">sfpt</a>, <a href="https://profiles.wordpress.org/shazahm1hotmailcom/">shazahm1</a>, <a href="https://profiles.wordpress.org/sstoqnov/">Stanimir Stoyanov</a>, <a href="https://profiles.wordpress.org/stevenkword/">Steven Word</a>, <a href="https://profiles.wordpress.org/szaqal21/">szaqal21</a>, <a href="https://profiles.wordpress.org/timph/">timph</a>, <a href="https://profiles.wordpress.org/voldemortensen/">voldemortensen</a>, <a href="https://profiles.wordpress.org/vortfu/">vortfu</a>, and <a href="https://profiles.wordpress.org/westonruter/">Weston Ruter</a>.</p>\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:30:"com-wordpress:feed-additions:1";a:1:{s:7:"post-id";a:1:{i:0;a:5:{s:4:"data";s:4:"4650";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:3;a:6:{s:4:"data";s:36:"\n		\n		\n		\n		\n				\n\n		\n		\n				\n	\n\n\n		";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:4:{s:0:"";a:7:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:27:"WordPress 4.7 “Vaughan”";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:43:"https://wordpress.org/news/2016/12/vaughan/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Tue, 06 Dec 2016 19:27:41 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:8:"category";a:1:{i:0;a:5:{s:4:"data";s:8:"Releases";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:34:"https://wordpress.org/news/?p=4596";s:7:"attribs";a:1:{s:0:"";a:1:{s:11:"isPermaLink";s:5:"false";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:240:"Version 4.7 of WordPress, named “Vaughan” in honor of legendary jazz vocalist Sarah "Sassy" Vaughan, is available for download or update in your WordPress dashboard. New features in 4.7 help you get your site set up the way you want it.";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:9:"enclosure";a:3:{i:0;a:5:{s:4:"data";s:0:"";s:7:"attribs";a:1:{s:0:"";a:3:{s:3:"url";s:60:"https://wordpress.org/news/files/2016/12/starter-content.mp4";s:6:"length";s:7:"3736020";s:4:"type";s:9:"video/mp4";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:1;a:5:{s:4:"data";s:0:"";s:7:"attribs";a:1:{s:0:"";a:3:{s:3:"url";s:59:"https://wordpress.org/news/files/2016/12/edit-shortcuts.mp4";s:6:"length";s:7:"1127483";s:4:"type";s:9:"video/mp4";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}i:2;a:5:{s:4:"data";s:0:"";s:7:"attribs";a:1:{s:0:"";a:3:{s:3:"url";s:58:"https://wordpress.org/news/files/2016/12/video-headers.mp4";s:6:"length";s:7:"1549803";s:4:"type";s:9:"video/mp4";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:15:"Helen Hou-Sandi";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:40:"http://purl.org/rss/1.0/modules/content/";a:1:{s:7:"encoded";a:1:{i:0;a:5:{s:4:"data";s:45502:"<p>Version 4.7 of WordPress, named “Vaughan” in honor of legendary jazz vocalist Sarah &#8220;Sassy&#8221; Vaughan, is available for download or update in your WordPress dashboard. New features in 4.7 help you get your site set up the way you want it.</p>\n<div id="v-AHz0Ca46-1" class="video-player"><video id="v-AHz0Ca46-1-video" width="632" height="354" poster="https://videos.files.wordpress.com/AHz0Ca46/wp4-7-vaughan-r8-mastered_scruberthumbnail_0.jpg" controls="true" preload="metadata" dir="ltr" lang="en"><source src="https://videos.files.wordpress.com/AHz0Ca46/wp4-7-vaughan-r8-mastered_dvd.mp4" type="video/mp4; codecs=&quot;avc1.64001E, mp4a.40.2&quot;" /><source src="https://videos.files.wordpress.com/AHz0Ca46/wp4-7-vaughan-r8-mastered_fmt1.ogv" type="video/ogg; codecs=&quot;theora, vorbis&quot;" /><div><img alt="Introducing WordPress 4.7" src="https://i1.wp.com/videos.files.wordpress.com/AHz0Ca46/wp4-7-vaughan-r8-mastered_scruberthumbnail_0.jpg?resize=632%2C354&#038;ssl=1" data-recalc-dims="1" /></div><p>Introducing WordPress 4.7</p></video></div>\n<hr />\n<h2 style="text-align:center">Presenting Twenty Seventeen</h2>\n<p>A brand new default theme brings your site to life with immersive featured images and video headers.</p>\n<p><img class="alignnone wp-image-4618 size-large" src="https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Twenty-Seventeen-1.jpg?resize=632%2C356&#038;ssl=1" srcset="https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Twenty-Seventeen-1.jpg?resize=1024%2C576&amp;ssl=1 1024w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Twenty-Seventeen-1.jpg?resize=300%2C169&amp;ssl=1 300w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Twenty-Seventeen-1.jpg?resize=768%2C432&amp;ssl=1 768w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Twenty-Seventeen-1.jpg?w=1600&amp;ssl=1 1600w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Twenty-Seventeen-1.jpg?w=1264&amp;ssl=1 1264w" sizes="(max-width: 632px) 100vw, 632px" data-recalc-dims="1" /></p>\n<p>Twenty Seventeen focuses on business sites and features a customizable front page with multiple sections. Personalize it with widgets, navigation, social menus, a logo, custom colors, and more. Our default theme for 2017 works great in many languages, on any device, and for a wide range of users.</p>\n<hr />\n<h2 style="text-align:center">Your Site, Your Way</h2>\n<p>WordPress 4.7 adds new features to the customizer to help take you through the initial setup of a theme, with non-destructive live previews of all your changes in one uninterrupted workflow.</p>\n<h3>Theme Starter Content</h3>\n<div style="width: 632px;" class="wp-video"><!--[if lt IE 9]><script>document.createElement(''video'');</script><![endif]-->\n<video class="wp-video-shortcode" id="video-4596-1" width="632" height="346" loop="1" autoplay="1" preload="metadata" controls="controls"><source type="video/mp4" src="https://wordpress.org/news/files/2016/12/starter-content.mp4?_=1" /><a href="https://wordpress.org/news/files/2016/12/starter-content.mp4">https://wordpress.org/news/files/2016/12/starter-content.mp4</a></video></div>\n<p>To help give you a solid base to build from, individual themes can provide starter content that appears when you go to customize your brand new site. This can range from placing a business information widget in the best location to providing a sample menu with social icon links to a static front page complete with beautiful images. Don’t worry &#8211; nothing new will appear on the live site until you’re ready to save and publish your initial theme setup.</p>\n<div style="float: left;width: 48%;margin: 0">\n<h3>Edit Shortcuts</h3>\n<div style="width: 300px;" class="wp-video"><video class="wp-video-shortcode" id="video-4596-2" width="300" height="173" poster="https://wordpress.org/news/files/2016/12/4.7-—-Edit-Shortcuts.jpg" loop="1" autoplay="1" preload="metadata" controls="controls"><source type="video/mp4" src="https://wordpress.org/news/files/2016/12/edit-shortcuts.mp4?_=2" /><a href="https://wordpress.org/news/files/2016/12/edit-shortcuts.mp4">https://wordpress.org/news/files/2016/12/edit-shortcuts.mp4</a></video></div>\n<p>Visible icons appear to show you which parts of your site can be customized while live previewing. Click on a shortcut and get straight to editing. Paired with starter content, getting started with customizing your site is faster than ever.</p>\n</div>\n<div style="float: right;width: 48%;margin: 0">\n<h3>Video Headers</h3>\n<div style="width: 300px;" class="wp-video"><video class="wp-video-shortcode" id="video-4596-3" width="300" height="173" poster="https://wordpress.org/news/files/2016/12/4.7-—-Header-Video.jpg" loop="1" autoplay="1" preload="metadata" controls="controls"><source type="video/mp4" src="https://wordpress.org/news/files/2016/12/video-headers.mp4?_=3" /><a href="https://wordpress.org/news/files/2016/12/video-headers.mp4">https://wordpress.org/news/files/2016/12/video-headers.mp4</a></video></div>\n<p>Sometimes a big atmospheric video as a moving header image is just what you need to showcase your wares; go ahead and try it out with Twenty Seventeen. Need some video inspiration? Try searching for sites with video headers available for download and use.</p>\n</div>\n<div style="clear: both"></div>\n<div style="float: left;width: 48%;margin: 0">\n<h3>Smoother Menu Building</h3>\n<p><img class="wp-image-4606 size-medium alignright" src="https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-Nav.jpg?resize=300%2C158&#038;ssl=1" srcset="https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-Nav.jpg?resize=300%2C158&amp;ssl=1 300w, https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-Nav.jpg?w=760&amp;ssl=1 760w" sizes="(max-width: 300px) 100vw, 300px" data-recalc-dims="1" /></p>\n<p>Many menus for sites contain links to the pages of your site, but what happens when you don’t have any pages yet? Now you can add new pages while building menus instead of leaving the customizer and abandoning your changes. Once you’ve published your customizations, you’ll have new pages ready for you to fill with content.</p>\n</div>\n<div style="float: right;width: 48%;margin: 0">\n<h3>Custom CSS</h3>\n<p><img class="wp-image-4607 size-medium alignright" src="https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-CSS.jpg?resize=300%2C158&#038;ssl=1" srcset="https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-CSS.jpg?resize=300%2C158&amp;ssl=1 300w, https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-CSS.jpg?w=760&amp;ssl=1 760w" sizes="(max-width: 300px) 100vw, 300px" data-recalc-dims="1" /></p>\n<p>Sometimes you just need a few visual tweaks to make your site perfect. WordPress 4.7 allows you to add custom CSS and instantly see how your changes affect your site. The live preview allows you to work quickly without page refreshes slowing you down.</p>\n</div>\n<div style="clear: both"></div>\n<hr />\n<div style="float: left;width: 48%;margin: 0">\n<h3>PDF Thumbnail Previews</h3>\n<p><img class="wp-image-4609 size-medium alignright" src="https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-PDF.jpg?resize=300%2C158&#038;ssl=1" srcset="https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-PDF.jpg?resize=300%2C158&amp;ssl=1 300w, https://i1.wp.com/wordpress.org/news/files/2016/12/4.7-—-PDF.jpg?w=760&amp;ssl=1 760w" sizes="(max-width: 300px) 100vw, 300px" data-recalc-dims="1" /></p>\n<p>Managing your document collection is easier with WordPress 4.7. Uploading PDFs will generate thumbnail images so you can more easily distinguish between all your documents.</p>\n</div>\n<div style="float: right;width: 48%;margin: 0">\n<h3>Dashboard in your language</h3>\n<p><img class="wp-image-4608 size-medium alignright" src="https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Language.jpg?resize=300%2C158&#038;ssl=1" srcset="https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Language.jpg?resize=300%2C158&amp;ssl=1 300w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-Language.jpg?w=760&amp;ssl=1 760w" sizes="(max-width: 300px) 100vw, 300px" data-recalc-dims="1" /></p>\n<p>Just because your site is in one language doesn’t mean that everybody helping manage it prefers that language for their admin. Add more languages to your site and a user language option will show up in your user’s profiles.</p>\n</div>\n<div style="clear: both"></div>\n<hr />\n<h2 style="text-align:center">Introducing REST API Content Endpoints</h2>\n<p>WordPress 4.7 comes with REST API endpoints for posts, comments, terms, users, meta, and settings.</p>\n<p><img class="size-large wp-image-4600 alignnone" src="https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-API.jpg?resize=632%2C205&#038;ssl=1" alt="" srcset="https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-API.jpg?resize=1024%2C332&amp;ssl=1 1024w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-API.jpg?resize=300%2C97&amp;ssl=1 300w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-API.jpg?resize=768%2C249&amp;ssl=1 768w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-API.jpg?w=1264&amp;ssl=1 1264w, https://i2.wp.com/wordpress.org/news/files/2016/12/4.7-—-API.jpg?w=1896&amp;ssl=1 1896w" sizes="(max-width: 632px) 100vw, 632px" data-recalc-dims="1" /></p>\n<p>Content endpoints provide machine-readable external access to your WordPress site with a clear, standards-driven interface, paving the way for new and innovative methods of interacting with sites through plugins, themes, apps, and beyond. Ready to get started with development? <a href="https://developer.wordpress.org/rest-api/reference/">Check out the REST API reference.</a></p>\n<hr />\n<h2 style="text-align:center">Even More Developer Happiness <img src="https://s.w.org/images/core/emoji/2.2.1/72x72/1f60a.png" alt="', 'no'),
(258, 'ga_version', '6.4.3', 'yes'),
(259, 'ga_annon', '', 'yes'),
(260, 'ga_defaults', 'yes', 'yes'),
(261, 'link_manager_enabled', '1', 'yes'),
(263, 'db_upgraded', '', 'yes'),
(266, 'theme_switched', '', 'yes'),
(267, 'freshy_options', 'a:20:{s:15:"highlight_color";s:7:"#FF3C00";s:17:"description_color";s:7:"#ADCF20";s:12:"author_color";s:7:"#a3cb00";s:10:"sidebar_bg";s:7:"#FFFFFF";s:20:"sidebar_titles_color";s:7:"#f78b0c";s:17:"sidebar_titles_bg";s:7:"#FFFFFF";s:7:"menu_bg";s:21:"menu_start_triple.gif";s:10:"menu_color";s:7:"#000000";s:9:"header_bg";s:10:"header.jpg";s:16:"header_bg_custom";s:0:"";s:19:"sidebar_titles_type";s:7:"stripes";s:16:"first_menu_label";s:5:"Inici";s:15:"blog_menu_label";s:4:"Blog";s:15:"last_menu_label";s:7:"Contact";s:14:"last_menu_type";s:0:"";s:13:"contact_email";s:0:"";s:12:"contact_link";s:0:"";s:9:"menu_type";s:4:"auto";s:10:"args_pages";s:32:"sort_column=menu_order&title_li=";s:9:"args_cats";s:168:"hide_empty=0&sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image=http://agora/blocs/wp-content/themes/xtec-v1.1/images/icons/feed-icon-10x10.gif";}', 'yes'),
(271, 'tadv_version', '4000', 'yes'),
(268, 'category_children', 'a:0:{}', 'yes'),
(269, 'theme_mods_xtec-v1.1', 'a:2:{i:0;b:0;s:16:"sidebars_widgets";a:2:{s:4:"time";i:1389360150;s:4:"data";a:2:{s:19:"wp_inactive_widgets";a:13:{i:0;s:7:"pages-2";i:1;s:10:"calendar-2";i:2;s:7:"links-2";i:3;s:6:"text-2";i:4;s:5:"rss-2";i:5;s:11:"tag_cloud-2";i:6;s:10:"nav_menu-2";i:7;s:8:"search-2";i:8;s:14:"recent-posts-2";i:9;s:17:"recent-comments-2";i:10;s:10:"archives-2";i:11;s:12:"categories-2";i:12;s:6:"meta-2";}s:9:"sidebar-1";N;}}}', 'yes'),
(270, 'theme_mods_xtecblocsdefault', 'a:1:{i:0;b:0;}', 'yes'),
(283, 'ga_disable_gasites', 'disabled', 'yes'),
(284, 'ga_analytic_snippet', 'enabled', 'yes'),
(285, 'ga_admin_disable_DimentionIndex', '', 'yes'),
(286, 'key_ga_show_ad', '1', 'yes'),
(287, 'ga_enhanced_link_attr', 'disabled', 'yes'),
(360, 'wpsupercache_count', '0', 'yes'),
(958, 'bwp_capt_theme', 'a:4:{s:9:"input_tab";s:1:"0";s:10:"enable_css";s:3:"yes";s:11:"select_lang";s:2:"es";s:12:"select_theme";s:3:"red";}', 'yes'),
(1060, '_transient_timeout_feed_mod_b9388c83948825c1edaef0d856b7b109', '1489537719', 'no'),
(1061, '_transient_feed_mod_b9388c83948825c1edaef0d856b7b109', '1489494519', 'no'),
(954, 'bwp_capt_version', '1.1.3', 'yes'),
(957, 'bwp_capt_general', 'a:18:{s:12:"input_pubkey";s:40:"6LdeRAUTAAAAAElOIZz-mWS21zDs6pe43Uhg4Btg";s:12:"input_prikey";s:40:"6LdeRAUTAAAAADdO3-Odt7C097AzBOMHGO1I6zeL";s:11:"input_error";s:80:"<strong>ERROR:</strong> Incorrect or empty reCAPTCHA response, please try again.";s:10:"input_back";s:127:"Error: Incorrect or empty reCAPTCHA response, please click the back button on your browser''s toolbar or click on %s to go back.";s:14:"input_approved";s:1:"1";s:14:"enable_comment";s:3:"yes";s:19:"enable_registration";s:0:"";s:12:"enable_login";s:0:"";s:14:"enable_akismet";s:0:"";s:15:"use_global_keys";s:3:"yes";s:10:"select_cap";s:14:"manage_options";s:14:"select_cf7_tag";s:9:"recaptcha";s:15:"select_response";s:8:"redirect";s:15:"select_position";s:19:"after_comment_field";s:20:"select_akismet_react";s:4:"hold";s:15:"hide_registered";s:3:"yes";s:8:"hide_cap";s:0:"";s:13:"hide_approved";s:0:"";}', 'yes'),
(463, 'wsl_settings_welcome_panel_enabled', '2.2.3', 'yes'),
(464, 'wsl_settings_redirect_url', 'http://agora/blocs', 'yes'),
(465, 'wsl_settings_force_redirect_url', '2', 'yes'),
(466, 'wsl_settings_connect_with_label', 'Connecta amb:', 'yes'),
(467, 'wsl_settings_users_avatars', '1', 'yes'),
(468, 'wsl_settings_use_popup', '2', 'yes'),
(469, 'wsl_settings_widget_display', '1', 'yes'),
(470, 'wsl_settings_authentication_widget_css', '.wp-social-login-connect-with {}\n.wp-social-login-provider-list {}\n.wp-social-login-provider-list a {}\n.wp-social-login-provider-list img {}\n.wsl_connect_with_provider {}', 'yes'),
(471, 'wsl_settings_bouncer_registration_enabled', '1', 'yes'),
(472, 'wsl_settings_bouncer_authentication_enabled', '1', 'yes'),
(473, 'wsl_settings_bouncer_profile_completion_require_email', '2', 'yes'),
(474, 'wsl_settings_bouncer_profile_completion_change_username', '2', 'yes'),
(475, 'wsl_settings_bouncer_new_users_moderation_level', '1', 'yes'),
(476, 'wsl_settings_bouncer_new_users_membership_default_role', 'default', 'yes'),
(477, 'wsl_settings_bouncer_new_users_restrict_domain_enabled', '2', 'yes'),
(478, 'wsl_settings_bouncer_new_users_restrict_domain_text_bounce', '<strong>This website is restricted to invited readers only.</strong><p>It doesn''t look like you have been invited to access this site. If you think this is a mistake, you might want to contact the website owner and request an invitation.<p>', 'yes'),
(479, 'wsl_settings_bouncer_new_users_restrict_email_enabled', '2', 'yes'),
(480, 'wsl_settings_bouncer_new_users_restrict_email_text_bounce', '<strong>This website is restricted to invited readers only.</strong><p>It doesn''t look like you have been invited to access this site. If you think this is a mistake, you might want to contact the website owner and request an invitation.<p>', 'yes'),
(481, 'wsl_settings_bouncer_new_users_restrict_profile_enabled', '2', 'yes'),
(482, 'wsl_settings_bouncer_new_users_restrict_profile_text_bounce', '<strong>This website is restricted to invited readers only.</strong><p>It doesn''t look like you have been invited to access this site. If you think this is a mistake, you might want to contact the website owner and request an invitation.<p>', 'yes'),
(483, 'wsl_settings_contacts_import_facebook', '2', 'yes'),
(484, 'wsl_settings_contacts_import_google', '2', 'yes'),
(485, 'wsl_settings_contacts_import_twitter', '2', 'yes'),
(486, 'wsl_settings_contacts_import_live', '2', 'yes'),
(487, 'wsl_settings_contacts_import_linkedin', '2', 'yes'),
(488, 'wsl_settings_buddypress_enable_mapping', '2', 'yes'),
(489, 'wsl_settings_buddypress_xprofile_map', '', 'yes'),
(490, 'wsl_settings_Google_enabled', '0', 'yes'),
(491, 'wsl_settings_Moodle_enabled', '0', 'yes'),
(492, 'wsl_components_core_enabled', '1', 'yes'),
(493, 'wsl_components_networks_enabled', '1', 'yes'),
(494, 'wsl_components_login-widget_enabled', '1', 'yes'),
(495, 'wsl_components_bouncer_enabled', '1', 'yes'),
(496, 'wsl_settings_Google_app_scope', 'profile https://www.googleapis.com/auth/plus.profile.emails.read', 'yes'),
(497, 'supercache_stats', 'a:3:{s:9:"generated";i:1429271272;s:10:"supercache";a:5:{s:7:"expired";i:0;s:12:"expired_list";a:0:{}s:6:"cached";i:0;s:11:"cached_list";a:0:{}s:2:"ts";i:1429271272;}s:7:"wpcache";a:3:{s:6:"cached";i:0;s:7:"expired";i:0;s:5:"fsize";s:3:"0KB";}}', 'yes'),
(339, 'mucd_duplicable', 'no', 'yes'),
(359, 'wpsupercache_start', '1424430200', 'yes'),
(582, 'post_count', '1', 'yes'),
(624, 'gce_settings_general', 'a:1:{s:13:"save_settings";i:1;}', 'yes'),
(626, 'gce_cpt_setup', '1', 'yes'),
(560, 'WPLANG', 'ca', 'yes'),
(561, 'new_admin_email', 'admin@blocs.xtec.cat', 'yes'),
(1038, 'ga_analyticator_global_notification', '1', 'yes'),
(1039, 'widget_gce_widget', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(1047, '_transient_timeout__simple-calendar_activation_redirect', '1489494571', 'no'),
(1048, '_transient__simple-calendar_activation_redirect', 'update', 'no'),
(1074, 'calendar_feed_children', 'a:0:{}', 'yes'),
(1044, 'calendar_type_children', 'a:0:{}', 'yes'),
(1045, 'simple-calendar_settings_feeds', 'a:1:{s:6:"google";a:1:{s:7:"api_key";s:0:"";}}', 'yes'),
(1046, 'simple-calendar_settings_advanced', 'a:1:{s:6:"assets";a:1:{s:11:"disable_css";s:0:"";}}', 'yes'),
(1049, 'simple-calendar_version', '3.1.9', 'yes'),
(1052, '_transient_timeout_feed_mod_ac0b00fe65abe10e0c5b588f3ed8c7ca', '1489537715', 'no'),
(1053, '_transient_feed_mod_ac0b00fe65abe10e0c5b588f3ed8c7ca', '1489494515', 'no'),
(1054, '_transient_timeout_feed_d117b5738fbd35bd8c0391cda1f2b5d9', '1489537717', 'no');
INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1055, '_transient_feed_d117b5738fbd35bd8c0391cda1f2b5d9', 'a:4:{s:5:"child";a:1:{s:0:"";a:1:{s:3:"rss";a:1:{i:0;a:6:{s:4:"data";s:3:"\n\n\n";s:7:"attribs";a:1:{s:0:"";a:1:{s:7:"version";s:3:"2.0";}}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:1:{s:0:"";a:1:{s:7:"channel";a:1:{i:0;a:6:{s:4:"data";s:61:"\n	\n	\n	\n	\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:1:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:16:"WordPress Planet";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:28:"http://planet.wordpress.org/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:8:"language";a:1:{i:0;a:5:{s:4:"data";s:2:"en";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:47:"WordPress Planet - http://planet.wordpress.org/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"item";a:50:{i:0;a:6:{s:4:"data";s:13:"\n	\n	\n	\n	\n	\n	\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:81:"WPTavern: WordPress.com Updates Its Post Editor With a Distraction-Free Interface";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:29:"https://wptavern.com/?p=67429";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:92:"https://wptavern.com/wordpress-com-updates-its-post-editor-with-a-distraction-free-interface";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:3321:"<p>WordPress.com has <a href="https://en.blog.wordpress.com/2017/03/13/a-distraction-free-writing-space-at-wordpress-com/">unveiled a refreshed post editor</a> that makes content front and center.</p>\n<p>The most noticeable change is the user interface. The sidebar of meta boxes is now on the right-hand side instead of the left. Clicking the Post Settings link hides the sidebar, providing a cleaner interface. The preview and publish buttons are no longer in a metabox and are permanently displayed.</p>\n<img />New WordPress.com Post Editor\n<p>Drafts are quickly accessible by clicking the number next to the Write button. Hovering over a draft title displays a small excerpt of the post. Unlike the distraction-free writing mode in the self-hosted version of WordPress, sidebars and other items on the screen do not disappear and reappear. This animation has <a href="https://wptavern.com/whats-your-first-impression-of-distraction-free-writing-in-wordpress-4-1#comment-62784">been described</a> by some as a distraction.</p>\n<p>Joen Asmussen and Matías Ventura, two Automatticians based in Europe, helped create the new interface. In an interview conducted by John Maeda, <span class="st">Global Head of Computational Design and Inclusion at Automattic, Asmussen describes what he&#8217;s most excited about with the improvements. </span></p>\n<p>&#8220;Everything has a right place,&#8221; Asmussen said. &#8220;In this iteration, we’ve tried to find those places for the preview and publish buttons, as well as the post settings. By making the buttons permanently visible and the sidebar optionally toggled, my hope is that the combination will provide a seamless flow for both the person who just wants to <i>write</i>, as well as the person who needs to configure their post settings.&#8221;</p>\n<p>Ventura says he is happy to bring the focus back on the content by placing it in the center. &#8220;I’m also fond of the recent drafts menu next to the &#8216;Write&#8217; button, as it provides a quick way to carry on with your unfinished posts,&#8221; he said. &#8220;These editor refinements have the potential to let your work on WordPress keep you deeply in the productive state of flow.&#8221;</p>\n<p>The core team continues to <a href="https://wptavern.com/wordpress-core-editor-team-publishes-ui-prototype-for-gutenberg-an-experimental-block-based-editor">work on a block based editor</a> for the open-source WordPress project and <span class="st">Asmussen</span> hints that this approach to writing could one day end up in the WordPress.com post editor.</p>\n<p>After testing the new editor on WordPress.com, I can say that it&#8217;s more enjoyable to use than the distraction-free writing mode in WordPress. There&#8217;s less distraction, meta boxes are either on the screen or they&#8217;re not, and I enjoyed writing without interface elements disappearing and reappearing on the screen.</p>\n<p>If you&#8217;d like to try the new editor on a self-hosted WordPress site, you can do so through Jetpack. Visit the Jetpack dashboard in the WordPress backend, click on the Apps link, then click the Try the New Editor button.</p>\n<p>After using the new editor, let us know what you think. How does it compare to the writing experience currently in WordPress?</p>\n<div id="epoch-width-sniffer"></div>";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Tue, 14 Mar 2017 02:03:50 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:13:"Jeff Chandler";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:1;a:6:{s:4:"data";s:13:"\n	\n	\n	\n	\n	\n	\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:90:"WPTavern: John Maeda’s 2017 Design in Tech Report Puts the Spotlight on Inclusive Design";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:29:"https://wptavern.com/?p=67406";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:98:"https://wptavern.com/john-maedas-2017-design-in-tech-report-puts-the-spotlight-on-inclusive-design";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:6207:"<p><a href="https://i2.wp.com/wptavern.com/wp-content/uploads/2017/03/design-in-tech-2017.png?ssl=1"><img /></a></p>\n<p><a href="https://maedastudio.com/" target="_blank">John Maeda</a>, Automattic&#8217;s Global Head of Computational Design and Inclusion, presented his third annual &#8220;<a href="https://designintechreport.wordpress.com/" target="_blank">Design in Tech</a>&#8221; report at SXSW over the weekend. The previous reports have received more than two million views and this one is equally loaded with thought-provoking information about the future of the design industry.</p>\n<p>&#8220;Design isn’t just about beauty; it’s about market relevance and meaningful results,&#8221; Maeda said. He highlighted how design leaders are increasingly top hires at major corporations, due to market demand. Businesses are beginning to embrace design as a fundamental tool for success. Design used to be relegated to extra-curricular clubs in business schools, but many schools are shifting to include design in the curriculum.</p>\n<p>Constant connectivity in the digital era has brought the idea of &#8220;computational design&#8221; to the forefront, which Maeda describes as &#8220;designing for billions of individual people and in realtime.&#8221; Designing at this scale requires an approach that is inclusive of the widest number of consumers, essentially designing for everyone.</p>\n<p>Maeda believes that &#8220;design and inclusion are inseparable&#8221; and he is on a mission to prove that inclusive design is good business. </p>\n<p>&#8220;When we separate inclusion into essentially an HR compliance topic, it loses its energy,&#8221; Maeda said. &#8220;It&#8217;s important, of course, but it loses the creativity that&#8217;s intrinsic to inclusion.&#8221; His 2017 Design in Tech report focuses on how inclusion matters in business. Maeda admits there are not many examples of how inclusion drives better financial outcomes, but one of his professional aims is to demonstrate how inclusive design can make a financial difference.</p>\n<p>&#8220;That&#8217;s why I joined Automattic,&#8221; Maeda said. &#8220;My hope is that this approach can lead to better business outcomes for the WordPress ecosystem. I don&#8217;t know if it will be possible but that&#8217;s my goal. I want to show that it&#8217;s possible numerically.&#8221;</p>\n<p>Making inclusive design profitable hinges on the principle that if you want to reach a larger market, you have to reach people you&#8217;re not already reaching by being inclusive. This new frontier of design requires some technical understanding outside of purely classical design. The hybrid designer/developer, often referred to as a &#8220;unicorn&#8221; in the tech industry, is often relied upon to bridge that gap.</p>\n<p>Maeda predicts that the scarcity of hybrid designer/developers will soon decrease, due to how things are changing in the industry. After surveying design leaders in 2016, Maeda found that 1/3 had formal engineering/science training, suggesting that &#8220;hybrid&#8221; talent has considerably increased in recent years. He shared his observations from surveying Automattic designers and developers about JavaScript competency. He found a significant segment of designers approaching moderate fluency in JavaScript after WordPress&#8217; 2015 initiative to encourage JavaScript mastery.</p>\n<p>&#8220;The world is moving, and moving with the world is what designers do,&#8221; Maeda said. He also made a bold recommendation for those who are maintaining a design-only skill set:</p>\n<p>&#8220;I encourage you, if you&#8217;re a pure pure designer, to &#8216;impurify yourself,&#8217; because it&#8217;s a whole new world and there&#8217;s a lot to learn,&#8221; Maeda said. &#8220;Anyone who&#8217;s in this game, if you aren&#8217;t watching videos and learning, you get behind in two months.&#8221; </p>\n<p>Maeda also encouraged listeners to shed biases that prevent them from seeing important trends and changes on the web. He addressed misconceptions about how products &#8220;made in China&#8221; are often thought of as something cheap or copycat, but China is moving forward in the mobile revolution in a far more advanced way than many other countries. He highlighted some major design trends pioneered by Chinese designers and how they are impacting the tech industry. </p>\n<p>Maeda closely monitors design-related M&#038;A activity and funds that are design and/or inclusion oriented. His data shows that tech companies are finding more value in design tool companies and design community platforms, with acquisitions steadily increasing every year. The value of design companies is especially evident in China where Maeda noted three designer co-founded Chinese companies have a combined market cap of over $300 billion. </p>\n<p>He also shared what he has learned about designers since taking his position at Automattic, which employs more than 500 people working remotely across 50 countries.</p>\n<p>&#8220;People want to have challenging work; they want to make change happen,&#8221; Maeda said. &#8220;With creative people this is their main driver. If that can&#8217;t be sated, they get unhappy and they leave. The problem is this kind of work is the kind that seems like bonus work, not the main work. So my question as a manager is, &#8216;How do I put the two together in some constructive way?&#8217; How do you make time to learn and grow? That&#8217;s something I didn&#8217;t know was pervasive in a busy busy tech company.&#8221;</p>\n<p>Maeda concludes that it is a good time to be a designer, especially if you&#8217;re willing to make up for the design education gap and teach yourself new skills online. His 2017 Design in Tech report is a must-read, not just for designers but for anyone working in tech or hiring tech talent. Check out the <a href="https://designintechreport.wordpress.com/" target="_blank">full report</a> on WordPress.com. You can also <a href="https://designintechreport.wordpress.com/2017/03/12/design-in-tech-report-2017-video/" target="_blank">listen to the audio of Maeda&#8217;s presentation</a> while viewing the slides.</p>\n<div id="epoch-width-sniffer"></div>";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 13 Mar 2017 20:07:08 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:13:"Sarah Gooding";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:2;a:6:{s:4:"data";s:13:"\n	\n	\n	\n	\n	\n	\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:75:"Post Status: JavaScript frameworks in a WordPress context — Draft podcast";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:31:"https://poststatus.com/?p=35232";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:77:"https://poststatus.com/javascript-frameworks-wordpress-context-draft-podcast/";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:2808:"<p>Welcome to the Post Status <a href="https://poststatus.com/category/draft">Draft podcast</a>, which you can find <a href="https://itunes.apple.com/us/podcast/post-status-draft-wordpress/id976403008">on iTunes</a>, <a href="https://play.google.com/music/m/Ih5egfxskgcec4qadr3f4zfpzzm?t=Post_Status__Draft_WordPress_Podcast">Google Play</a>, <a href="http://www.stitcher.com/podcast/krogsgard/post-status-draft-wordpress-podcast">Stitcher</a>, and <a href="http://simplecast.fm/podcasts/1061/rss">via RSS</a> for your favorite podcatcher. Post Status Draft is hosted by Joe Hoyle &#8212; the CTO of Human Made &#8212; and Brian Krogsgard.</p>\n<p><span>Live from the A Day of REST workshops, Brian, Joe, and Zac talk about the state of working with JavaScript &#8212; including several popular JavaScript frameworks &#8212; and WordPress. They go through the pros and cons of using each one, what to watch out for when working with them and WordPress, and ways they think the process can improve.</span></p>\n<p><!--[if lt IE 9]><script>document.createElement(''audio'');</script><![endif]-->\n<a href="https://audio.simplecast.com/62575.mp3">https://audio.simplecast.com/62575.mp3</a><br />\n<a href="https://audio.simplecast.com/62575.mp3">Direct Download</a></p>\n<h3>Links</h3>\n<ul>\n<li><a href="https://javascriptforwp.com/">JavaScript for WP</a></li>\n<li><a href="https://facebook.github.io/react/">React</a></li>\n<li><a href="https://vuejs.org/">Vue</a></li>\n<li><a href="http://backbonejs.org/">Backbone</a></li>\n<li><a href="http://underscorejs.org/">Underscores</a></li>\n<li><a href="https://angularjs.org/">Angular</a></li>\n<li><a href="https://adayofrest.hm/boston-2017/">A Day of Rest</a></li>\n</ul>\n<h3>Sponsor: WP Migrate DB Pro</h3>\n<p><span>Today’s show is sponsored by</span><a href="https://deliciousbrains.com/"> <span>Delicious Brains</span></a><span>.</span><a href="https://deliciousbrains.com/wp-migrate-db-pro/"> <span>WP Migrate DB Pro</span></a> <span>makes moving and copying databases simple. They  also have an exciting new project for merging databases, called Mergebot. Go to</span><a href="https://mergebot.com/"> <span>Mergebot.com</span></a><span> for updates on that, and</span><a href="https://deliciousbrains.com/"> <span>deliciousbrains.com</span></a><span> for more information on WPMigrate DB Pro. Thanks to the team at Delicious Brains for being a Post Status partner.</span></p>\n<h3>Special Thanks: Bocoup</h3>\n<p><span>Special thanks to <a href="https://bocoup.com/">Bocoup</a> for allowing us to record this podcast episode in their office. Bocoup was a partner and workshop host for A Day of REST, and were incredibly hospitable. Checkout <a href="https://bocoup.com/">Bocoup</a> to learn more about how they embrace open source as a consulting agency.</span></p>";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:7:"pubDate";a:1:{i:0;a:5:{s:4:"data";s:31:"Mon, 13 Mar 2017 00:56:04 +0000";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}s:32:"http://purl.org/dc/elements/1.1/";a:1:{s:7:"creator";a:1:{i:0;a:5:{s:4:"data";s:14:"Katie Richards";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}}}}i:3;a:6:{s:4:"data";s:13:"\n	\n	\n	\n	\n	\n	\n";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";s:5:"child";a:2:{s:0:"";a:5:{s:5:"title";a:1:{i:0;a:5:{s:4:"data";s:74:"WPTavern: Hacker News Question: Developers with kids, how do you skill up?";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"guid";a:1:{i:0;a:5:{s:4:"data";s:29:"https://wptavern.com/?p=67259";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:4:"link";a:1:{i:0;a:5:{s:4:"data";s:82:"https://wptavern.com/hacker-news-question-developers-with-kids-how-do-you-skill-up";s:7:"attribs";a:0:{}s:8:"xml_base";s:0:"";s:17:"xml_base_explicit";b:0;s:8:"xml_lang";s:0:"";}}s:11:"description";a:1:{i:0;a:5:{s:4:"data";s:9488:"<a href="https://i0.wp.com/wptavern.com/wp-content/uploads/2016/03/child-theme.jpg?ssl=1"><img /></a>photo credit: <a href="https://stocksnap.io/photo/R0C7A5M4WB">Leeroy</a>\n<p>By now you&#8217;ve probably seen the viral <a href="https://twitter.com/JOE_co_uk/status/840165524038377472" target="_blank">clip</a> of a father getting interrupted by his children while giving a live interview on BBC. Working parents everywhere, especially remote workers, could identify with the humorous embarrassment of the situation. Even those who have had pets interrupt Skype calls know the feeling. You want to be thought of as a professional and taken seriously but little home office invaders have other plans.</p>\n<blockquote class="twitter-tweet">\n<p lang="en" dir="ltr">This BBC interview is amazing. Just wait until the mum rushes in&#8230; <img src="https://s.w.org/images/core/emoji/2.2.1/72x72/1f602.png" alt="', 'no'),
(1056, '_transient_timeout_feed_mod_d117b5738fbd35bd8c0391cda1f2b5d9', '1489537717', 'no'),
(1057, '_transient_feed_mod_d117b5738fbd35bd8c0391cda1f2b5d9', '1489494517', 'no'),
(1064, '_transient_timeout_dash_1cdc9b194b2615962b15195b3b6a2097', '1489537719', 'no'),
(1065, '_transient_dash_1cdc9b194b2615962b15195b3b6a2097', '<div class="rss-widget"><ul><li><a class=''rsswidget'' href=''https://wordpress.org/news/2017/03/wordpress-4-7-3-security-and-maintenance-release/''>WordPress 4.7.3 Security and Maintenance Release</a> <span class="rss-date">6 març 2017</span><div class="rssSummary">WordPress 4.7.3 is now available. This is a security release for all previous versions and we strongly encourage you to update your sites immediately. WordPress versions 4.7.2 and earlier are affected by six security issues: Cross-site scripting (XSS) via media file metadata.  Reported by Chris Andrè Dale, Yorick Koster, and Simon P. Briggs. Control characters can trick redirect [&hellip;]</div></li></ul></div><div class="rss-widget"><ul><li><a class=''rsswidget'' href=''https://wptavern.com/wordpress-com-updates-its-post-editor-with-a-distraction-free-interface''>WPTavern: WordPress.com Updates Its Post Editor With a Distraction-Free Interface</a></li><li><a class=''rsswidget'' href=''https://wptavern.com/john-maedas-2017-design-in-tech-report-puts-the-spotlight-on-inclusive-design''>WPTavern: John Maeda’s 2017 Design in Tech Report Puts the Spotlight on Inclusive Design</a></li><li><a class=''rsswidget'' href=''https://poststatus.com/javascript-frameworks-wordpress-context-draft-podcast/''>Post Status: JavaScript frameworks in a WordPress context — Draft podcast</a></li></ul></div><div class="rss-widget"><ul><li class="dashboard-news-plugin"><span>Extensió popular:</span> Ninja Forms&nbsp;<a href="plugin-install.php?tab=plugin-information&amp;plugin=ninja-forms&amp;_wpnonce=97416b1221&amp;TB_iframe=true&amp;width=600&amp;height=800" class="thickbox open-plugin-details-modal" aria-label="Instal·la Ninja Forms">(Instal·la)</a></li></ul></div>', 'no'),
(1066, '_transient_timeout__simple-calendar_feed_ids', '1490099319', 'no'),
(1067, '_transient__simple-calendar_feed_ids', 'a:1:{i:12;s:0:"";}', 'no'),
(1068, 'finished_splitting_shared_terms', '1', 'yes'),
(1069, 'site_icon', '0', 'yes'),
(1070, 'medium_large_size_w', '768', 'yes'),
(1071, 'medium_large_size_h', '0', 'yes'),
(1078, 'slideshow-jquery-image-gallery-updated-from-v2-1-20-to-v2-1-22', 'updated', 'no'),
(1079, 'slideshow-jquery-image-gallery-updated-from-v2-1-20-to-v2-1-23', 'updated', 'no'),
(1080, 'slideshow-jquery-image-gallery-updated-from-v2-1-23-to-v2-2-0', 'updated', 'no'),
(1081, 'slideshow-jquery-image-gallery-updated-from-v2-2-0-to-v2-2-8', 'updated', 'no'),
(1082, 'slideshow-jquery-image-gallery-updated-from-v2-2-8-to-v2-2-12', 'updated', 'no'),
(1083, 'slideshow-jquery-image-gallery-updated-from-v2-2-12-to-v2-2-16', 'updated', 'no'),
(1084, 'slideshow-jquery-image-gallery-updated-from-v2-2-16-to-v2-2-17', 'updated', 'no'),
(1085, 'slideshow-jquery-image-gallery-updated-from-v2-2-17-to-v2-2-20', 'updated', 'no'),
(1086, 'slideshow-jquery-image-gallery-plugin-version', '2.3.1', 'yes'),
(1087, 'widget_slideshowwidget', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(1088, 'widget_users_data_widget', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(1089, '_transient_timeout_dirsize_cache', '1489498364', 'no'),
(1090, '_transient_dirsize_cache', 'a:1:{s:35:"/dades/blocs/src/wp-content/uploads";a:1:{s:4:"size";i:0;}}', 'no'),
(1092, 'widget_a2a_share_save_widget', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(1093, 'widget_a2a_follow_widget', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(1096, 'simple_local_avatars', 'a:2:{s:4:"caps";i:0;s:4:"only";i:0;}', 'yes'),
(1099, 'xtecweekblog_default_msg', 'Des de XTECBlocs, el professorat i els centres podeu crear tants blocs com necessiteu i convidar a l''alumnat a participar-hi.', 'yes');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_postmeta`
--

CREATE TABLE IF NOT EXISTS `wp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

--
-- Bolcant dades de la taula `wp_postmeta`
--

INSERT INTO `wp_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(1, 2, '_wp_page_template', 'default'),
(2, 1, '_oembed_9898809c08baff43cc22eefcdcdff8dc', '<iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/34439974/content" scrolling="no" id="34439974" width="500" height="750" frameborder="0"></iframe><script type="text/javascript">          (function() { var scribd = document.createElement("script"); scribd.type = "text/javascript"; scribd.async = true; scribd.src = "https://www.scribd.com/javascripts/embed_code/inject.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(scribd, s); })()        </script>'),
(3, 1, '_oembed_time_9898809c08baff43cc22eefcdcdff8dc', '1424428104'),
(25, 12, 'gce_list_max_num', '7'),
(26, 12, 'gce_list_max_length', 'days'),
(29, 12, 'gce_feed_start_interval', 'months'),
(31, 12, 'gce_feed_end_interval', 'years'),
(44, 12, '_edit_lock', '1426070871:1'),
(41, 12, '_edit_last', '1'),
(73, 12, '_calendar_version', '3.0.0'),
(53, 12, '_calendar_view', 'a:1:{s:16:"default-calendar";s:4:"grid";}'),
(54, 12, '_default_calendar_list_range_type', 'daily'),
(55, 12, '_default_calendar_list_range_span', '7'),
(56, 12, '_calendar_begins', 'today'),
(57, 12, '_feed_earliest_event_date', 'months_before'),
(58, 12, '_feed_earliest_event_date_range', '1'),
(59, 12, '_feed_latest_event_date', 'years_after'),
(60, 12, '_feed_latest_event_date_range', '2'),
(61, 12, '_default_calendar_event_bubble_trigger', 'hover'),
(62, 12, '_default_calendar_expand_multi_day_events', 'yes'),
(63, 12, '_google_calendar_id', ''),
(64, 12, '_google_events_max_results', '2500'),
(65, 12, '_google_events_recurring', 'show'),
(66, 12, '_calendar_date_format_setting', 'use_site'),
(67, 12, '_calendar_time_format_setting', 'use_site'),
(68, 12, '_calendar_datetime_separator', '@'),
(69, 12, '_calendar_week_starts_on_setting', 'use_site'),
(70, 12, '_feed_cache_user_unit', '3600'),
(71, 12, '_feed_cache_user_amount', '12'),
(72, 12, '_feed_cache', '43200');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_posts`
--

CREATE TABLE IF NOT EXISTS `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(20) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `post_name` (`post_name`(191))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Bolcant dades de la taula `wp_posts`
--

INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(1, 1, '2012-12-19 13:05:13', '2012-12-19 13:05:13', 'Benvingut al WordPress. Aquest és el vostre primer article. Editeu-lo o suprimiu-lo ..... i comenceu a publicar!', 'Hola, món!', '', 'publish', 'open', 'open', '', 'hola-mon', '', '', '2012-12-19 13:05:13', '2012-12-19 13:05:13', '', 0, 'http://agora/blocs/?p=1', 0, 'post', '', 0),
(2, 1, '2012-12-19 13:05:13', '2012-12-19 13:05:13', 'Aquest és un exemple de pàgina. És diferent a un article perquè romandrà en un lloc i es mostrarà en la navegació del bloc (en la majoria d''aparences). Molta gent comença amb una pàgina "Quant a" que els presenta als visitants potencials del bloc. Es podria dir quelcom així: \n<blockquote>Hola a tothom! Treballo de missatger de dia, sóc aspirant a actor de nit, i aquest és el meu bloc. Visc a Barcelona, tinc un gosa meravellosa que es diu Lluna, i m''agraden les calçotades. (I quedar atrapat per la pluja.)</blockquote>\n\n... o quelcom així:\n\n<blockquote>La Companyia d''Adobs XYZ es va fundar el 1971, i ha estat proporcionant adobs de qualitat des de llavors. Situada en Gotham City, XYZ dóna feina a més de 2,000 persones i fa tot tipus de meravelloses tasques per a la comunitat de Gotham.</blockquote>\n \n\n \nCom a usuari nou del WordPress, heu d''anar al <a href="http://agora/blocs/wp-admin/">tauler</a> a esborrar aquesta pàgina i crear pàgines noves amb el vostre contingut. Que es diverteixin!', 'Pàgina d''exemple', '', 'publish', 'open', 'open', '', 'pagina-exemple', '', '', '2012-12-19 13:05:13', '2012-12-19 13:05:13', '', 0, 'http://agora/blocs/?page_id=2', 0, 'page', '', 0),
(12, 1, '2015-03-11 10:50:12', '2015-03-11 10:50:12', '<div class="gce-list-event gce-tooltip-event">[event-title]</div>\r\n[if-not-all-day]\r\n[if-single-day]<div><span>Quan:</span> [start-time]-[end-time]</div>[/if-single-day]\r\n[/if-not-all-day]\r\n[if-multi-day]<div>Del [start-date] fins al [end-date]</div>[/if-multi-day]\r\n[if-location]<div><span>On:</span> [location]</div>[/if-location]\r\n[if-description]<div>[description]</div>[/if-description]\r\n<div>[link newwindow="true"]Més detalls...[/link]</div>\r\n', '', '', 'publish', 'closed', 'closed', '', '12', '', '', '2015-03-11 10:50:12', '2015-03-11 10:50:12', '', 0, 'http://agora/blocs/?post_type=gce_feed&#038;p=12', 0, 'calendar', '', 0),
(20, 1, '2017-03-14 12:45:32', '0000-00-00 00:00:00', '', 'Esborrany automàtic', '', 'auto-draft', 'open', 'open', '', '', '', '', '2017-03-14 12:45:32', '0000-00-00 00:00:00', '', 0, 'http://agora/blocs/?p=20', 0, 'post', '', 0);

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_registration_log`
--

CREATE TABLE IF NOT EXISTS `wp_registration_log` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `IP` varchar(30) NOT NULL DEFAULT '',
  `blog_id` bigint(20) NOT NULL DEFAULT '0',
  `date_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `IP` (`IP`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Bolcant dades de la taula `wp_registration_log`
--

INSERT INTO `wp_registration_log` (`ID`, `email`, `IP`, `blog_id`, `date_registered`) VALUES
(1, 'admin@blocs.xtec.cat', '192.168.56.1', 2, '2012-12-20 08:27:07'),
(2, 'admin@blocs.xtec.cat', '192.168.56.1', 3, '2012-12-20 08:51:03'),
(3, 'admin@blocs.xtec.cat', '192.168.56.1', 4, '2012-12-20 09:20:20'),
(4, 'admin@blocs.xtec.cat', '192.168.56.1', 5, '2012-12-20 11:12:27'),
(5, 'admin@blocs.xtec.cat', '192.168.56.1', 6, '2012-12-20 11:15:28'),
(6, 'admin@blocs.xtec.cat', '192.168.56.1', 7, '2015-02-25 08:51:20'),
(7, 'admin@blocs.xtec.cat', '192.168.56.1', 8, '2015-02-25 08:52:42'),
(8, 'admin@blocs.xtec.cat', '192.168.56.1', 9, '2015-02-25 09:00:23'),
(9, 'admin@blocs.xtec.cat', '192.168.56.1', 10, '2015-02-25 09:11:50'),
(10, 'admin@blocs.xtec.cat', '192.168.56.1', 11, '2015-02-25 09:31:25'),
(11, 'admin@blocs.xtec.cat', '192.168.56.1', 12, '2015-02-25 11:43:43'),
(12, 'admin@blocs.xtec.cat', '192.168.56.1', 13, '2015-02-26 14:01:11'),
(13, 'admin@blocs.xtec.cat', '192.168.56.1', 14, '2015-02-27 07:55:50'),
(14, 'admin@blocs.xtec.cat', '192.168.56.1', 15, '2015-02-27 07:57:47'),
(15, 'admin@blocs.xtec.cat', '192.168.56.1', 16, '2015-02-27 07:58:34'),
(16, 'admin@blocs.xtec.cat', '192.168.56.1', 19, '2015-03-02 12:52:52'),
(17, 'admin@blocs.xtec.cat', '192.168.56.1', 20, '2015-03-11 07:30:56'),
(18, 'admin@blocs.xtec.cat', '192.168.56.1', 21, '2015-03-11 07:33:14'),
(19, 'admin@blocs.xtec.cat', '192.168.56.1', 22, '2015-03-11 07:35:00'),
(20, 'admin@blocs.xtec.cat', '192.168.56.1', 23, '2015-03-11 07:40:31'),
(21, 'admin@blocs.xtec.cat', '192.168.56.1', 24, '2015-03-11 08:16:59'),
(22, 'admin@blocs.xtec.cat', '192.168.56.1', 25, '2015-03-11 08:17:54'),
(23, 'admin@blocs.xtec.cat', '192.168.56.1', 26, '2015-03-11 08:31:52'),
(24, 'admin@blocs.xtec.cat', '192.168.56.1', 27, '2015-03-11 08:36:25'),
(25, 'admin@blocs.xtec.cat', '192.168.56.1', 28, '2015-03-11 08:39:23'),
(26, 'admin@blocs.xtec.cat', '192.168.56.1', 29, '2015-03-11 08:57:32'),
(27, 'admin@blocs.xtec.cat', '192.168.56.1', 30, '2015-03-11 09:06:26'),
(28, 'admin@blocs.xtec.cat', '192.168.56.1', 31, '2015-03-12 08:41:30'),
(29, 'admin@blocs.xtec.cat', '192.168.56.1', 32, '2015-03-13 12:11:24'),
(30, 'admin@blocs.xtec.cat', '192.168.56.1', 33, '2015-03-13 12:13:23'),
(31, 'admin@blocs.xtec.cat', '192.168.56.1', 34, '2015-03-13 12:16:55'),
(32, 'admin@blocs.xtec.cat', '192.168.56.1', 35, '2015-03-13 12:17:40'),
(33, 'admin@blocs.xtec.cat', '192.168.56.1', 36, '2015-03-13 12:25:45'),
(34, 'admin@blocs.xtec.cat', '192.168.56.1', 37, '2015-03-17 08:35:41'),
(35, 'admin@blocs.xtec.cat', '192.168.56.1', 38, '2015-03-17 08:38:38'),
(36, 'admin@blocs.xtec.cat', '192.168.56.1', 39, '2015-03-17 08:44:06'),
(37, 'admin@blocs.xtec.cat', '192.168.56.1', 40, '2015-03-17 08:48:49'),
(38, 'admin@blocs.xtec.cat', '192.168.56.1', 41, '2015-03-17 08:50:23'),
(39, 'admin@blocs.xtec.cat', '192.168.56.1', 42, '2015-03-17 08:53:28'),
(40, 'admin@blocs.xtec.cat', '192.168.56.1', 43, '2015-03-17 08:55:47'),
(41, 'admin@blocs.xtec.cat', '192.168.56.1', 45, '2015-03-17 09:12:23'),
(42, 'admin@blocs.xtec.cat', '192.168.56.1', 46, '2015-03-17 09:14:03'),
(43, 'admin@blocs.xtec.cat', '192.168.56.1', 47, '2015-03-17 09:17:21'),
(44, 'admin@blocs.xtec.cat', '192.168.56.1', 48, '2015-03-17 09:18:15'),
(45, 'admin@blocs.xtec.cat', '192.168.56.1', 49, '2015-03-17 09:19:57'),
(46, 'admin@blocs.xtec.cat', '192.168.56.1', 50, '2015-03-17 09:40:43'),
(47, 'admin@blocs.xtec.cat', '192.168.56.1', 51, '2015-03-19 09:35:06'),
(48, 'admin@blocs.xtec.cat', '192.168.56.1', 52, '2015-04-02 10:18:15'),
(49, 'admin@blocs.xtec.cat', '192.168.56.1', 53, '2015-04-13 14:22:41'),
(50, 'admin@blocs.xtec.cat', '192.168.56.1', 54, '2015-04-13 14:33:02'),
(51, 'admin@blocs.xtec.cat', '192.168.56.1', 55, '2015-04-13 14:50:13'),
(52, 'admin@blocs.xtec.cat', '192.168.56.1', 56, '2015-04-17 11:49:17');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_requests`
--

CREATE TABLE IF NOT EXISTS `wp_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` tinyint(4) NOT NULL DEFAULT '1',
  `blog_id` int(11) NOT NULL DEFAULT '0',
  `request_type_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `time_creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_edition` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  `response` text NOT NULL,
  `priv_notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `blog_id` (`blog_id`),
  KEY `request_type_id` (`request_type_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_request_types`
--

CREATE TABLE IF NOT EXISTS `wp_request_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(200) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `comments_text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_search`
--

CREATE TABLE IF NOT EXISTS `wp_search` (
  `blogid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`blogid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Bolcant dades de la taula `wp_search`
--

INSERT INTO `wp_search` (`blogid`, `name`, `description`, `domain`, `path`) VALUES
(4, 'Bloc dels cargols', 'Un altre bloc XTEC Blocs ', 'agora', '/blocs/elscargols/'),
(5, 'Bloc de les tortugues', 'Un altre bloc XTEC Blocs', 'agora', '/blocs/lestortugues/'),
(6, 'Blocs dels pingüins', 'Un altre bloc XTEC Blocs ', 'agora', '/blocs/elspinguins/');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_signups`
--

CREATE TABLE IF NOT EXISTS `wp_signups` (
  `signup_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  `title` longtext NOT NULL,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `activation_key` varchar(50) NOT NULL DEFAULT '',
  `meta` longtext,
  PRIMARY KEY (`signup_id`),
  KEY `activation_key` (`activation_key`),
  KEY `user_email` (`user_email`),
  KEY `user_login_email` (`user_login`,`user_email`),
  KEY `domain_path` (`domain`,`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Bolcant dades de la taula `wp_signups`
--

INSERT INTO `wp_signups` (`signup_id`, `domain`, `path`, `title`, `user_login`, `user_email`, `registered`, `activated`, `active`, `activation_key`, `meta`) VALUES
(1, '', '', '', 'prova1', 'saved1_3@hotmail.com', '2015-02-20 10:08:35', '0000-00-00 00:00:00', 0, '2fd536305e1ec008', 'a:2:{s:11:"add_to_blog";s:1:"3";s:8:"new_role";s:10:"subscriber";}'),
(2, '', '', '', 'nacho', 'ignacio.benito.abejaro@upcnet.es', '2015-03-13 11:24:53', '0000-00-00 00:00:00', 0, 'b04784f68a359dac', 'a:2:{s:11:"add_to_blog";s:1:"1";s:8:"new_role";s:10:"subscriber";}'),
(4, '', '', '', 'victor', 'Saved1.3@gmail.com', '2015-03-27 07:41:41', '0000-00-00 00:00:00', 0, 'adad29c41b16ed92', 'a:2:{s:11:"add_to_blog";s:1:"1";s:8:"new_role";s:10:"subscriber";}'),
(5, '', '', '', 'victore', 'Saved1.3@gmail.com', '2015-03-27 07:41:51', '0000-00-00 00:00:00', 0, '2f7a33cfe34fbba8', 'a:2:{s:11:"add_to_blog";s:1:"1";s:8:"new_role";s:10:"subscriber";}'),
(6, '', '', '', 'victore', 'Saved1.3@gmail.com', '2015-03-27 07:42:13', '0000-00-00 00:00:00', 0, '05807348a80e6ae5', 'a:2:{s:11:"add_to_blog";s:1:"1";s:8:"new_role";s:10:"subscriber";}'),
(7, '', '', '', 'victore', 'Saved1.3@gmail.com', '2015-03-27 07:42:26', '0000-00-00 00:00:00', 0, 'da330ee3750cb638', 'a:2:{s:11:"add_to_blog";s:1:"1";s:8:"new_role";s:10:"subscriber";}'),
(8, '', '', '', 'victore', 'Saved1.3@gmail.com', '2015-03-27 07:42:43', '0000-00-00 00:00:00', 0, 'e64debbde9969568', 'a:2:{s:11:"add_to_blog";s:1:"1";s:8:"new_role";s:10:"subscriber";}'),
(9, '', '', '', 'est_colex', 'est_colex@blocs.xtec.cat', '2015-04-20 12:17:51', '0000-00-00 00:00:00', 0, 'e96073fab7f0c33c', 'a:2:{s:11:"add_to_blog";s:1:"3";s:8:"new_role";s:10:"subscriber";}');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_site`
--

CREATE TABLE IF NOT EXISTS `wp_site` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`,`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Bolcant dades de la taula `wp_site`
--

INSERT INTO `wp_site` (`id`, `domain`, `path`) VALUES
(1, 'agora', '/blocs/');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_sitemeta`
--

CREATE TABLE IF NOT EXISTS `wp_sitemeta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `meta_key` (`meta_key`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=664 ;

--
-- Bolcant dades de la taula `wp_sitemeta`
--

INSERT INTO `wp_sitemeta` (`meta_id`, `site_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'site_name', 'XTECBlocs'),
(2, 1, 'admin_email', 'admin@blocs.xtec.cat'),
(3, 1, 'admin_user_id', '1'),
(4, 1, 'registration', 'blog'),
(5, 1, 'upload_filetypes', 'jpg jpeg png gif mp3 mov avi wmv midi mid pdf css odt doc docx ppt pptx swf flv'),
(6, 1, 'blog_upload_space', '70'),
(7, 1, 'fileupload_maxk', '4000'),
(8, 1, 'site_admins', 'a:1:{i:0;s:5:"admin";}'),
(9, 1, 'allowedthemes', 'a:14:{s:18:"classic-chalkboard";b:1;s:8:"delicacy";b:1;s:7:"freshy2";b:1;s:8:"mystique";b:1;s:6:"reddle";b:1;s:12:"twentyeleven";b:1;s:9:"twentyten";b:1;s:12:"twentytwelve";b:1;s:9:"xtec-v1.1";b:1;s:8:"fukasawa";b:1;s:13:"twentyfifteen";b:1;s:14:"twentyfourteen";b:1;s:13:"twentysixteen";b:1;s:14:"twentythirteen";b:1;}'),
(10, 1, 'illegal_names', 'a:62:{i:0;s:3:"www";i:1;s:3:"web";i:2;s:4:"root";i:3;s:5:"admin";i:4;s:4:"main";i:5;s:6:"invite";i:6;s:13:"administrator";i:7;s:12:"Audiovisuals";i:8;s:4:"xtec";i:9;s:10:"xtecràdio";i:10;s:10:"xteccinema";i:11;s:6:"cinema";i:12;s:4:"clic";i:13;s:5:"jclic";i:14;s:2:"qv";i:15;s:8:"quaderns";i:16;s:16:"quadernsvirtuals";i:17;s:7:"quadern";i:18;s:14:"quadernvirtual";i:19;s:5:"colex";i:20;s:6:"collex";i:21;s:8:"intraweb";i:22;s:8:"intranet";i:23;s:3:"web";i:24;s:6:"linkat";i:25;s:6:"lincat";i:26;s:5:"linux";i:27;s:8:"gnulinux";i:28;s:4:"bloc";i:29;s:5:"blocs";i:30;s:4:"blog";i:31;s:5:"blogs";i:32;s:5:"forum";i:33;s:6:"forums";i:34;s:6:"moodle";i:35;s:5:"agora";i:36;s:10:"xtecmoodle";i:37;s:9:"xtecblocs";i:38;s:9:"xtecforum";i:39;s:6:"edu365";i:40;s:4:"smav";i:41;s:9:"videoteca";i:42;s:3:"tic";i:43;s:3:"tac";i:44;s:7:"diedrom";i:45;s:7:"isodrom";i:46;s:12:"xtecwebquest";i:47;s:7:"caspian";i:48;s:15:"caspianlearning";i:49;s:6:"slxtec";i:50;s:16:"gustperlaparaula";i:51;s:5:"agora";i:52;s:12:"prestatgeria";i:53;s:15:"catala2allengua";i:54;s:5:"gepse";i:55;s:11:"manteniment";i:56;s:6:"suport";i:57;s:5:"heura";i:58;s:10:"equipament";i:59;s:14:"paisoscatalans";i:60;s:16:"dominilinguistic";i:61;s:14:"blocs_formacio";}'),
(11, 1, 'wpmu_upgrade_site', '36686'),
(12, 1, 'welcome_email', 'Blocaire,\r\n\r\nEl nou bloc SITE_NAME s''ha configurat correctament en:\r\nBLOG_URL\r\n\r\nPodeu iniciar sessió en el compte d''administrador amb la següent informació:\r\nNom d''usuari: USERNAME\r\nContrasenya: PASSWORD\r\nEntreu aquí: BLOG_URLwp-login.php\r\n\r\nEsperem que gaudiu del vostre nou bloc.\r\nGràcies!\r\n\r\n--L''equip @ SITE_NAME'),
(13, 1, 'first_post', 'Benvingut a <a href="SITE_URL">SITE_NAME</a>. Aquest és el vostre primer article. Editeu-lo o suprimiu-lo, aleshores comenceu a publicar!'),
(14, 1, 'siteurl', 'http://agora/blocs/'),
(15, 1, 'add_new_users', '0'),
(16, 1, 'upload_space_check_disabled', '0'),
(17, 1, 'subdomain_install', '0'),
(18, 1, 'global_terms_enabled', '0'),
(576, 1, 'bwp_capt_general', 'a:18:{s:12:"input_pubkey";s:40:"6LdeRAUTAAAAAElOIZz-mWS21zDs6pe43Uhg4Btg";s:12:"input_prikey";s:40:"6LdeRAUTAAAAADdO3-Odt7C097AzBOMHGO1I6zeL";s:11:"input_error";s:80:"<strong>ERROR:</strong> Incorrect or empty reCAPTCHA response, please try again.";s:10:"input_back";s:127:"Error: Incorrect or empty reCAPTCHA response, please click the back button on your browser''s toolbar or click on %s to go back.";s:14:"input_approved";s:1:"1";s:14:"enable_comment";s:3:"yes";s:19:"enable_registration";s:0:"";s:12:"enable_login";s:0:"";s:14:"enable_akismet";s:0:"";s:15:"use_global_keys";s:3:"yes";s:10:"select_cap";s:14:"manage_options";s:14:"select_cf7_tag";s:9:"recaptcha";s:15:"select_response";s:8:"redirect";s:15:"select_position";s:19:"after_comment_field";s:20:"select_akismet_react";s:4:"hold";s:15:"hide_registered";s:3:"yes";s:8:"hide_cap";s:0:"";s:13:"hide_approved";s:0:"";}'),
(363, 1, 'secure_auth_key', '4=S1AH}CYV-to!af9q)N@(ag=@jO;YDom,3#qr^KCgIFAZIXsV@9-J*1j]x XH4+'),
(364, 1, 'secure_auth_salt', 'pA~0{ke5_WMQi>ti:vYbxlu-}[r|e/BDJ+G4]SIs&gyXW~>0$_n>Lj7={NH>Lw)v'),
(365, 1, 'logged_in_key', 'IodwAlt~[O7(CLXsJ9.q</vK7T_#O`)j&blr{I9,~H(&#y|HKQRpdw12u>K((kR>'),
(74, 1, 'auto_core_update_notified', 'a:4:{s:4:"type";s:6:"manual";s:5:"email";s:20:"admin@blocs.xtec.cat";s:7:"version";s:5:"4.0.1";s:9:"timestamp";i:1424257160;}'),
(637, 1, '_site_transient_timeout_theme_roots', '1489496248'),
(638, 1, '_site_transient_theme_roots', 'a:17:{s:18:"classic-chalkboard";s:7:"/themes";s:8:"delicacy";s:7:"/themes";s:7:"freshy2";s:7:"/themes";s:8:"fukasawa";s:7:"/themes";s:8:"mystique";s:7:"/themes";s:6:"reddle";s:7:"/themes";s:12:"twentyeleven";s:7:"/themes";s:13:"twentyfifteen";s:7:"/themes";s:14:"twentyfourteen";s:7:"/themes";s:13:"twentysixteen";s:7:"/themes";s:9:"twentyten";s:7:"/themes";s:14:"twentythirteen";s:7:"/themes";s:12:"twentytwelve";s:7:"/themes";s:9:"xtec-v1.1";s:7:"/themes";s:13:"xtec898encurs";s:7:"/themes";s:25:"xtecblocsdefault-formacio";s:7:"/themes";s:16:"xtecblocsdefault";s:7:"/themes";}'),
(366, 1, 'logged_in_salt', 'IY(w@%y6uTpAThY9~;/x=IjGrv)^f(YnAH5)a7HoHu5mDLCwYdmx[H>#<(PITZ;>'),
(367, 1, 'nonce_key', 'CO67QeHfvTK3o`8ioxAmy[b/J;dAp#$aR*D0X}AqundITH _T&jfK=_JkdW7i~@R'),
(368, 1, 'nonce_salt', 'Zy3dj(E?Rt($d;55WR:<QfFl3vNU>enu=;Lr7@ Sa9<!2[t0N,UZU_x9?zaxKExp'),
(24, 1, 'blog_count', '4'),
(25, 1, 'user_count', '2'),
(26, 1, 'can_compress_scripts', '1'),
(47, 1, 'initial_db_version', '17516'),
(31, 1, 'active_sitewide_plugins', 'a:30:{s:37:"blogger-importer/blogger-importer.php";i:1389360202;s:43:"google-analyticator/google-analyticator.php";i:1389360204;s:29:"link-manager/link-manager.php";i:1389360209;s:43:"multisite-plugin-manager/plugin-manager.php";i:1389360211;s:25:"slideshare/slideshare.php";i:1389360218;s:37:"tinymce-advanced/tinymce-advanced.php";i:1389360220;s:49:"vipers-video-quicktags/vipers-video-quicktags.php";i:1389360222;s:41:"wordpress-importer/wordpress-importer.php";i:1389360225;s:27:"wp-super-cache/wp-cache.php";i:1389360229;s:21:"xtec-api/xtec-api.php";i:1389360231;s:37:"xtec-descriptors/xtec-descriptors.php";i:1389360233;s:33:"xtec-favorites/xtec-favorites.php";i:1389360236;s:41:"xtec-lastest-posts/xtec-lastest-posts.php";i:1389360238;s:35:"xtec-ldap-login/xtec-ldap-login.php";i:1389360240;s:37:"xtec-link-player/xtec-link-player.php";i:1389360242;s:23:"xtec-mail/xtec-mail.php";i:1389360244;s:37:"xtec-maintenance/xtec-maintenance.php";i:1389360246;s:31:"xtec-settings/xtec-settings.php";i:1389360250;s:27:"xtec-signup/xtec-signup.php";i:1389360252;s:25:"xtec-users/xtec-users.php";i:1389360254;s:45:"simple-local-avatars/simple-local-avatars.php";i:1426749718;s:29:"wp-recaptcha/wp-recaptcha.php";i:1429014106;s:23:"anti-spam/anti-spam.php";i:1429023307;s:49:"google-calendar-events/google-calendar-events.php";i:1489494617;s:57:"multisite-clone-duplicator/multisite-clone-duplicator.php";i:1489494637;s:34:"scribd-doc-embedder/scribd_doc.php";i:1489494653;s:44:"slideshow-jquery-image-gallery/slideshow.php";i:1489494708;s:41:"wordpress-php-info/wordpress-php-info.php";i:1489494734;s:35:"xtec-ms-manager/xtec-ms-manager.php";i:1489494744;s:47:"xtec-widget-data-users/xtec-class-data-user.php";i:1489494763;}'),
(32, 1, 'recaptcha_options', 'a:14:{s:10:"public_key";s:40:"6LdYmdoSAAAAAJI5whFCwEiXBik7H6CwBMptVJ1O";s:11:"private_key";s:40:"6LdYmdoSAAAAAIgSS-jRH-UB65b1YdWAIwlk-VZk";s:16:"show_in_comments";i:1;s:27:"bypass_for_registered_users";i:1;s:20:"minimum_bypass_level";s:4:"read";s:14:"comments_theme";s:3:"red";s:18:"comments_tab_index";s:1:"5";s:20:"show_in_registration";i:1;s:18:"registration_theme";s:3:"red";s:22:"registration_tab_index";s:2:"30";s:18:"recaptcha_language";s:2:"es";s:16:"xhtml_compliance";i:0;s:17:"no_response_error";s:58:"<strong>ERROR</strong>: Please fill in the reCAPTCHA form.";s:24:"incorrect_response_error";s:62:"<strong>ERROR</strong>: That reCAPTCHA response was incorrect.";}'),
(53, 1, 'pm_user_control_list', 'a:1:{i:0;s:42:"wordpress-social-login/wp-social-login.php";}'),
(54, 1, 'pm_auto_activate_list', 'a:1:{i:0;s:25:"add-to-any/add-to-any.php";}'),
(650, 1, '_site_transient_available_translations', 'a:81:{s:3:"ary";a:8:{s:8:"language";s:3:"ary";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-09-03 15:24:06";s:12:"english_name";s:15:"Moroccan Arabic";s:11:"native_name";s:31:"العربية المغربية";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.5.7/ary.zip";s:3:"iso";a:2:{i:1;s:2:"ar";i:3;s:3:"ary";}s:7:"strings";a:1:{s:8:"continue";s:16:"المتابعة";}}s:2:"ar";a:8:{s:8:"language";s:2:"ar";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-14 10:53:34";s:12:"english_name";s:6:"Arabic";s:11:"native_name";s:14:"العربية";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/ar.zip";s:3:"iso";a:2:{i:1;s:2:"ar";i:2;s:3:"ara";}s:7:"strings";a:1:{s:8:"continue";s:16:"المتابعة";}}s:2:"az";a:8:{s:8:"language";s:2:"az";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-18 20:18:13";s:12:"english_name";s:11:"Azerbaijani";s:11:"native_name";s:16:"Azərbaycan dili";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/az.zip";s:3:"iso";a:2:{i:1;s:2:"az";i:2;s:3:"aze";}s:7:"strings";a:1:{s:8:"continue";s:5:"Davam";}}s:3:"azb";a:8:{s:8:"language";s:3:"azb";s:7:"version";s:5:"4.4.2";s:7:"updated";s:19:"2015-12-11 22:42:10";s:12:"english_name";s:17:"South Azerbaijani";s:11:"native_name";s:29:"گؤنئی آذربایجان";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.4.2/azb.zip";s:3:"iso";a:2:{i:1;s:2:"az";i:3;s:3:"azb";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"bg_BG";a:8:{s:8:"language";s:5:"bg_BG";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-27 08:19:49";s:12:"english_name";s:9:"Bulgarian";s:11:"native_name";s:18:"Български";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/bg_BG.zip";s:3:"iso";a:2:{i:1;s:2:"bg";i:2;s:3:"bul";}s:7:"strings";a:1:{s:8:"continue";s:12:"Напред";}}s:5:"bn_BD";a:8:{s:8:"language";s:5:"bn_BD";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-14 05:03:35";s:12:"english_name";s:7:"Bengali";s:11:"native_name";s:15:"বাংলা";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/bn_BD.zip";s:3:"iso";a:1:{i:1;s:2:"bn";}s:7:"strings";a:1:{s:8:"continue";s:23:"এগিয়ে চল.";}}s:5:"bs_BA";a:8:{s:8:"language";s:5:"bs_BA";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-04-19 23:16:37";s:12:"english_name";s:7:"Bosnian";s:11:"native_name";s:8:"Bosanski";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/bs_BA.zip";s:3:"iso";a:2:{i:1;s:2:"bs";i:2;s:3:"bos";}s:7:"strings";a:1:{s:8:"continue";s:7:"Nastavi";}}s:2:"ca";a:8:{s:8:"language";s:2:"ca";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-11 20:22:42";s:12:"english_name";s:7:"Catalan";s:11:"native_name";s:7:"Català";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/ca.zip";s:3:"iso";a:2:{i:1;s:2:"ca";i:2;s:3:"cat";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continua";}}s:3:"ceb";a:8:{s:8:"language";s:3:"ceb";s:7:"version";s:5:"4.4.7";s:7:"updated";s:19:"2016-02-16 15:34:57";s:12:"english_name";s:7:"Cebuano";s:11:"native_name";s:7:"Cebuano";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.4.7/ceb.zip";s:3:"iso";a:2:{i:2;s:3:"ceb";i:3;s:3:"ceb";}s:7:"strings";a:1:{s:8:"continue";s:7:"Padayun";}}s:5:"cs_CZ";a:8:{s:8:"language";s:5:"cs_CZ";s:7:"version";s:5:"4.4.2";s:7:"updated";s:19:"2016-02-11 18:32:36";s:12:"english_name";s:5:"Czech";s:11:"native_name";s:12:"Čeština‎";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.4.2/cs_CZ.zip";s:3:"iso";a:2:{i:1;s:2:"cs";i:2;s:3:"ces";}s:7:"strings";a:1:{s:8:"continue";s:11:"Pokračovat";}}s:2:"cy";a:8:{s:8:"language";s:2:"cy";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:02:40";s:12:"english_name";s:5:"Welsh";s:11:"native_name";s:7:"Cymraeg";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/cy.zip";s:3:"iso";a:2:{i:1;s:2:"cy";i:2;s:3:"cym";}s:7:"strings";a:1:{s:8:"continue";s:6:"Parhau";}}s:5:"da_DK";a:8:{s:8:"language";s:5:"da_DK";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-28 11:16:44";s:12:"english_name";s:6:"Danish";s:11:"native_name";s:5:"Dansk";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/da_DK.zip";s:3:"iso";a:2:{i:1;s:2:"da";i:2;s:3:"dan";}s:7:"strings";a:1:{s:8:"continue";s:12:"Forts&#230;t";}}s:14:"de_CH_informal";a:8:{s:8:"language";s:14:"de_CH_informal";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-04-12 20:03:25";s:12:"english_name";s:30:"German (Switzerland, Informal)";s:11:"native_name";s:21:"Deutsch (Schweiz, Du)";s:7:"package";s:73:"https://downloads.wordpress.org/translation/core/4.5.7/de_CH_informal.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:5:"de_DE";a:8:{s:8:"language";s:5:"de_DE";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-10-27 04:34:07";s:12:"english_name";s:6:"German";s:11:"native_name";s:7:"Deutsch";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/de_DE.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:12:"de_DE_formal";a:8:{s:8:"language";s:12:"de_DE_formal";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-30 18:59:13";s:12:"english_name";s:15:"German (Formal)";s:11:"native_name";s:13:"Deutsch (Sie)";s:7:"package";s:71:"https://downloads.wordpress.org/translation/core/4.5.7/de_DE_formal.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:5:"de_CH";a:8:{s:8:"language";s:5:"de_CH";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-26 10:08:23";s:12:"english_name";s:20:"German (Switzerland)";s:11:"native_name";s:17:"Deutsch (Schweiz)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/de_CH.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:2:"el";a:8:{s:8:"language";s:2:"el";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-02-27 10:29:58";s:12:"english_name";s:5:"Greek";s:11:"native_name";s:16:"Ελληνικά";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/el.zip";s:3:"iso";a:2:{i:1;s:2:"el";i:2;s:3:"ell";}s:7:"strings";a:1:{s:8:"continue";s:16:"Συνέχεια";}}s:5:"en_GB";a:8:{s:8:"language";s:5:"en_GB";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-21 22:23:41";s:12:"english_name";s:12:"English (UK)";s:11:"native_name";s:12:"English (UK)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/en_GB.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_CA";a:8:{s:8:"language";s:5:"en_CA";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-26 19:24:51";s:12:"english_name";s:16:"English (Canada)";s:11:"native_name";s:16:"English (Canada)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/en_CA.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_NZ";a:8:{s:8:"language";s:5:"en_NZ";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-21 22:55:40";s:12:"english_name";s:21:"English (New Zealand)";s:11:"native_name";s:21:"English (New Zealand)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/en_NZ.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_ZA";a:8:{s:8:"language";s:5:"en_ZA";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-04-28 11:29:02";s:12:"english_name";s:22:"English (South Africa)";s:11:"native_name";s:22:"English (South Africa)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/en_ZA.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_AU";a:8:{s:8:"language";s:5:"en_AU";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-21 21:28:52";s:12:"english_name";s:19:"English (Australia)";s:11:"native_name";s:19:"English (Australia)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/en_AU.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:2:"eo";a:8:{s:8:"language";s:2:"eo";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-29 13:59:02";s:12:"english_name";s:9:"Esperanto";s:11:"native_name";s:9:"Esperanto";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/eo.zip";s:3:"iso";a:2:{i:1;s:2:"eo";i:2;s:3:"epo";}s:7:"strings";a:1:{s:8:"continue";s:8:"Daŭrigi";}}s:5:"es_VE";a:8:{s:8:"language";s:5:"es_VE";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-23 19:36:14";s:12:"english_name";s:19:"Spanish (Venezuela)";s:11:"native_name";s:21:"Español de Venezuela";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_VE.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_AR";a:8:{s:8:"language";s:5:"es_AR";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-15 15:42:15";s:12:"english_name";s:19:"Spanish (Argentina)";s:11:"native_name";s:21:"Español de Argentina";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_AR.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_ES";a:8:{s:8:"language";s:5:"es_ES";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:02:40";s:12:"english_name";s:15:"Spanish (Spain)";s:11:"native_name";s:8:"Español";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_ES.zip";s:3:"iso";a:1:{i:1;s:2:"es";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_CO";a:8:{s:8:"language";s:5:"es_CO";s:7:"version";s:6:"4.3-RC";s:7:"updated";s:19:"2015-08-04 06:10:33";s:12:"english_name";s:18:"Spanish (Colombia)";s:11:"native_name";s:20:"Español de Colombia";s:7:"package";s:65:"https://downloads.wordpress.org/translation/core/4.3-RC/es_CO.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_PE";a:8:{s:8:"language";s:5:"es_PE";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-04-16 17:35:43";s:12:"english_name";s:14:"Spanish (Peru)";s:11:"native_name";s:17:"Español de Perú";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_PE.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_GT";a:8:{s:8:"language";s:5:"es_GT";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-05 22:16:54";s:12:"english_name";s:19:"Spanish (Guatemala)";s:11:"native_name";s:21:"Español de Guatemala";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_GT.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_MX";a:8:{s:8:"language";s:5:"es_MX";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-29 15:10:17";s:12:"english_name";s:16:"Spanish (Mexico)";s:11:"native_name";s:19:"Español de México";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_MX.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_CL";a:8:{s:8:"language";s:5:"es_CL";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-01 14:32:46";s:12:"english_name";s:15:"Spanish (Chile)";s:11:"native_name";s:17:"Español de Chile";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_CL.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:2:"et";a:8:{s:8:"language";s:2:"et";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:02:40";s:12:"english_name";s:8:"Estonian";s:11:"native_name";s:5:"Eesti";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/et.zip";s:3:"iso";a:2:{i:1;s:2:"et";i:2;s:3:"est";}s:7:"strings";a:1:{s:8:"continue";s:6:"Jätka";}}s:2:"eu";a:8:{s:8:"language";s:2:"eu";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-30 11:41:42";s:12:"english_name";s:6:"Basque";s:11:"native_name";s:7:"Euskara";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/eu.zip";s:3:"iso";a:2:{i:1;s:2:"eu";i:2;s:3:"eus";}s:7:"strings";a:1:{s:8:"continue";s:8:"Jarraitu";}}s:5:"fa_IR";a:8:{s:8:"language";s:5:"fa_IR";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-20 14:58:27";s:12:"english_name";s:7:"Persian";s:11:"native_name";s:10:"فارسی";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/fa_IR.zip";s:3:"iso";a:2:{i:1;s:2:"fa";i:2;s:3:"fas";}s:7:"strings";a:1:{s:8:"continue";s:10:"ادامه";}}s:2:"fi";a:8:{s:8:"language";s:2:"fi";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-26 09:08:24";s:12:"english_name";s:7:"Finnish";s:11:"native_name";s:5:"Suomi";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/fi.zip";s:3:"iso";a:2:{i:1;s:2:"fi";i:2;s:3:"fin";}s:7:"strings";a:1:{s:8:"continue";s:5:"Jatka";}}s:5:"fr_FR";a:8:{s:8:"language";s:5:"fr_FR";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-29 13:30:07";s:12:"english_name";s:15:"French (France)";s:11:"native_name";s:9:"Français";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/fr_FR.zip";s:3:"iso";a:1:{i:1;s:2:"fr";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuer";}}s:5:"fr_BE";a:8:{s:8:"language";s:5:"fr_BE";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-22 06:33:34";s:12:"english_name";s:16:"French (Belgium)";s:11:"native_name";s:21:"Français de Belgique";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/fr_BE.zip";s:3:"iso";a:2:{i:1;s:2:"fr";i:2;s:3:"fra";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuer";}}s:5:"fr_CA";a:8:{s:8:"language";s:5:"fr_CA";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-13 12:55:08";s:12:"english_name";s:15:"French (Canada)";s:11:"native_name";s:19:"Français du Canada";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/fr_CA.zip";s:3:"iso";a:2:{i:1;s:2:"fr";i:2;s:3:"fra";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuer";}}s:2:"gd";a:8:{s:8:"language";s:2:"gd";s:7:"version";s:5:"4.3.9";s:7:"updated";s:19:"2015-09-24 15:25:30";s:12:"english_name";s:15:"Scottish Gaelic";s:11:"native_name";s:9:"Gàidhlig";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.3.9/gd.zip";s:3:"iso";a:3:{i:1;s:2:"gd";i:2;s:3:"gla";i:3;s:3:"gla";}s:7:"strings";a:1:{s:8:"continue";s:15:"Lean air adhart";}}s:5:"gl_ES";a:8:{s:8:"language";s:5:"gl_ES";s:7:"version";s:5:"4.5.6";s:7:"updated";s:19:"2016-06-28 21:28:18";s:12:"english_name";s:8:"Galician";s:11:"native_name";s:6:"Galego";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.6/gl_ES.zip";s:3:"iso";a:2:{i:1;s:2:"gl";i:2;s:3:"glg";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:3:"haz";a:8:{s:8:"language";s:3:"haz";s:7:"version";s:5:"4.4.2";s:7:"updated";s:19:"2015-12-05 00:59:09";s:12:"english_name";s:8:"Hazaragi";s:11:"native_name";s:15:"هزاره گی";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.4.2/haz.zip";s:3:"iso";a:1:{i:3;s:3:"haz";}s:7:"strings";a:1:{s:8:"continue";s:10:"ادامه";}}s:5:"he_IL";a:8:{s:8:"language";s:5:"he_IL";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-26 15:19:37";s:12:"english_name";s:6:"Hebrew";s:11:"native_name";s:16:"עִבְרִית";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/he_IL.zip";s:3:"iso";a:1:{i:1;s:2:"he";}s:7:"strings";a:1:{s:8:"continue";s:8:"המשך";}}s:5:"hi_IN";a:8:{s:8:"language";s:5:"hi_IN";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:02:41";s:12:"english_name";s:5:"Hindi";s:11:"native_name";s:18:"हिन्दी";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/hi_IN.zip";s:3:"iso";a:2:{i:1;s:2:"hi";i:2;s:3:"hin";}s:7:"strings";a:1:{s:8:"continue";s:12:"जारी";}}s:2:"hr";a:8:{s:8:"language";s:2:"hr";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-11 16:18:13";s:12:"english_name";s:8:"Croatian";s:11:"native_name";s:8:"Hrvatski";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/hr.zip";s:3:"iso";a:2:{i:1;s:2:"hr";i:2;s:3:"hrv";}s:7:"strings";a:1:{s:8:"continue";s:7:"Nastavi";}}s:5:"hu_HU";a:8:{s:8:"language";s:5:"hu_HU";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-21 18:58:51";s:12:"english_name";s:9:"Hungarian";s:11:"native_name";s:6:"Magyar";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/hu_HU.zip";s:3:"iso";a:2:{i:1;s:2:"hu";i:2;s:3:"hun";}s:7:"strings";a:1:{s:8:"continue";s:10:"Folytatás";}}s:2:"hy";a:8:{s:8:"language";s:2:"hy";s:7:"version";s:5:"4.4.2";s:7:"updated";s:19:"2016-02-04 07:13:54";s:12:"english_name";s:8:"Armenian";s:11:"native_name";s:14:"Հայերեն";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.4.2/hy.zip";s:3:"iso";a:2:{i:1;s:2:"hy";i:2;s:3:"hye";}s:7:"strings";a:1:{s:8:"continue";s:20:"Շարունակել";}}s:5:"id_ID";a:8:{s:8:"language";s:5:"id_ID";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-29 09:14:16";s:12:"english_name";s:10:"Indonesian";s:11:"native_name";s:16:"Bahasa Indonesia";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/id_ID.zip";s:3:"iso";a:2:{i:1;s:2:"id";i:2;s:3:"ind";}s:7:"strings";a:1:{s:8:"continue";s:9:"Lanjutkan";}}s:5:"is_IS";a:8:{s:8:"language";s:5:"is_IS";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:04:19";s:12:"english_name";s:9:"Icelandic";s:11:"native_name";s:9:"Íslenska";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/is_IS.zip";s:3:"iso";a:2:{i:1;s:2:"is";i:2;s:3:"isl";}s:7:"strings";a:1:{s:8:"continue";s:6:"Áfram";}}s:5:"it_IT";a:8:{s:8:"language";s:5:"it_IT";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-12-02 08:41:30";s:12:"english_name";s:7:"Italian";s:11:"native_name";s:8:"Italiano";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/it_IT.zip";s:3:"iso";a:2:{i:1;s:2:"it";i:2;s:3:"ita";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continua";}}s:2:"ja";a:8:{s:8:"language";s:2:"ja";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-17 17:39:43";s:12:"english_name";s:8:"Japanese";s:11:"native_name";s:9:"日本語";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/ja.zip";s:3:"iso";a:1:{i:1;s:2:"ja";}s:7:"strings";a:1:{s:8:"continue";s:9:"続ける";}}s:5:"ka_GE";a:8:{s:8:"language";s:5:"ka_GE";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-15 07:32:48";s:12:"english_name";s:8:"Georgian";s:11:"native_name";s:21:"ქართული";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/ka_GE.zip";s:3:"iso";a:2:{i:1;s:2:"ka";i:2;s:3:"kat";}s:7:"strings";a:1:{s:8:"continue";s:30:"გაგრძელება";}}s:5:"ko_KR";a:8:{s:8:"language";s:5:"ko_KR";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-23 09:09:28";s:12:"english_name";s:6:"Korean";s:11:"native_name";s:9:"한국어";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/ko_KR.zip";s:3:"iso";a:2:{i:1;s:2:"ko";i:2;s:3:"kor";}s:7:"strings";a:1:{s:8:"continue";s:6:"계속";}}s:5:"lt_LT";a:8:{s:8:"language";s:5:"lt_LT";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-03 22:34:27";s:12:"english_name";s:10:"Lithuanian";s:11:"native_name";s:15:"Lietuvių kalba";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/lt_LT.zip";s:3:"iso";a:2:{i:1;s:2:"lt";i:2;s:3:"lit";}s:7:"strings";a:1:{s:8:"continue";s:6:"Tęsti";}}s:5:"mk_MK";a:8:{s:8:"language";s:5:"mk_MK";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-05-12 13:55:28";s:12:"english_name";s:10:"Macedonian";s:11:"native_name";s:31:"Македонски јазик";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/mk_MK.zip";s:3:"iso";a:2:{i:1;s:2:"mk";i:2;s:3:"mkd";}s:7:"strings";a:1:{s:8:"continue";s:16:"Продолжи";}}s:2:"mr";a:8:{s:8:"language";s:2:"mr";s:7:"version";s:5:"4.5.6";s:7:"updated";s:19:"2016-08-26 13:19:17";s:12:"english_name";s:7:"Marathi";s:11:"native_name";s:15:"मराठी";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.6/mr.zip";s:3:"iso";a:2:{i:1;s:2:"mr";i:2;s:3:"mar";}s:7:"strings";a:1:{s:8:"continue";s:25:"सुरु ठेवा";}}s:5:"ms_MY";a:8:{s:8:"language";s:5:"ms_MY";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-28 05:36:22";s:12:"english_name";s:5:"Malay";s:11:"native_name";s:13:"Bahasa Melayu";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/ms_MY.zip";s:3:"iso";a:2:{i:1;s:2:"ms";i:2;s:3:"msa";}s:7:"strings";a:1:{s:8:"continue";s:8:"Teruskan";}}s:5:"my_MM";a:8:{s:8:"language";s:5:"my_MM";s:7:"version";s:6:"4.1.16";s:7:"updated";s:19:"2015-03-26 15:57:42";s:12:"english_name";s:17:"Myanmar (Burmese)";s:11:"native_name";s:15:"ဗမာစာ";s:7:"package";s:65:"https://downloads.wordpress.org/translation/core/4.1.16/my_MM.zip";s:3:"iso";a:2:{i:1;s:2:"my";i:2;s:3:"mya";}s:7:"strings";a:1:{s:8:"continue";s:54:"ဆက်လက်လုပ်ဆောင်ပါ။";}}s:5:"nb_NO";a:8:{s:8:"language";s:5:"nb_NO";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-09 14:35:35";s:12:"english_name";s:19:"Norwegian (Bokmål)";s:11:"native_name";s:13:"Norsk bokmål";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/nb_NO.zip";s:3:"iso";a:2:{i:1;s:2:"nb";i:2;s:3:"nob";}s:7:"strings";a:1:{s:8:"continue";s:8:"Fortsett";}}s:5:"nl_NL";a:8:{s:8:"language";s:5:"nl_NL";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-15 10:43:48";s:12:"english_name";s:5:"Dutch";s:11:"native_name";s:10:"Nederlands";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/nl_NL.zip";s:3:"iso";a:2:{i:1;s:2:"nl";i:2;s:3:"nld";}s:7:"strings";a:1:{s:8:"continue";s:8:"Doorgaan";}}s:12:"nl_NL_formal";a:8:{s:8:"language";s:12:"nl_NL_formal";s:7:"version";s:5:"4.4.8";s:7:"updated";s:19:"2016-01-20 13:35:50";s:12:"english_name";s:14:"Dutch (Formal)";s:11:"native_name";s:20:"Nederlands (Formeel)";s:7:"package";s:71:"https://downloads.wordpress.org/translation/core/4.4.8/nl_NL_formal.zip";s:3:"iso";a:2:{i:1;s:2:"nl";i:2;s:3:"nld";}s:7:"strings";a:1:{s:8:"continue";s:8:"Doorgaan";}}s:5:"nn_NO";a:8:{s:8:"language";s:5:"nn_NO";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-23 11:56:46";s:12:"english_name";s:19:"Norwegian (Nynorsk)";s:11:"native_name";s:13:"Norsk nynorsk";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/nn_NO.zip";s:3:"iso";a:2:{i:1;s:2:"nn";i:2;s:3:"nno";}s:7:"strings";a:1:{s:8:"continue";s:9:"Hald fram";}}s:3:"oci";a:8:{s:8:"language";s:3:"oci";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-09-23 13:45:56";s:12:"english_name";s:7:"Occitan";s:11:"native_name";s:7:"Occitan";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.5.7/oci.zip";s:3:"iso";a:2:{i:1;s:2:"oc";i:2;s:3:"oci";}s:7:"strings";a:1:{s:8:"continue";s:9:"Contunhar";}}s:5:"pl_PL";a:8:{s:8:"language";s:5:"pl_PL";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-23 08:13:15";s:12:"english_name";s:6:"Polish";s:11:"native_name";s:6:"Polski";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/pl_PL.zip";s:3:"iso";a:2:{i:1;s:2:"pl";i:2;s:3:"pol";}s:7:"strings";a:1:{s:8:"continue";s:9:"Kontynuuj";}}s:2:"ps";a:8:{s:8:"language";s:2:"ps";s:7:"version";s:6:"4.1.16";s:7:"updated";s:19:"2015-03-29 22:19:48";s:12:"english_name";s:6:"Pashto";s:11:"native_name";s:8:"پښتو";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.1.16/ps.zip";s:3:"iso";a:2:{i:1;s:2:"ps";i:2;s:3:"pus";}s:7:"strings";a:1:{s:8:"continue";s:19:"دوام ورکړه";}}s:5:"pt_PT";a:8:{s:8:"language";s:5:"pt_PT";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-02-20 18:51:36";s:12:"english_name";s:21:"Portuguese (Portugal)";s:11:"native_name";s:10:"Português";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/pt_PT.zip";s:3:"iso";a:1:{i:1;s:2:"pt";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"pt_BR";a:8:{s:8:"language";s:5:"pt_BR";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:04:19";s:12:"english_name";s:19:"Portuguese (Brazil)";s:11:"native_name";s:20:"Português do Brasil";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/pt_BR.zip";s:3:"iso";a:2:{i:1;s:2:"pt";i:2;s:3:"por";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"ro_RO";a:8:{s:8:"language";s:5:"ro_RO";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-28 05:26:21";s:12:"english_name";s:8:"Romanian";s:11:"native_name";s:8:"Română";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/ro_RO.zip";s:3:"iso";a:2:{i:1;s:2:"ro";i:2;s:3:"ron";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuă";}}s:5:"ru_RU";a:8:{s:8:"language";s:5:"ru_RU";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-26 13:55:57";s:12:"english_name";s:7:"Russian";s:11:"native_name";s:14:"Русский";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/ru_RU.zip";s:3:"iso";a:2:{i:1;s:2:"ru";i:2;s:3:"rus";}s:7:"strings";a:1:{s:8:"continue";s:20:"Продолжить";}}s:5:"sk_SK";a:8:{s:8:"language";s:5:"sk_SK";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-08-15 21:05:03";s:12:"english_name";s:6:"Slovak";s:11:"native_name";s:11:"Slovenčina";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/sk_SK.zip";s:3:"iso";a:2:{i:1;s:2:"sk";i:2;s:3:"slk";}s:7:"strings";a:1:{s:8:"continue";s:12:"Pokračovať";}}s:5:"sl_SI";a:8:{s:8:"language";s:5:"sl_SI";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-29 21:37:59";s:12:"english_name";s:9:"Slovenian";s:11:"native_name";s:13:"Slovenščina";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/sl_SI.zip";s:3:"iso";a:2:{i:1;s:2:"sl";i:2;s:3:"slv";}s:7:"strings";a:1:{s:8:"continue";s:8:"Nadaljuj";}}s:2:"sq";a:8:{s:8:"language";s:2:"sq";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-23 09:08:48";s:12:"english_name";s:8:"Albanian";s:11:"native_name";s:5:"Shqip";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/sq.zip";s:3:"iso";a:2:{i:1;s:2:"sq";i:2;s:3:"sqi";}s:7:"strings";a:1:{s:8:"continue";s:6:"Vazhdo";}}s:5:"sr_RS";a:8:{s:8:"language";s:5:"sr_RS";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-07-28 05:05:25";s:12:"english_name";s:7:"Serbian";s:11:"native_name";s:23:"Српски језик";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/sr_RS.zip";s:3:"iso";a:2:{i:1;s:2:"sr";i:2;s:3:"srp";}s:7:"strings";a:1:{s:8:"continue";s:14:"Настави";}}s:5:"sv_SE";a:8:{s:8:"language";s:5:"sv_SE";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-23 10:13:40";s:12:"english_name";s:7:"Swedish";s:11:"native_name";s:7:"Svenska";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/sv_SE.zip";s:3:"iso";a:2:{i:1;s:2:"sv";i:2;s:3:"swe";}s:7:"strings";a:1:{s:8:"continue";s:9:"Fortsätt";}}s:2:"th";a:8:{s:8:"language";s:2:"th";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-30 10:22:26";s:12:"english_name";s:4:"Thai";s:11:"native_name";s:9:"ไทย";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/th.zip";s:3:"iso";a:2:{i:1;s:2:"th";i:2;s:3:"tha";}s:7:"strings";a:1:{s:8:"continue";s:15:"ต่อไป";}}s:2:"tl";a:8:{s:8:"language";s:2:"tl";s:7:"version";s:5:"4.4.2";s:7:"updated";s:19:"2015-11-27 15:51:36";s:12:"english_name";s:7:"Tagalog";s:11:"native_name";s:7:"Tagalog";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.4.2/tl.zip";s:3:"iso";a:2:{i:1;s:2:"tl";i:2;s:3:"tgl";}s:7:"strings";a:1:{s:8:"continue";s:10:"Magpatuloy";}}s:5:"tr_TR";a:8:{s:8:"language";s:5:"tr_TR";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-26 08:25:58";s:12:"english_name";s:7:"Turkish";s:11:"native_name";s:8:"Türkçe";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/tr_TR.zip";s:3:"iso";a:2:{i:1;s:2:"tr";i:2;s:3:"tur";}s:7:"strings";a:1:{s:8:"continue";s:5:"Devam";}}s:5:"ug_CN";a:8:{s:8:"language";s:5:"ug_CN";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-06-22 12:27:05";s:12:"english_name";s:6:"Uighur";s:11:"native_name";s:9:"Uyƣurqə";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/ug_CN.zip";s:3:"iso";a:2:{i:1;s:2:"ug";i:2;s:3:"uig";}s:7:"strings";a:1:{s:8:"continue";s:26:"داۋاملاشتۇرۇش";}}s:2:"uk";a:8:{s:8:"language";s:2:"uk";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:04:19";s:12:"english_name";s:9:"Ukrainian";s:11:"native_name";s:20:"Українська";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.5.7/uk.zip";s:3:"iso";a:2:{i:1;s:2:"uk";i:2;s:3:"ukr";}s:7:"strings";a:1:{s:8:"continue";s:20:"Продовжити";}}s:2:"vi";a:8:{s:8:"language";s:2:"vi";s:7:"version";s:5:"4.4.2";s:7:"updated";s:19:"2015-12-09 01:01:25";s:12:"english_name";s:10:"Vietnamese";s:11:"native_name";s:14:"Tiếng Việt";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.4.2/vi.zip";s:3:"iso";a:2:{i:1;s:2:"vi";i:2;s:3:"vie";}s:7:"strings";a:1:{s:8:"continue";s:12:"Tiếp tục";}}s:5:"zh_TW";a:8:{s:8:"language";s:5:"zh_TW";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-04-12 09:08:07";s:12:"english_name";s:16:"Chinese (Taiwan)";s:11:"native_name";s:12:"繁體中文";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/zh_TW.zip";s:3:"iso";a:2:{i:1;s:2:"zh";i:2;s:3:"zho";}s:7:"strings";a:1:{s:8:"continue";s:6:"繼續";}}s:5:"zh_CN";a:8:{s:8:"language";s:5:"zh_CN";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2016-04-17 03:29:01";s:12:"english_name";s:15:"Chinese (China)";s:11:"native_name";s:12:"简体中文";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/zh_CN.zip";s:3:"iso";a:2:{i:1;s:2:"zh";i:2;s:3:"zho";}s:7:"strings";a:1:{s:8:"continue";s:6:"继续";}}}'),
(33, 1, 'registrationnotification', 'no'),
(34, 1, 'welcome_user_email', 'Blocaire,\r\n\r\nEl vostre compre està configurat.\r\n\r\nPodeu iniciar una sessió amb la següent informació:\r\nUsuari: USERNAME\r\nContrasenya: PASSWORD\r\nLOGINLINK\r\n\r\nGràcies!\r\n\r\n--L''equip @ SITE_NAME'),
(109, 1, 'mucd_duplicables', 'all'),
(110, 1, 'mucd_log_dir', '/srv/www/blocs/src/wp-content/plugins/multisite-clone-duplicator/logs/'),
(52, 1, 'pm_supporter_control_list', 'EMPTY'),
(635, 1, '_site_transient_update_themes', 'O:8:"stdClass":4:{s:12:"last_checked";i:1489494450;s:7:"checked";a:17:{s:18:"classic-chalkboard";s:3:"2.6";s:8:"delicacy";s:5:"1.2.8";s:7:"freshy2";s:5:"2.1.2";s:8:"fukasawa";s:4:"1.10";s:8:"mystique";s:5:"2.5.7";s:6:"reddle";s:5:"1.3.5";s:12:"twentyeleven";s:3:"2.5";s:13:"twentyfifteen";s:3:"1.6";s:14:"twentyfourteen";s:3:"1.7";s:13:"twentysixteen";s:3:"1.2";s:9:"twentyten";s:3:"2.1";s:14:"twentythirteen";s:3:"1.9";s:12:"twentytwelve";s:3:"2.1";s:9:"xtec-v1.1";s:3:"1.1";s:13:"xtec898encurs";s:4:"v2.0";s:25:"xtecblocsdefault-formacio";s:3:"1.0";s:16:"xtecblocsdefault";s:3:"1.0";}s:8:"response";a:6:{s:13:"twentyfifteen";a:4:{s:5:"theme";s:13:"twentyfifteen";s:11:"new_version";s:3:"1.7";s:3:"url";s:43:"https://wordpress.org/themes/twentyfifteen/";s:7:"package";s:59:"https://downloads.wordpress.org/theme/twentyfifteen.1.7.zip";}s:14:"twentyfourteen";a:4:{s:5:"theme";s:14:"twentyfourteen";s:11:"new_version";s:3:"1.9";s:3:"url";s:44:"https://wordpress.org/themes/twentyfourteen/";s:7:"package";s:60:"https://downloads.wordpress.org/theme/twentyfourteen.1.9.zip";}s:13:"twentysixteen";a:4:{s:5:"theme";s:13:"twentysixteen";s:11:"new_version";s:3:"1.3";s:3:"url";s:43:"https://wordpress.org/themes/twentysixteen/";s:7:"package";s:59:"https://downloads.wordpress.org/theme/twentysixteen.1.3.zip";}s:9:"twentyten";a:4:{s:5:"theme";s:9:"twentyten";s:11:"new_version";s:3:"2.2";s:3:"url";s:39:"https://wordpress.org/themes/twentyten/";s:7:"package";s:55:"https://downloads.wordpress.org/theme/twentyten.2.2.zip";}s:14:"twentythirteen";a:4:{s:5:"theme";s:14:"twentythirteen";s:11:"new_version";s:3:"2.1";s:3:"url";s:44:"https://wordpress.org/themes/twentythirteen/";s:7:"package";s:60:"https://downloads.wordpress.org/theme/twentythirteen.2.1.zip";}s:12:"twentytwelve";a:4:{s:5:"theme";s:12:"twentytwelve";s:11:"new_version";s:3:"2.2";s:3:"url";s:42:"https://wordpress.org/themes/twentytwelve/";s:7:"package";s:58:"https://downloads.wordpress.org/theme/twentytwelve.2.2.zip";}}s:12:"translations";a:16:{i:0;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:8:"fukasawa";s:8:"language";s:2:"ca";s:7:"version";s:4:"1.10";s:7:"updated";s:19:"2016-03-12 09:22:59";s:7:"package";s:70:"https://downloads.wordpress.org/translation/theme/fukasawa/1.10/ca.zip";s:10:"autoupdate";b:1;}i:1;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:8:"fukasawa";s:8:"language";s:5:"es_ES";s:7:"version";s:4:"1.10";s:7:"updated";s:19:"2016-03-12 09:22:59";s:7:"package";s:73:"https://downloads.wordpress.org/translation/theme/fukasawa/1.10/es_ES.zip";s:10:"autoupdate";b:1;}i:2;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:12:"twentyeleven";s:8:"language";s:2:"ca";s:7:"version";s:3:"2.5";s:7:"updated";s:19:"2016-01-27 11:19:48";s:7:"package";s:73:"https://downloads.wordpress.org/translation/theme/twentyeleven/2.5/ca.zip";s:10:"autoupdate";b:1;}i:3;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:12:"twentyeleven";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"2.5";s:7:"updated";s:19:"2015-10-21 19:35:27";s:7:"package";s:76:"https://downloads.wordpress.org/translation/theme/twentyeleven/2.5/es_ES.zip";s:10:"autoupdate";b:1;}i:4;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:13:"twentyfifteen";s:8:"language";s:2:"ca";s:7:"version";s:3:"1.6";s:7:"updated";s:19:"2015-08-18 16:52:11";s:7:"package";s:74:"https://downloads.wordpress.org/translation/theme/twentyfifteen/1.6/ca.zip";s:10:"autoupdate";b:1;}i:5;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:13:"twentyfifteen";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"1.6";s:7:"updated";s:19:"2016-11-29 16:17:41";s:7:"package";s:77:"https://downloads.wordpress.org/translation/theme/twentyfifteen/1.6/es_ES.zip";s:10:"autoupdate";b:1;}i:6;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:14:"twentyfourteen";s:8:"language";s:2:"ca";s:7:"version";s:3:"1.7";s:7:"updated";s:19:"2015-03-26 20:57:19";s:7:"package";s:75:"https://downloads.wordpress.org/translation/theme/twentyfourteen/1.7/ca.zip";s:10:"autoupdate";b:1;}i:7;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:14:"twentyfourteen";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"1.7";s:7:"updated";s:19:"2015-04-25 17:18:04";s:7:"package";s:78:"https://downloads.wordpress.org/translation/theme/twentyfourteen/1.7/es_ES.zip";s:10:"autoupdate";b:1;}i:8;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:13:"twentysixteen";s:8:"language";s:2:"ca";s:7:"version";s:3:"1.2";s:7:"updated";s:19:"2015-12-07 16:51:38";s:7:"package";s:74:"https://downloads.wordpress.org/translation/theme/twentysixteen/1.2/ca.zip";s:10:"autoupdate";b:1;}i:9;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:13:"twentysixteen";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"1.2";s:7:"updated";s:19:"2016-01-13 09:35:47";s:7:"package";s:77:"https://downloads.wordpress.org/translation/theme/twentysixteen/1.2/es_ES.zip";s:10:"autoupdate";b:1;}i:10;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:9:"twentyten";s:8:"language";s:2:"ca";s:7:"version";s:3:"2.1";s:7:"updated";s:19:"2015-04-21 18:36:02";s:7:"package";s:70:"https://downloads.wordpress.org/translation/theme/twentyten/2.1/ca.zip";s:10:"autoupdate";b:1;}i:11;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:9:"twentyten";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"2.1";s:7:"updated";s:19:"2015-10-21 19:35:55";s:7:"package";s:73:"https://downloads.wordpress.org/translation/theme/twentyten/2.1/es_ES.zip";s:10:"autoupdate";b:1;}i:12;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:14:"twentythirteen";s:8:"language";s:2:"ca";s:7:"version";s:3:"1.9";s:7:"updated";s:19:"2015-03-26 20:57:39";s:7:"package";s:75:"https://downloads.wordpress.org/translation/theme/twentythirteen/1.9/ca.zip";s:10:"autoupdate";b:1;}i:13;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:14:"twentythirteen";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"1.9";s:7:"updated";s:19:"2015-04-25 17:18:29";s:7:"package";s:78:"https://downloads.wordpress.org/translation/theme/twentythirteen/1.9/es_ES.zip";s:10:"autoupdate";b:1;}i:14;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:12:"twentytwelve";s:8:"language";s:2:"ca";s:7:"version";s:3:"2.1";s:7:"updated";s:19:"2015-07-18 10:36:59";s:7:"package";s:73:"https://downloads.wordpress.org/translation/theme/twentytwelve/2.1/ca.zip";s:10:"autoupdate";b:1;}i:15;a:7:{s:4:"type";s:5:"theme";s:4:"slug";s:12:"twentytwelve";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"2.1";s:7:"updated";s:19:"2015-10-21 19:36:21";s:7:"package";s:76:"https://downloads.wordpress.org/translation/theme/twentytwelve/2.1/es_ES.zip";s:10:"autoupdate";b:1;}}}');
INSERT INTO `wp_sitemeta` (`meta_id`, `site_id`, `meta_key`, `meta_value`) VALUES
(636, 1, '_site_transient_update_plugins', 'O:8:"stdClass":5:{s:12:"last_checked";i:1489494537;s:7:"checked";a:34:{s:25:"add-to-any/add-to-any.php";s:5:"1.7.7";s:23:"anti-spam/anti-spam.php";s:3:"4.2";s:37:"blogger-importer/blogger-importer.php";s:3:"0.9";s:43:"google-analyticator/google-analyticator.php";s:7:"6.5.0.0";s:29:"link-manager/link-manager.php";s:8:"0.1-beta";s:57:"multisite-clone-duplicator/multisite-clone-duplicator.php";s:5:"1.3.2";s:43:"multisite-plugin-manager/plugin-manager.php";s:5:"3.1.5";s:34:"scribd-doc-embedder/scribd_doc.php";s:3:"2.0";s:49:"google-calendar-events/google-calendar-events.php";s:5:"3.1.9";s:45:"simple-local-avatars/simple-local-avatars.php";s:3:"2.0";s:32:"simpler-ipaper/scribd-ipaper.php";s:5:"1.3.1";s:25:"slideshare/slideshare.php";s:5:"1.9.2";s:44:"slideshow-jquery-image-gallery/slideshow.php";s:5:"2.3.1";s:37:"tinymce-advanced/tinymce-advanced.php";s:8:"4.3.10.1";s:49:"vipers-video-quicktags/vipers-video-quicktags.php";s:5:"6.5.2";s:41:"wordpress-importer/wordpress-importer.php";s:5:"0.6.3";s:41:"wordpress-php-info/wordpress-php-info.php";s:2:"15";s:42:"wordpress-social-login/wp-social-login.php";s:5:"2.3.0";s:29:"wp-recaptcha/wp-recaptcha.php";s:3:"4.1";s:27:"wp-super-cache/wp-cache.php";s:5:"1.4.8";s:21:"xtec-api/xtec-api.php";s:3:"1.0";s:37:"xtec-descriptors/xtec-descriptors.php";s:3:"1.1";s:33:"xtec-favorites/xtec-favorites.php";s:3:"1.0";s:41:"xtec-lastest-posts/xtec-lastest-posts.php";s:3:"1.0";s:35:"xtec-ldap-login/xtec-ldap-login.php";s:3:"2.1";s:37:"xtec-link-player/xtec-link-player.php";s:3:"1.1";s:23:"xtec-mail/xtec-mail.php";s:3:"3.0";s:37:"xtec-maintenance/xtec-maintenance.php";s:3:"1.1";s:35:"xtec-ms-manager/xtec-ms-manager.php";s:5:"1.1.0";s:31:"xtec-settings/xtec-settings.php";s:3:"1.1";s:27:"xtec-signup/xtec-signup.php";s:3:"1.1";s:25:"xtec-users/xtec-users.php";s:3:"1.1";s:33:"xtec-weekblog2/xtec-weekblog2.php";s:3:"1.0";s:47:"xtec-widget-data-users/xtec-class-data-user.php";s:3:"1.0";}s:8:"response";a:5:{s:23:"anti-spam/anti-spam.php";O:8:"stdClass":8:{s:2:"id";s:5:"34185";s:4:"slug";s:9:"anti-spam";s:6:"plugin";s:23:"anti-spam/anti-spam.php";s:11:"new_version";s:3:"4.3";s:3:"url";s:40:"https://wordpress.org/plugins/anti-spam/";s:7:"package";s:56:"https://downloads.wordpress.org/plugin/anti-spam.4.3.zip";s:6:"tested";s:3:"4.9";s:13:"compatibility";O:8:"stdClass":1:{s:6:"scalar";O:8:"stdClass":1:{s:6:"scalar";b:0;}}}s:43:"google-analyticator/google-analyticator.php";O:8:"stdClass":8:{s:2:"id";s:3:"130";s:4:"slug";s:19:"google-analyticator";s:6:"plugin";s:43:"google-analyticator/google-analyticator.php";s:11:"new_version";s:5:"6.5.2";s:3:"url";s:50:"https://wordpress.org/plugins/google-analyticator/";s:7:"package";s:68:"https://downloads.wordpress.org/plugin/google-analyticator.6.5.2.zip";s:6:"tested";s:5:"4.7.3";s:13:"compatibility";O:8:"stdClass":1:{s:6:"scalar";O:8:"stdClass":1:{s:6:"scalar";b:0;}}}s:57:"multisite-clone-duplicator/multisite-clone-duplicator.php";O:8:"stdClass":8:{s:2:"id";s:5:"52190";s:4:"slug";s:26:"multisite-clone-duplicator";s:6:"plugin";s:57:"multisite-clone-duplicator/multisite-clone-duplicator.php";s:11:"new_version";s:5:"1.4.1";s:3:"url";s:57:"https://wordpress.org/plugins/multisite-clone-duplicator/";s:7:"package";s:75:"https://downloads.wordpress.org/plugin/multisite-clone-duplicator.1.4.1.zip";s:6:"tested";s:5:"4.7.3";s:13:"compatibility";O:8:"stdClass":1:{s:6:"scalar";O:8:"stdClass":1:{s:6:"scalar";b:0;}}}s:49:"vipers-video-quicktags/vipers-video-quicktags.php";O:8:"stdClass":8:{s:2:"id";s:3:"530";s:4:"slug";s:22:"vipers-video-quicktags";s:6:"plugin";s:49:"vipers-video-quicktags/vipers-video-quicktags.php";s:11:"new_version";s:5:"6.6.0";s:3:"url";s:53:"https://wordpress.org/plugins/vipers-video-quicktags/";s:7:"package";s:65:"https://downloads.wordpress.org/plugin/vipers-video-quicktags.zip";s:6:"tested";s:6:"3.9.17";s:13:"compatibility";O:8:"stdClass":1:{s:6:"scalar";O:8:"stdClass":1:{s:6:"scalar";b:0;}}}s:27:"wp-super-cache/wp-cache.php";O:8:"stdClass":9:{s:2:"id";s:4:"1221";s:4:"slug";s:14:"wp-super-cache";s:6:"plugin";s:27:"wp-super-cache/wp-cache.php";s:11:"new_version";s:5:"1.4.9";s:3:"url";s:45:"https://wordpress.org/plugins/wp-super-cache/";s:7:"package";s:63:"https://downloads.wordpress.org/plugin/wp-super-cache.1.4.9.zip";s:14:"upgrade_notice";s:139:"Fixed XSS on the settings page, settings page updates, file locking fixes and PHP 7.1 fix, caching fixes on static homepage blogs and more.";s:6:"tested";s:5:"4.7.3";s:13:"compatibility";O:8:"stdClass":1:{s:6:"scalar";O:8:"stdClass":1:{s:6:"scalar";b:0;}}}}s:12:"translations";a:9:{i:0;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:16:"blogger-importer";s:8:"language";s:2:"ca";s:7:"version";s:3:"0.9";s:7:"updated";s:19:"2015-11-27 11:11:44";s:7:"package";s:78:"https://downloads.wordpress.org/translation/plugin/blogger-importer/0.9/ca.zip";s:10:"autoupdate";b:1;}i:1;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:16:"blogger-importer";s:8:"language";s:5:"es_ES";s:7:"version";s:3:"0.9";s:7:"updated";s:19:"2016-09-29 07:55:07";s:7:"package";s:81:"https://downloads.wordpress.org/translation/plugin/blogger-importer/0.9/es_ES.zip";s:10:"autoupdate";b:1;}i:2;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:19:"google-analyticator";s:8:"language";s:2:"ca";s:7:"version";s:7:"6.5.0.0";s:7:"updated";s:19:"2016-10-26 09:36:44";s:7:"package";s:85:"https://downloads.wordpress.org/translation/plugin/google-analyticator/6.5.0.0/ca.zip";s:10:"autoupdate";b:1;}i:3;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:19:"google-analyticator";s:8:"language";s:5:"es_ES";s:7:"version";s:7:"6.5.0.0";s:7:"updated";s:19:"2015-09-17 17:48:16";s:7:"package";s:88:"https://downloads.wordpress.org/translation/plugin/google-analyticator/6.5.0.0/es_ES.zip";s:10:"autoupdate";b:1;}i:4;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:30:"slideshow-jquery-image-gallery";s:8:"language";s:2:"ca";s:7:"version";s:5:"2.3.1";s:7:"updated";s:19:"2015-11-24 16:19:09";s:7:"package";s:94:"https://downloads.wordpress.org/translation/plugin/slideshow-jquery-image-gallery/2.3.1/ca.zip";s:10:"autoupdate";b:1;}i:5;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:16:"tinymce-advanced";s:8:"language";s:2:"ca";s:7:"version";s:5:"4.2.8";s:7:"updated";s:19:"2015-11-25 09:33:27";s:7:"package";s:80:"https://downloads.wordpress.org/translation/plugin/tinymce-advanced/4.2.8/ca.zip";s:10:"autoupdate";b:1;}i:6;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:22:"vipers-video-quicktags";s:8:"language";s:5:"es_ES";s:7:"version";s:5:"6.5.2";s:7:"updated";s:19:"2015-09-23 16:51:48";s:7:"package";s:89:"https://downloads.wordpress.org/translation/plugin/vipers-video-quicktags/6.5.2/es_ES.zip";s:10:"autoupdate";b:1;}i:7;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:18:"wordpress-importer";s:8:"language";s:2:"ca";s:7:"version";s:5:"0.6.3";s:7:"updated";s:19:"2016-12-09 04:03:57";s:7:"package";s:82:"https://downloads.wordpress.org/translation/plugin/wordpress-importer/0.6.3/ca.zip";s:10:"autoupdate";b:1;}i:8;a:7:{s:4:"type";s:6:"plugin";s:4:"slug";s:18:"wordpress-importer";s:8:"language";s:5:"es_ES";s:7:"version";s:5:"0.6.3";s:7:"updated";s:19:"2015-09-23 23:53:27";s:7:"package";s:85:"https://downloads.wordpress.org/translation/plugin/wordpress-importer/0.6.3/es_ES.zip";s:10:"autoupdate";b:1;}}s:9:"no_update";a:14:{s:25:"add-to-any/add-to-any.php";O:8:"stdClass":6:{s:2:"id";s:3:"429";s:4:"slug";s:10:"add-to-any";s:6:"plugin";s:25:"add-to-any/add-to-any.php";s:11:"new_version";s:5:"1.7.7";s:3:"url";s:41:"https://wordpress.org/plugins/add-to-any/";s:7:"package";s:59:"https://downloads.wordpress.org/plugin/add-to-any.1.7.7.zip";}s:37:"blogger-importer/blogger-importer.php";O:8:"stdClass":6:{s:2:"id";s:5:"14987";s:4:"slug";s:16:"blogger-importer";s:6:"plugin";s:37:"blogger-importer/blogger-importer.php";s:11:"new_version";s:3:"0.9";s:3:"url";s:47:"https://wordpress.org/plugins/blogger-importer/";s:7:"package";s:63:"https://downloads.wordpress.org/plugin/blogger-importer.0.9.zip";}s:29:"link-manager/link-manager.php";O:8:"stdClass":6:{s:2:"id";s:5:"33981";s:4:"slug";s:12:"link-manager";s:6:"plugin";s:29:"link-manager/link-manager.php";s:11:"new_version";s:8:"0.1-beta";s:3:"url";s:43:"https://wordpress.org/plugins/link-manager/";s:7:"package";s:55:"https://downloads.wordpress.org/plugin/link-manager.zip";}s:43:"multisite-plugin-manager/plugin-manager.php";O:8:"stdClass":6:{s:2:"id";s:5:"21587";s:4:"slug";s:24:"multisite-plugin-manager";s:6:"plugin";s:43:"multisite-plugin-manager/plugin-manager.php";s:11:"new_version";s:5:"3.1.5";s:3:"url";s:55:"https://wordpress.org/plugins/multisite-plugin-manager/";s:7:"package";s:73:"https://downloads.wordpress.org/plugin/multisite-plugin-manager.3.1.5.zip";}s:34:"scribd-doc-embedder/scribd_doc.php";O:8:"stdClass":7:{s:2:"id";s:5:"48197";s:4:"slug";s:19:"scribd-doc-embedder";s:6:"plugin";s:34:"scribd-doc-embedder/scribd_doc.php";s:11:"new_version";s:3:"2.0";s:3:"url";s:50:"https://wordpress.org/plugins/scribd-doc-embedder/";s:7:"package";s:62:"https://downloads.wordpress.org/plugin/scribd-doc-embedder.zip";s:14:"upgrade_notice";s:148:"Added shortcode button and configurator to the editor, making it easier to embed documents. (Shortcode button functionality requires WordPress 3.9+)";}s:49:"google-calendar-events/google-calendar-events.php";O:8:"stdClass":6:{s:2:"id";s:5:"15794";s:4:"slug";s:22:"google-calendar-events";s:6:"plugin";s:49:"google-calendar-events/google-calendar-events.php";s:11:"new_version";s:5:"3.1.9";s:3:"url";s:53:"https://wordpress.org/plugins/google-calendar-events/";s:7:"package";s:71:"https://downloads.wordpress.org/plugin/google-calendar-events.3.1.9.zip";}s:45:"simple-local-avatars/simple-local-avatars.php";O:8:"stdClass":7:{s:2:"id";s:5:"20007";s:4:"slug";s:20:"simple-local-avatars";s:6:"plugin";s:45:"simple-local-avatars/simple-local-avatars.php";s:11:"new_version";s:3:"2.0";s:3:"url";s:51:"https://wordpress.org/plugins/simple-local-avatars/";s:7:"package";s:67:"https://downloads.wordpress.org/plugin/simple-local-avatars.2.0.zip";s:14:"upgrade_notice";s:273:"Upgraded to take advantage of WordPress 3.5 and newer. Does not support older versions! This has also not been tested with front end profile plug-ins - feedback welcome. Note that several language strings have been added or modified - revised translations would be welcome!";}s:32:"simpler-ipaper/scribd-ipaper.php";O:8:"stdClass":7:{s:2:"id";s:4:"4839";s:4:"slug";s:14:"simpler-ipaper";s:6:"plugin";s:32:"simpler-ipaper/scribd-ipaper.php";s:11:"new_version";s:5:"1.3.1";s:3:"url";s:45:"https://wordpress.org/plugins/simpler-ipaper/";s:7:"package";s:63:"https://downloads.wordpress.org/plugin/simpler-ipaper.1.3.1.zip";s:14:"upgrade_notice";s:75:"Upgrade for new functionality: positioning and styling the embed using CSS.";}s:25:"slideshare/slideshare.php";O:8:"stdClass":6:{s:2:"id";s:4:"1569";s:4:"slug";s:10:"slideshare";s:6:"plugin";s:25:"slideshare/slideshare.php";s:11:"new_version";s:5:"1.9.2";s:3:"url";s:41:"https://wordpress.org/plugins/slideshare/";s:7:"package";s:59:"https://downloads.wordpress.org/plugin/slideshare.1.9.2.zip";}s:44:"slideshow-jquery-image-gallery/slideshow.php";O:8:"stdClass":6:{s:2:"id";s:5:"31854";s:4:"slug";s:30:"slideshow-jquery-image-gallery";s:6:"plugin";s:44:"slideshow-jquery-image-gallery/slideshow.php";s:11:"new_version";s:5:"2.3.1";s:3:"url";s:61:"https://wordpress.org/plugins/slideshow-jquery-image-gallery/";s:7:"package";s:73:"https://downloads.wordpress.org/plugin/slideshow-jquery-image-gallery.zip";}s:37:"tinymce-advanced/tinymce-advanced.php";O:8:"stdClass":8:{s:2:"id";s:3:"731";s:4:"slug";s:16:"tinymce-advanced";s:6:"plugin";s:37:"tinymce-advanced/tinymce-advanced.php";s:11:"new_version";s:5:"4.4.3";s:3:"url";s:47:"https://wordpress.org/plugins/tinymce-advanced/";s:7:"package";s:65:"https://downloads.wordpress.org/plugin/tinymce-advanced.4.4.3.zip";s:6:"tested";s:5:"4.7.3";s:13:"compatibility";b:0;}s:41:"wordpress-importer/wordpress-importer.php";O:8:"stdClass":6:{s:2:"id";s:5:"14975";s:4:"slug";s:18:"wordpress-importer";s:6:"plugin";s:41:"wordpress-importer/wordpress-importer.php";s:11:"new_version";s:5:"0.6.3";s:3:"url";s:49:"https://wordpress.org/plugins/wordpress-importer/";s:7:"package";s:67:"https://downloads.wordpress.org/plugin/wordpress-importer.0.6.3.zip";}s:41:"wordpress-php-info/wordpress-php-info.php";O:8:"stdClass":6:{s:2:"id";s:4:"6362";s:4:"slug";s:18:"wordpress-php-info";s:6:"plugin";s:41:"wordpress-php-info/wordpress-php-info.php";s:11:"new_version";s:2:"15";s:3:"url";s:49:"https://wordpress.org/plugins/wordpress-php-info/";s:7:"package";s:61:"https://downloads.wordpress.org/plugin/wordpress-php-info.zip";}s:42:"wordpress-social-login/wp-social-login.php";O:8:"stdClass":6:{s:2:"id";s:5:"27354";s:4:"slug";s:22:"wordpress-social-login";s:6:"plugin";s:42:"wordpress-social-login/wp-social-login.php";s:11:"new_version";s:5:"2.3.0";s:3:"url";s:53:"https://wordpress.org/plugins/wordpress-social-login/";s:7:"package";s:65:"https://downloads.wordpress.org/plugin/wordpress-social-login.zip";}}}'),
(642, 1, 'recently_activated', 'a:2:{s:33:"xtec-weekblog2/xtec-weekblog2.php";i:1489494759;s:32:"simpler-ipaper/scribd-ipaper.php";i:1489494684;}'),
(643, 1, 'mucd_copy_files', 'yes'),
(644, 1, 'mucd_keep_users', 'yes'),
(645, 1, 'mucd_log', 'no'),
(646, 1, 'xmm_quota_percentage', '75'),
(647, 1, 'xmm_send_email', '1'),
(648, 1, 'xmm_email_addresses', ''),
(649, 1, '_site_transient_timeout_available_translations', '1489505824'),
(621, 1, 'auth_key', '+sxLD,5 dv?}H<#$=t2F}*fH, 1Vk0Ok&W:p|nmE9XpkHso^UI1)q5Y+*li6>~6J'),
(622, 1, 'auth_salt', 'zcW>;<}?p&Eg!2#JHR_1fYQHU68gtwg!cV@O]&#i6k)|h G4SV5d[5]r>/9Yx1?5'),
(641, 1, '_site_transient_update_core', 'O:8:"stdClass":4:{s:7:"updates";a:4:{i:0;O:8:"stdClass":10:{s:8:"response";s:7:"upgrade";s:8:"download";s:62:"https://downloads.wordpress.org/release/ca/wordpress-4.7.3.zip";s:6:"locale";s:2:"ca";s:8:"packages";O:8:"stdClass":5:{s:4:"full";s:62:"https://downloads.wordpress.org/release/ca/wordpress-4.7.3.zip";s:10:"no_content";b:0;s:11:"new_bundled";b:0;s:7:"partial";b:0;s:8:"rollback";b:0;}s:7:"current";s:5:"4.7.3";s:7:"version";s:5:"4.7.3";s:11:"php_version";s:5:"5.2.4";s:13:"mysql_version";s:3:"5.0";s:11:"new_bundled";s:3:"4.7";s:15:"partial_version";s:0:"";}i:1;O:8:"stdClass":10:{s:8:"response";s:7:"upgrade";s:8:"download";s:59:"https://downloads.wordpress.org/release/wordpress-4.7.3.zip";s:6:"locale";s:5:"en_US";s:8:"packages";O:8:"stdClass":5:{s:4:"full";s:59:"https://downloads.wordpress.org/release/wordpress-4.7.3.zip";s:10:"no_content";s:70:"https://downloads.wordpress.org/release/wordpress-4.7.3-no-content.zip";s:11:"new_bundled";s:71:"https://downloads.wordpress.org/release/wordpress-4.7.3-new-bundled.zip";s:7:"partial";b:0;s:8:"rollback";b:0;}s:7:"current";s:5:"4.7.3";s:7:"version";s:5:"4.7.3";s:11:"php_version";s:5:"5.2.4";s:13:"mysql_version";s:3:"5.0";s:11:"new_bundled";s:3:"4.7";s:15:"partial_version";s:0:"";}i:2;O:8:"stdClass":11:{s:8:"response";s:10:"autoupdate";s:8:"download";s:59:"https://downloads.wordpress.org/release/wordpress-4.7.3.zip";s:6:"locale";s:5:"en_US";s:8:"packages";O:8:"stdClass":5:{s:4:"full";s:59:"https://downloads.wordpress.org/release/wordpress-4.7.3.zip";s:10:"no_content";s:70:"https://downloads.wordpress.org/release/wordpress-4.7.3-no-content.zip";s:11:"new_bundled";s:71:"https://downloads.wordpress.org/release/wordpress-4.7.3-new-bundled.zip";s:7:"partial";b:0;s:8:"rollback";b:0;}s:7:"current";s:5:"4.7.3";s:7:"version";s:5:"4.7.3";s:11:"php_version";s:5:"5.2.4";s:13:"mysql_version";s:3:"5.0";s:11:"new_bundled";s:3:"4.7";s:15:"partial_version";s:0:"";s:9:"new_files";s:1:"1";}i:3;O:8:"stdClass":11:{s:8:"response";s:10:"autoupdate";s:8:"download";s:59:"https://downloads.wordpress.org/release/wordpress-4.6.4.zip";s:6:"locale";s:5:"en_US";s:8:"packages";O:8:"stdClass":5:{s:4:"full";s:59:"https://downloads.wordpress.org/release/wordpress-4.6.4.zip";s:10:"no_content";s:70:"https://downloads.wordpress.org/release/wordpress-4.6.4-no-content.zip";s:11:"new_bundled";s:71:"https://downloads.wordpress.org/release/wordpress-4.6.4-new-bundled.zip";s:7:"partial";b:0;s:8:"rollback";b:0;}s:7:"current";s:5:"4.6.4";s:7:"version";s:5:"4.6.4";s:11:"php_version";s:5:"5.2.4";s:13:"mysql_version";s:3:"5.0";s:11:"new_bundled";s:3:"4.7";s:15:"partial_version";s:0:"";s:9:"new_files";s:1:"1";}}s:12:"last_checked";i:1489494525;s:15:"version_checked";s:5:"4.5.7";s:12:"translations";a:1:{i:0;a:7:{s:4:"type";s:4:"core";s:4:"slug";s:7:"default";s:8:"language";s:5:"es_ES";s:7:"version";s:5:"4.5.7";s:7:"updated";s:19:"2017-01-29 17:02:40";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.5.7/es_ES.zip";s:10:"autoupdate";b:1;}}}'),
(600, 1, 'xtec_mail_replyto', 'blocs-noreply@xtec.invalid'),
(601, 1, 'xtec_mail_sender', 'educacio'),
(602, 1, 'xtec_mail_log', '1'),
(603, 1, 'xtec_mail_debug', '0'),
(604, 1, 'xtec_mail_logpath', '/var/log/apache2/correulog.txt'),
(599, 1, 'xtec_mail_idapp', 'XTECBLOCS'),
(573, 1, 'xtec_ldap_login_type', 'LDAP'),
(577, 1, 'bwp_capt_theme', 'a:4:{s:9:"input_tab";s:1:"0";s:10:"enable_css";s:3:"yes";s:11:"select_lang";s:2:"es";s:12:"select_theme";s:3:"red";}'),
(639, 1, '_site_transient_timeout_browser_d2854db6940f4667d5c5fbbeb7a4d501', '1490099311'),
(640, 1, '_site_transient_browser_d2854db6940f4667d5c5fbbeb7a4d501', 'a:9:{s:8:"platform";s:5:"Linux";s:4:"name";s:6:"Chrome";s:7:"version";s:13:"53.0.2785.143";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}'),
(651, 1, 'menu_items', 'a:1:{s:7:"plugins";s:1:"1";}'),
(652, 1, 'first_page', ''),
(653, 1, 'first_comment', ''),
(654, 1, 'first_comment_url', ''),
(655, 1, 'first_comment_author', ''),
(656, 1, 'limited_email_domains', ''),
(657, 1, 'banned_email_domains', ''),
(658, 1, 'WPLANG', 'ca'),
(659, 1, 'xtec_signup_maxblogsday', '20'),
(660, 1, 'xtec_ldap_host', 'oid-xtec.educacio.intranet'),
(661, 1, 'xtec_ldap_port', '389'),
(662, 1, 'xtec_ldap_version', '3'),
(663, 1, 'xtec_ldap_base_dn', 'cn=users,dc=educacio,dc=intranet');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_termmeta`
--

CREATE TABLE IF NOT EXISTS `wp_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_terms`
--

CREATE TABLE IF NOT EXISTS `wp_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Bolcant dades de la taula `wp_terms`
--

INSERT INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(1, 'General', 'general', 0),
(2, 'Blogroll', 'blogroll', 0),
(3, 'google', 'google', 0),
(4, 'default-calendar', 'default-calendar', 0),
(5, 'grouped-calendar', 'grouped-calendar', 0);

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_term_relationships`
--

CREATE TABLE IF NOT EXISTS `wp_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Bolcant dades de la taula `wp_term_relationships`
--

INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES
(1, 2, 0),
(2, 2, 0),
(3, 2, 0),
(4, 2, 0),
(5, 2, 0),
(6, 2, 0),
(7, 2, 0),
(1, 1, 0),
(12, 3, 0),
(12, 4, 0);

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_term_taxonomy`
--

CREATE TABLE IF NOT EXISTS `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Bolcant dades de la taula `wp_term_taxonomy`
--

INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1, 1, 'category', '', 0, 1),
(2, 2, 'link_category', '', 0, 7),
(3, 3, 'calendar_feed', '', 0, 0),
(4, 4, 'calendar_type', '', 0, 0),
(5, 5, 'calendar_feed', '', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_usermeta`
--

CREATE TABLE IF NOT EXISTS `wp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=392 ;

--
-- Bolcant dades de la taula `wp_usermeta`
--

INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'first_name', 'Administrador'),
(2, 1, 'last_name', ''),
(3, 1, 'nickname', 'admin'),
(4, 1, 'description', ''),
(5, 1, 'rich_editing', 'true'),
(6, 1, 'comment_shortcuts', 'false'),
(7, 1, 'admin_color', 'fresh'),
(8, 1, 'use_ssl', '0'),
(9, 1, 'show_admin_bar_front', 'true'),
(66, 1, 'wp_5_user-settings-time', '1489496058'),
(11, 1, 'aim', ''),
(12, 1, 'yim', ''),
(13, 1, 'jabber', ''),
(14, 1, 'wp_capabilities', 'a:1:{s:13:"administrator";s:1:"1";}'),
(15, 1, 'wp_user_level', '10'),
(16, 1, 'wp_dashboard_quick_press_last_post_id', '20'),
(17, 1, 'source_domain', 'agora'),
(18, 1, 'primary_blog', '1'),
(28, 1, 'wp_5_capabilities', 'a:1:{s:13:"administrator";s:1:"1";}'),
(21, 1, 'wp_3_capabilities', 'a:1:{s:13:"administrator";s:1:"1";}'),
(22, 1, 'wp_3_user_level', '10'),
(24, 1, 'wp_4_capabilities', 'a:1:{s:13:"administrator";s:1:"1";}'),
(25, 1, 'wp_4_user_level', '10'),
(26, 1, 'wp_3_dashboard_quick_press_last_post_id', '122'),
(27, 1, 'wp_4_dashboard_quick_press_last_post_id', '177'),
(29, 1, 'wp_5_user_level', '10'),
(302, 1, 'meta-box-order_post', 'a:3:{s:4:"side";s:56:"submitdiv,postimagediv,postexcerpt,metabox1,tagsdiv-post";s:6:"normal";s:11:"categorydiv";s:8:"advanced";s:0:"";}'),
(32, 1, 'wp_5_dashboard_quick_press_last_post_id', '11'),
(65, 1, 'wp_5_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce'),
(64, 1, 'wp_user-settings-time', '1489494506'),
(63, 1, 'wp_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce'),
(67, 1, 'dismissed_wp_pointers', 'wp330_toolbar,wp340_customize_current_theme_link,wp350_media,wp360_revisions,addtoany_settings_pointer'),
(68, 1, 'wp_6_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),
(69, 1, 'wp_6_user-settings-time', '1424705365'),
(70, 1, 'wp_6_dashboard_quick_press_last_post_id', '12'),
(71, 1, 'wp_3_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce'),
(72, 1, 'wp_3_user-settings-time', '1489495977'),
(73, 1, 'wp_4_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce'),
(74, 1, 'wp_4_user-settings-time', '1489496035'),
(75, 1, 'closedpostboxes_dashboard', 'a:0:{}'),
(76, 1, 'metaboxhidden_dashboard', 'a:0:{}'),
(77, 1, 'closedpostboxes_dashboard-network', 'a:0:{}'),
(78, 1, 'metaboxhidden_dashboard-network', 'a:0:{}'),
(104, 1, 'managenav-menuscolumnshidden', 'a:4:{i:0;s:11:"link-target";i:1;s:11:"css-classes";i:2;s:3:"xfn";i:3;s:11:"description";}'),
(105, 1, 'metaboxhidden_nav-menus', 'a:4:{i:0;s:8:"add-post";i:1;s:12:"add-gce_feed";i:2;s:12:"add-post_tag";i:3;s:15:"add-post_format";}'),
(123, 1, 'wp_7_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),
(124, 1, 'wp_7_user-settings-time', '1424854295'),
(125, 1, 'wp_7_dashboard_quick_press_last_post_id', '3'),
(130, 1, 'wp_9_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),
(131, 1, 'wp_9_user-settings-time', '1424854980'),
(132, 1, 'wp_9_dashboard_quick_press_last_post_id', '3'),
(137, 1, 'wp_11_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),
(138, 1, 'wp_11_user-settings-time', '1424856720'),
(139, 1, 'wp_11_dashboard_quick_press_last_post_id', '3'),
(168, 1, 'wp_18_dashboard_quick_press_last_post_id', '46'),
(270, 1, 'wp_36_dashboard_quick_press_last_post_id', '3'),
(348, 1, 'wp_52_dashboard_quick_press_last_post_id', '6'),
(367, 1, 'wp_53_dashboard_quick_press_last_post_id', '3'),
(269, 1, 'wp_36_user-settings-time', '1426249582'),
(166, 1, 'wp_18_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606'),
(167, 1, 'wp_18_user-settings-time', '1425287273'),
(195, 1, 'wp_19_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(196, 1, 'wp_19_user-settings-time', '1425300779'),
(197, 1, 'wp_19_dashboard_quick_press_last_post_id', '3'),
(347, 1, 'wp_52_user-settings-time', '1427970091'),
(250, 1, 'wp_30_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),
(224, 1, 'wp_26_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(225, 1, 'wp_26_user-settings-time', '1426062713'),
(226, 1, 'wp_26_dashboard_quick_press_last_post_id', '111'),
(252, 1, 'wp_30_dashboard_quick_press_last_post_id', '112'),
(251, 1, 'wp_30_user-settings-time', '1426064784'),
(365, 1, 'wp_53_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(366, 1, 'wp_53_user-settings-time', '1428934970'),
(233, 1, 'wp_27_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(234, 1, 'wp_27_user-settings-time', '1426063042'),
(235, 1, 'wp_27_dashboard_quick_press_last_post_id', '111'),
(268, 1, 'wp_36_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),
(255, 1, 'wp_31_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),
(256, 1, 'wp_31_user-settings-time', '1426149689'),
(257, 1, 'wp_31_dashboard_quick_press_last_post_id', '112'),
(346, 1, 'wp_52_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(287, 1, 'wp_44_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),
(288, 1, 'wp_44_user-settings-time', '1426583453'),
(289, 1, 'wp_44_dashboard_quick_press_last_post_id', '112'),
(303, 1, 'metaboxhidden_post', 'a:2:{i:0;s:7:"slugdiv";i:1;s:9:"authordiv";}'),
(305, 1, 'meta-box-order_page', 'a:3:{s:4:"side";s:23:"submitdiv,pageparentdiv";s:6:"normal";s:16:"commentstatusdiv";s:8:"advanced";s:0:"";}'),
(304, 1, 'closedpostboxes_post', 'a:0:{}'),
(306, 1, 'metaboxhidden_page', 'a:3:{i:0;s:16:"commentstatusdiv";i:1;s:7:"slugdiv";i:2;s:9:"authordiv";}'),
(376, 1, 'wp_55_user-settings-time', '1428936615'),
(307, 1, 'closedpostboxes_page', 'a:0:{}'),
(371, 1, 'wp_54_user-settings-time', '1428935582'),
(372, 1, 'wp_54_dashboard_quick_press_last_post_id', '3'),
(375, 1, 'wp_55_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(387, 11, 'use_ssl', '0'),
(388, 11, 'show_admin_bar_front', 'true'),
(391, 11, 'dismissed_wp_pointers', 'wp350_media,wp360_revisions,wp360_locks,wp390_widgets'),
(383, 11, 'description', ''),
(337, 1, 'wp_51_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(338, 1, 'wp_51_user-settings-time', '1426757741'),
(339, 1, 'wp_51_dashboard_quick_press_last_post_id', '3'),
(384, 11, 'rich_editing', 'true'),
(385, 11, 'comment_shortcuts', 'false'),
(386, 11, 'admin_color', 'fresh'),
(381, 11, 'first_name', ''),
(380, 11, 'nickname', 'est_colex'),
(370, 1, 'wp_54_user-settings', 'm6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),
(377, 1, 'wp_55_dashboard_quick_press_last_post_id', '3'),
(382, 11, 'last_name', '');

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_users`
--

CREATE TABLE IF NOT EXISTS `wp_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `spam` tinyint(2) NOT NULL DEFAULT '0',
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Bolcant dades de la taula `wp_users`
--

INSERT INTO `wp_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`, `spam`, `deleted`) VALUES
(1, 'admin', '$P$B0BqNdVBE.ATTO79Lz.szWyh1QRonh.', 'admin', 'admin@blocs.xtec.cat', '', '2012-12-19 13:05:13', '', 0, 'admin', 0, 0),
(11, 'est_colex', '$P$BG97aVa8N.KCw0mH38BbV5XzY8b7xR0', 'est_colex', 'est_colex@blocs.xtec.cat', '', '2015-04-20 12:17:51', '', 0, 'est_colex', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de la taula `wp_user_blogs`
--

CREATE TABLE IF NOT EXISTS `wp_user_blogs` (
  `ubid` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT '0',
  `blogId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ubid`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
