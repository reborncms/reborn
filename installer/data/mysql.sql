CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `version` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
--- db ---
CREATE TABLE IF NOT EXISTS `settings` (
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `desc` text COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `default` text COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--- db ---
INSERT INTO `settings` (`slug`, `name`, `desc`, `value`, `default`, `module`) VALUES
('default_module', 'Default Module', 'Default Module for Reborn CMS', '', 'pages', 'system'),
('home_page', 'Home Page', 'Home Page for your site', '', 'home', 'system'),
('site_title', 'Site Title', 'Site name for your site', '{SITETITLE}', 'Reborn CMS', 'system'),
('site_slogan', 'Site Slogan', 'Slogan for your site', '{SLOGAN}', 'Your slogan here', 'system'),
('public_theme', 'Public Theme', 'Theme for the site frontend (Public)', 'default', 'default', 'system'),
('admin_theme', 'Admin Panel Theme', 'Theme for the site backend (Admin Panel)', '', 'default', 'system'),
('adminpanel_url', 'Admin Panel URI', 'URI for the admin panel.', '', 'admin', 'system'),
('default_language', 'Default Language', 'Default Language for Reborn CMS', '', 'en', 'system'),
('timezone', 'Select your Timezone', 'Set timezone for your server', '', 'UTC', 'system'),
('admin_item_per_page', 'Items to show in one page (Admin Panel)', 'Item limit to show in admin Data Tables', '', '10', 'system'),
('frontend_enabled', 'Frontend Status', 'If your site in maintenance condition, you can closed your site.', '', 'enable', 'system'),
('spam_filter', 'Spam Filter Key', 'Use this key for spam filter from bot', '', 'D0ntFillINthI$FielD', 'system'),
('sever_mail', 'Sever Mail', 'Email for outgoing Email', '', '{EMAIL}', 'Contact'),
('site_mail', 'Site Mail', 'Contact for your Website', '', '{EMAIL}', 'Contact');
--- db ---
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `groups_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--- db ---
INSERT INTO `groups` (`id`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', '{"admin":1,"navigation":1,"tag":1,"pages":1,"user":1,"setting":1,"module":1,"media":1,"theme":1,"maintenance":1,"contact":1,"widgets":1,"blog":1,"field":1,"comment":1,"site":1,"nav.create":1,"nav.edit":1,"nav.delete":1,"tag.create":1,"tag.edit":1,"tag.delete":1,"pages.create":1,"pages.edit":1,"pages.delete":1,"user.create":1,"user.edit":1,"user.delete":1,"user.group":1,"user.group.create":1,"user.group.edit":1,"user.group.delete":1,"user.permission":1,"user.permission.edit":1,"module.upload":1,"module.install":1,"module.disable":1,"module.enable":1,"theme.upload":1,"theme.activate":1,"theme.delete":1,"theme.editor":1,"contact.view":1,"contact.reply":1,"contact.delete":1,"contact.template.add":1,"contact.template.edit":1,"contact.template.delete":1,"blog.create":1,"blog.edit":1,"blog.delete":1,"blog_cat.create":1,"blog_cat.edit":1,"blog_cat.delete":1,"comment.reply":1,"comment.edit":1,"comment.delete":1}', '{NOW}', '{NOW}'),
(2, 'Moderator', '{"admin":1,"tag":1,"pages":1,"media":1,"blog":1,"comment":1,"tag.create":1,"tag.edit":1,"tag.delete":1,"pages.create":1,"pages.edit":1,"pages.delete":1,"blog.create":1,"blog.edit":1,"blog.delete":1,"blog_cat.create":1,"blog_cat.edit":1,"blog_cat.delete":1,"comment.reply":1,"comment.edit":1,"comment.delete":1}', '{NOW}', '{NOW}'),
(3, 'User', '{"blog":1,"blog.create":1,"blog.edit":1,"blog.delete":1,"blog_cat.create":1,"blog_cat.edit":1,"blog_cat.delete":1}', '{NOW}', '{NOW}'),
(6, 'Subscriber', '', '{NOW}', '{NOW}');
--- db ---
CREATE TABLE `throttle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `suspended` tinyint(4) NOT NULL DEFAULT '0',
  `banned` tinyint(4) NOT NULL DEFAULT '0',
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--- db ---
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `activated` tinyint(4) NOT NULL DEFAULT '0',
  `activation_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activated_at` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `persist_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_password_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--- db ---
INSERT INTO `users` (`id`, `email`, `password`, `reset_password_code`, `activation_code`, `activated_at`, `last_login`, `persist_code`, `activated`, `permissions`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
(1, '{EMAIL}', '{PASS}', NULL, NULL, '', '', '', 1, '{"superuser":1}', '{FIRSTNAME}', '{LASTNAME}', '{NOW}', '{NOW}');
--- db ---
CREATE TABLE `users_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--- db ---
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1);
--- db ---
CREATE TABLE IF NOT EXISTS `users_metadata` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `biography` text COLLATE utf8_unicode_ci,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--- db ---
INSERT INTO `users_metadata` (`user_id`, `username`, `biography`, `country`, `website`, `facebook`, `twitter`) VALUES
(1, '', '', '', '', '', '');
