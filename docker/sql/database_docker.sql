-- Adminer 5.4.1 MariaDB 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;


DROP TABLE IF EXISTS `allergene`;
CREATE TABLE `allergene` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `allergene` (`id`, `libelle`) VALUES
(1,	'Arachide'),
(2,	'Céleri'),
(3,	'Crustacés'),
(4,	'Gluten'),
(5,	'Fruits à coque'),
(6,	'Lait'),
(7,	'Lupin'),
(8,	'Oeuf'),
(9,	'Poisson'),
(10,	'Mollusques'),
(11,	'Moutarde'),
(12,	'Sésame'),
(13,	'Soja'),
(14,	'Sulfites');

DROP TABLE IF EXISTS `avis`;
CREATE TABLE `avis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `statut` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `date_creation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F91ABF0A76ED395` (`user_id`),
  KEY `IDX_8F91ABF082EA2E54` (`commande_id`),
  CONSTRAINT `FK_8F91ABF082EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`),
  CONSTRAINT `FK_8F91ABF0A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `avis` (`id`, `note`, `description`, `statut`, `user_id`, `commande_id`, `date_creation`) VALUES
(12,	5,	'Le repas était délicieux, avec du poisson frais et des légumes goûteux et croquants.',	'Validé',	13,	15,	'2026-02-07 15:50:02'),
(13,	1,	'Trop salé, trop cuit....',	'Rejeté',	10,	16,	'2026-02-12 09:05:09'),
(14,	4,	'Très belle découverte, j\'ai adoré!!',	'Validé',	11,	17,	'2026-02-12 09:17:30'),
(16,	4,	'Très bon rapport qualité/prix avec la livraison!! ',	'Validé',	11,	19,	'2026-02-12 09:14:45'),
(17,	5,	'Expérience très réussie, le repas était excellent. Je recommande vivement.',	'Validé',	14,	23,	'2026-02-13 15:25:15'),
(18,	4,	'J\'ai voulu essayer le curry de légumes et j\'ai adoré. Le repas était copieux et très parfumé.',	'Validé',	14,	24,	'2026-02-16 10:31:20'),
(19,	5,	'J\'ai opté pour le menu indien et nous l\'avons adoré! Très épicé et très copieux.',	'Validé',	13,	28,	'2026-02-16 11:57:25');

DROP TABLE IF EXISTS `commande`;
CREATE TABLE `commande` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_commande` varchar(255) NOT NULL,
  `date_commande` date NOT NULL,
  `date_prestation` date NOT NULL,
  `heure_liv` time NOT NULL,
  `prix_menu` double NOT NULL,
  `nombre_personne` int(11) NOT NULL,
  `prix_liv` double NOT NULL,
  `statut` varchar(255) NOT NULL,
  `pret_mat` tinyint(4) DEFAULT NULL,
  `retour_mat` tinyint(4) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `contact_method` varchar(20) DEFAULT NULL,
  `modification_reason` longtext DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `avis_depose` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6EEAA67DA76ED395` (`user_id`),
  KEY `IDX_6EEAA67D99049ECE` (`modified_by_id`),
  CONSTRAINT `FK_6EEAA67D99049ECE` FOREIGN KEY (`modified_by_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_6EEAA67DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `commande` (`id`, `numero_commande`, `date_commande`, `date_prestation`, `heure_liv`, `prix_menu`, `nombre_personne`, `prix_liv`, `statut`, `pret_mat`, `retour_mat`, `user_id`, `contact_method`, `modification_reason`, `modified_at`, `modified_by_id`, `avis_depose`) VALUES
(15,	'CMD-20260130-32005',	'2026-01-30',	'2026-02-02',	'12:00:00',	270,	12,	10.9,	'Terminé',	1,	1,	13,	NULL,	NULL,	'2026-02-05 16:54:45',	7,	1),
(16,	'CMD-20260209-12670',	'2026-02-09',	'2026-02-11',	'12:00:00',	168,	6,	10.9,	'Terminé',	0,	0,	10,	NULL,	NULL,	'2026-02-09 20:40:36',	8,	1),
(17,	'CMD-20260209-47609',	'2026-02-09',	'2026-02-11',	'12:30:00',	280,	10,	5,	'Terminé',	0,	0,	11,	NULL,	NULL,	'2026-02-09 20:52:22',	9,	1),
(19,	'CMD-20260212-06559',	'2026-02-12',	'2026-02-14',	'12:00:00',	120,	6,	5,	'Terminé',	1,	1,	11,	NULL,	NULL,	'2026-02-12 11:19:49',	8,	1),
(21,	'CMD-20260213-43593',	'2026-02-13',	'2026-02-15',	'12:00:00',	60,	4,	10.9,	'Terminé',	0,	0,	10,	NULL,	NULL,	'2026-02-13 11:25:29',	7,	NULL),
(22,	'CMD-20260213-74429',	'2026-02-13',	'2026-02-15',	'12:00:00',	168,	6,	5,	'Terminé',	0,	0,	5,	NULL,	NULL,	'2026-02-13 12:12:24',	7,	NULL),
(23,	'CMD-20260213-51142',	'2026-02-13',	'2026-02-15',	'12:00:00',	240,	8,	10.9,	'Terminé',	0,	0,	14,	NULL,	NULL,	'2026-02-13 13:31:44',	7,	1),
(24,	'CMD-20260213-73059',	'2026-02-13',	'2026-02-15',	'12:00:00',	80,	4,	10.9,	'Terminé',	0,	0,	14,	NULL,	NULL,	'2026-02-13 13:59:46',	7,	1),
(26,	'CMD-20260213-60050',	'2026-02-13',	'2026-02-15',	'12:00:00',	210.6,	9,	5,	'Terminé',	0,	0,	5,	NULL,	NULL,	'2026-02-13 16:47:52',	7,	NULL),
(28,	'CMD-20260213-34440',	'2026-02-13',	'2026-02-15',	'12:00:00',	210.6,	9,	10.9,	'Terminé',	0,	0,	13,	NULL,	NULL,	'2026-02-13 17:26:35',	7,	1),
(29,	'CMD-20260213-05425',	'2026-02-13',	'2026-02-15',	'12:00:00',	224,	10,	10.9,	'Terminé',	1,	1,	13,	'Mail',	'Le client a ajouté 2 personnes',	'2026-02-13 17:28:19',	7,	NULL);

DROP TABLE IF EXISTS `commande_menu`;
CREATE TABLE `commande_menu` (
  `commande_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  PRIMARY KEY (`commande_id`,`menu_id`),
  KEY `IDX_16693B7082EA2E54` (`commande_id`),
  KEY `IDX_16693B70CCD7E912` (`menu_id`),
  CONSTRAINT `FK_16693B7082EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_16693B70CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `commande_menu` (`commande_id`, `menu_id`) VALUES
(15,	26),
(16,	28),
(17,	28),
(19,	29),
(21,	31),
(22,	28),
(23,	27),
(24,	29),
(26,	9),
(28,	9),
(29,	28);

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `horaire`;
CREATE TABLE `horaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jour` varchar(255) DEFAULT NULL,
  `heure_ouverture` varchar(255) DEFAULT NULL,
  `heure_fermeture` varchar(255) DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `horaire` (`id`, `jour`, `heure_ouverture`, `heure_fermeture`, `note`) VALUES
(1,	'Lundi',	'00:00',	'00:00',	'Fermé'),
(2,	'Mardi',	'09:00',	'19:00',	NULL),
(3,	'Mercredi',	'09:00',	'19:00',	NULL),
(4,	'Jeudi',	'00:00',	'00:00',	'Fermé '),
(5,	'Vendredi',	'09:00',	'19:00',	NULL),
(6,	'Samedi',	'09:00',	'20:00',	NULL),
(7,	'Dimanche',	'09:00',	'12:00',	NULL);

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `nombre_personne_mini` int(11) NOT NULL,
  `prix_par_personne` double NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantite_restante` int(11) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `regime_id` int(11) DEFAULT NULL,
  `conditions` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7D053A9359027487` (`theme_id`),
  KEY `IDX_7D053A9335E7D534` (`regime_id`),
  CONSTRAINT `FK_7D053A9335E7D534` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`id`),
  CONSTRAINT `FK_7D053A9359027487` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `menu` (`id`, `titre`, `nombre_personne_mini`, `prix_par_personne`, `description`, `quantite_restante`, `theme_id`, `regime_id`, `conditions`) VALUES
(9,	'Indien',	4,	26,	'Poulet, fromage, épices, oignons, pain.',	10,	3,	4,	'A commander 2 jours avant. Du lundi au vendredi. '),
(26,	'Truite entière',	6,	25,	'Truites entières d\'aquaculture de nos régions, carottes, courgettes, oignon, pomme de terre. Sauce hollandaise avec jaunes d’œufs, du beurre mou, du jus de citron, sel, poivre. \n',	18,	4,	4,	'A commander 2 ou 3 jours avant. '),
(27,	'Chapon',	8,	30,	'Chapon, marron, pain aux céréales, échalotes, champignons de paris, beurre, bouquet de coriandre, poivre du moulin.',	40,	2,	4,	'Commander 10 jours avant.'),
(28,	'Filet de bœuf ',	6,	28,	'Filet de bœuf, pomme de terre, navet, carotte, potiron, moutarde, huile d\'olive, persil, laurier, sel, poivre. ',	18,	4,	4,	'A commander 2 ou 3 jours avant. '),
(29,	'Curry de légumes',	4,	20,	'Curry de légumes végan, riz, carottes, pois chiches, concentré de tomates, courgettes, échalotes, gousses d\'ail, crème de coco,, curry, cumin.',	10,	5,	3,	'A commander 2 jours avant. '),
(31,	'Poke Bowl végétarien',	4,	15,	'Riz, salade, haricot, maïs, tomate, chou, Œuf, tofu.',	20,	5,	2,	'A commander 2h avant du lundi au vendredi livraison sur Bordeaux');

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `plat`;
CREATE TABLE `plat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre_plat` varchar(50) DEFAULT NULL,
  `photo` longtext DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2038A207CCD7E912` (`menu_id`),
  CONSTRAINT `FK_2038A207CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `plat` (`id`, `titre_plat`, `photo`, `menu_id`) VALUES
(6,	'Indien',	'/uploads/photos/indien-695d1ba48ec0b.jpg',	9),
(7,	'Truite entière ',	'/uploads/photos/poisson-697ca830209d2.jpg',	26),
(8,	'Chapon ',	'/uploads/photos/noel-697c8789ebeaf.jpg',	27),
(9,	'Filet de bœuf ',	'/uploads/photos/filet-698b671e588a9.jpg',	28),
(10,	'Curry de légumes',	'/uploads/photos/curry-697c8b19920c5.jpg',	29),
(12,	'Poke bowl',	'/uploads/photos/poke-697caad0652d0.jpg',	31);

DROP TABLE IF EXISTS `plat_allergene`;
CREATE TABLE `plat_allergene` (
  `plat_id` int(11) NOT NULL,
  `allergene_id` int(11) NOT NULL,
  PRIMARY KEY (`plat_id`,`allergene_id`),
  KEY `IDX_6FA44BBFD73DB560` (`plat_id`),
  KEY `IDX_6FA44BBF4646AB2` (`allergene_id`),
  CONSTRAINT `FK_6FA44BBF4646AB2` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6FA44BBFD73DB560` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `plat_allergene` (`plat_id`, `allergene_id`) VALUES
(6,	4),
(6,	6),
(6,	8),
(7,	6),
(7,	9),
(9,	11),
(12,	8);

DROP TABLE IF EXISTS `regime`;
CREATE TABLE `regime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `regime` (`id`, `libelle`) VALUES
(1,	'Tous'),
(2,	'Végétarien'),
(3,	'Vegan'),
(4,	'Classique');

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `theme`;
CREATE TABLE `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `theme` (`id`, `libelle`) VALUES
(1,	'Tous'),
(2,	'Repas de fête'),
(3,	'Cuisine du monde'),
(4,	'Pour recevoir'),
(5,	'Entre amis');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `tel` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `code_p` varchar(255) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `api_token` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `actif` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  KEY `IDX_8D93D649D60322AC` (`role_id`),
  CONSTRAINT `FK_8D93D649D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `tel`, `adresse`, `code_p`, `ville`, `created_at`, `updated_at`, `api_token`, `role_id`, `actif`) VALUES
(5,	'jean@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$SCpYMRjpj6qkmfYxclWE8eFhFihvhvcCsGfH7djXARhBLjftV44Dy',	'Mou',	'JeanPierre',	'123456789',	'11 rue des merles',	'33000',	'Bordeaux',	'2025-12-09 22:16:05',	NULL,	'392adc4f48046af5ec059ed08e40c94ff361e6a9',	NULL,	1),
(7,	'admin@viteetgourmand.fr',	'[\"ROLE_ADMIN\"]',	'$2y$13$CuJNgncPAqXFCt9jD0yzP.nNHFQM0Qh49s0wqMGYpWWZB6NjdI8Ce',	'Admin',	'Julie',	'0600000000',	'3 rue du pont',	'33000',	'Bordeaux',	'2025-12-16 13:17:44',	'2025-12-16 13:17:44',	'd29f1b6af88568e7e8f217d8f7c2a7b2b3ade633e1320466df9590c145b38e2b',	NULL,	1),
(8,	'employe1@mail.com',	'[\"ROLE_EMPLOYE\"]',	'$2y$13$YORb2mCTY01ziJfJoeZGBulb3GGk8/WCVSsyicvOQYdqwv6QoFy1W',	'Durand',	'Thomas',	'456789',	'rue du vieux pont',	'33000',	'Bordeaux',	'2025-12-19 20:38:53',	NULL,	'efec0e874782c042aaaa30dd7f0586ba514fdcdc09ece7ff10b4a9de23cfae9d',	NULL,	1),
(9,	'employe2@mail.com',	'[\"ROLE_EMPLOYE\"]',	'$2y$13$w/gg4JqNXp84lumUSibCXeigW0.KEuZpujii0zoCFYpexpQrp8Y06',	'Blanc',	'Michel',	'',	'',	'',	'',	'2025-12-19 21:23:09',	NULL,	'b0438b5c0a1004e888c542c27864f7e6a34796fca52a7cd85074b7eba14171ae',	NULL,	1),
(10,	'yvette@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$nIMeyyrHJMZ2VCs/wvPuF.LEkhWdCk3cZCwgfRuK9B3ol7um53krC',	'Durand',	'Yvette',	'123456',	'Cenon',	'33119',	'Cenon',	'2026-01-29 19:05:41',	NULL,	'e0abdec70193f465d5498b9607e46aefefd614c5',	NULL,	1),
(11,	'pascal@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$nLk5SkjQaxjXekMJ9OXZFOYaZf.W5T8APV2/vfAg2LvlF3N8Uin1W',	'Deslilas',	'Pascal',	'123456',	'Bordeaux',	'33000',	'Bordeaux',	'2026-01-29 19:17:19',	NULL,	'20c7a104e603cea6617254188aff75d3f0c2a2e9',	NULL,	1),
(13,	'michelle@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$cL/ZCQRihwN2spWYvXHRzeAMgiItsZf5MuVRpFv0TmfYy80/YSVT2',	'Bouquet',	'Michelle',	'123456',	'Pessac',	'33318',	'Pessac',	'2026-01-30 10:15:34',	NULL,	'963173b773620847aa1e1a82016276a116dbec83',	NULL,	1),
(14,	'justine@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$xb/4bZTr5eY6178DRcvXtuA3UksE89Fe/MicWyE9z6PinRqyWHAky',	'Dubois',	'Justine',	'123456',	'Floirac',	'33167',	'Floirac',	'2026-02-13 13:17:13',	NULL,	'242d14042cfd0d8f9183020d32ed9237771d560b',	NULL,	1);

-- 2026-02-16 15:56:10 UTC
