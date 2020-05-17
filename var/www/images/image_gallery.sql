CREATE TABLE `ae_gallery` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) character SET utf8 NOT NULL,
  `ext` varchar(8) character SET utf8 NOT NULL,
  `image_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data` mediumblob NOT NULL,
  PRIMARY KEY  (`id`) 
);
