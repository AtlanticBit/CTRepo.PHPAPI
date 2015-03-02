USE DB2USE;

CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `author` text NOT NULL,
  `iconurl` text NOT NULL,
  `version` text NOT NULL,
  `shortdesc` text NOT NULL,
  `longdesc` text NOT NULL,
  `urlto3dsx` text NOT NULL,
  `urltosmdh` text NOT NULL,
  `urltocia` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
