-- Adminer 5.4.1 MariaDB 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

--Insertion des allergènes 
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

--Insertion des avis
INSERT INTO `avis` (`id`, `note`, `description`, `statut`, `user_id`, `commande_id`, `date_creation`) VALUES
(11,	5,	'Expérience parfaite du début à la fin : repas savoureux et livraison impeccable.',	'Validé',	5,	1,	'2026-01-26 00:00:00'),
(12,	5,	'Le repas était délicieux, avec du poisson frais et des légumes goûteux et croquants.',	'Validé',	13,	15,	NULL),
(13,	1,	'Trop salé, trop cuit....',	'Rejeté',	10,	16,	NULL),
(14,	4,	'Très belle découverte, j\ai adoré!!',	'Validé',	11,	17,	NULL),
(15,	5,	'Merci à l\équipe pour la modification de ma commande, tout c\est très bien déroulé!',	'Validé',	11,	18,	NULL);

--Insertion des commandes
INSERT INTO `commande` (`id`, `numero_commande`, `date_commande`, `date_prestation`, `heure_liv`, `prix_menu`, `nombre_personne`, `prix_liv`, `statut`, `pret_mat`, `retour_mat`, `user_id`, `contact_method`, `modification_reason`, `modified_at`, `modified_by_id`, `avis_depose`) VALUES
(1,	'CMD-20260114-36456',	'2026-01-14',	'2026-01-21',	'13:00:00',	312,	12,	5,	'Terminé',	0,	0,	5,	'Tel',	'Le client utilisera son matériel',	'2026-01-23 14:40:56',	8,	1),
(15,	'CMD-20260130-32005',	'2026-01-30',	'2026-02-02',	'12:00:00',	270,	12,	10.9,	'Terminé',	1,	1,	13,	NULL,	NULL,	'2026-02-05 16:54:45',	7,	1),
(16,	'CMD-20260209-12670',	'2026-02-09',	'2026-02-11',	'12:00:00',	168,	6,	10.9,	'Terminé',	0,	0,	10,	NULL,	NULL,	'2026-02-09 20:40:36',	8,	1),
(17,	'CMD-20260209-47609',	'2026-02-09',	'2026-02-11',	'12:30:00',	280,	10,	5,	'Terminé',	0,	0,	11,	NULL,	NULL,	'2026-02-09 20:52:22',	9,	1),
(18,	'CMD-20260209-60964',	'2026-02-09',	'2026-02-17',	'14:00:00',	208,	9,	5,	'Terminé',	0,	0,	11,	'Tel',	'Ajout du client ',	'2026-02-09 20:53:57',	9,	1);

INSERT INTO `commande_menu` (`commande_id`, `menu_id`) VALUES
(1,	9),
(15,	26),
(16,	28),
(17,	28),
(18,	9);

--Insertion des horaires
INSERT INTO `horaire` (`id`, `jour`, `heure_ouverture`, `heure_fermeture`, `note`) VALUES
(1,	'Lundi',	'00:00',	'00:00',	'Fermé'),
(2,	'Mardi',	'09:00',	'18:00',	NULL),
(3,	'Mercredi',	'09:00',	'18:00',	NULL),
(4,	'Jeudi',	'09:00',	'18:00',	NULL),
(5,	'Vendredi',	'09:00',	'18:00',	NULL),
(6,	'Samedi',	'09:00',	'18:00',	NULL),
(7,	'Dimanche',	'09:00',	'12:00',	NULL);

--Insertion des menus
INSERT INTO `menu` (`id`, `titre`, `nombre_personne_mini`, `prix_par_personne`, `description`, `quantite_restante`, `theme_id`, `regime_id`, `conditions`) VALUES
(9,	'Indien',	4,	26,	'Poulet, fromage, épices, oignons, pain.',	28,	3,	4,	'A commander 2 jours avant. Du lundi au vendredi. '),
(26,	'Truite entière',	6,	25,	'Truites entières d\aquaculture de nos régions, carottes, courgettes, oignon, pomme de terre. Sauce hollandaise avec jaunes d’œufs, du beurre mou, du jus de citron, sel, poivre. \n',	18,	4,	4,	'A commander 2 ou 3 jours avant. '),
(27,	'Chapon',	8,	30,	'Chapon, marron, pain aux céréales, échalotes, champignons de paris, beurre, bouquet de coriandre, poivre du moulin.',	48,	2,	4,	'Commander 10 jours avant.'),
(28,	'Filet de bœuf ',	6,	28,	'Filet de bœuf, pomme de terre, navet, carotte, potiron, moutarde, huile d\olive, persil, laurier, sel, poivre. ',	14,	4,	4,	'A commander 2 ou 3 jours avant. '),
(29,	'Curry de légumes',	4,	20,	'Curry de légumes végan, riz, carottes, pois chiches, concentré de tomates, courgettes, échalotes, gousses d\ail, crème de coco,, curry, cumin.',	20,	5,	3,	'A commander 2 jours avant. '),
(31,	'Poke Bowl végétarien',	4,	15,	'Riz, salade, haricot, maïs, tomate, chou, Œuf, tofu.',	24,	5,	2,	'A commander 2h avant du lundi au vendredi livraison sur Bordeaux');

--Insertion des plats
INSERT INTO `plat` (`id`, `titre_plat`, `photo`, `menu_id`) VALUES
(6,	'Indien',	'/uploads/photos/indien-695d1ba48ec0b.jpg',	9),
(7,	'Truite entière ',	'/uploads/photos/poisson-697ca830209d2.jpg',	26),
(8,	'Chapon ',	'/uploads/photos/noel-697c8789ebeaf.jpg',	27),
(9,	'Filet de bœuf ',	'/uploads/photos/filet-697c895dca86a.jpg',	28),
(10,	'Curry de légumes',	'/uploads/photos/curry-697c8b19920c5.jpg',	29),
(12,	'Poke bowl',	'/uploads/photos/poke-697caad0652d0.jpg',	31);

INSERT INTO `plat_allergene` (`plat_id`, `allergene_id`) VALUES
(6,	4),
(6,	6),
(6,	8),
(7,	6),
(7,	9),
(9,	11),
(12,	8);

--Insertion des régimes
INSERT INTO `regime` (`id`, `libelle`) VALUES
(1,	'Tous'),
(2,	'Végétarien'),
(3,	'Vegan'),
(4,	'Classique');

--Insertion des thèmes
INSERT INTO `theme` (`id`, `libelle`) VALUES
(1,	'Tous'),
(2,	'Repas de fête'),
(3,	'Cuisine du monde'),
(4,	'Pour recevoir'),
(5,	'Entre amis');

--Insertion des utilisateurs
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `tel`, `adresse`, `code_p`, `ville`, `created_at`, `updated_at`, `api_token`, `role_id`, `actif`) VALUES
(5,	'jean@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$SCpYMRjpj6qkmfYxclWE8eFhFihvhvcCsGfH7djXARhBLjftV44Dy',	'Mou',	'JeanPierre',	'123456789',	'11 rue des merles',	'33000',	'Bordeaux',	'2025-12-09 22:16:05',	NULL,	'392adc4f48046af5ec059ed08e40c94ff361e6a9',	NULL,	1),
(7,	'admin@viteetgourmand.fr',	'[\"ROLE_ADMIN\"]',	'$2y$13$CuJNgncPAqXFCt9jD0yzP.nNHFQM0Qh49s0wqMGYpWWZB6NjdI8Ce',	'Admin',	'Julie',	'0600000000',	'3 rue du pont',	'33000',	'Bordeaux',	'2025-12-16 13:17:44',	'2025-12-16 13:17:44',	'd29f1b6af88568e7e8f217d8f7c2a7b2b3ade633e1320466df9590c145b38e2b',	NULL,	1),
(8,	'employe1@mail.com',	'[\"ROLE_EMPLOYE\"]',	'$2y$13$YORb2mCTY01ziJfJoeZGBulb3GGk8/WCVSsyicvOQYdqwv6QoFy1W',	'Durand',	'Thomas',	'456789',	'rue du vieux pont',	'33000',	'Bordeaux',	'2025-12-19 20:38:53',	NULL,	'efec0e874782c042aaaa30dd7f0586ba514fdcdc09ece7ff10b4a9de23cfae9d',	NULL,	1),
(9,	'employe2@mail.com',	'[\"ROLE_EMPLOYE\"]',	'$2y$13$w/gg4JqNXp84lumUSibCXeigW0.KEuZpujii0zoCFYpexpQrp8Y06',	'Blanc',	'Michel',	NULL,	NULL,	NULL,	NULL,	'2025-12-19 21:23:09',	NULL,	'b0438b5c0a1004e888c542c27864f7e6a34796fca52a7cd85074b7eba14171ae',	NULL,	1),
(10,	'yvette@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$nIMeyyrHJMZ2VCs/wvPuF.LEkhWdCk3cZCwgfRuK9B3ol7um53krC',	'Durand',	'Yvette',	'123456',	'Cenon',	'33119',	'Cenon',	'2026-01-29 19:05:41',	NULL,	'e0abdec70193f465d5498b9607e46aefefd614c5',	NULL,	1),
(11,	'pascal@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$nLk5SkjQaxjXekMJ9OXZFOYaZf.W5T8APV2/vfAg2LvlF3N8Uin1W',	'Deslilas',	'Pascal',	'123456',	'Bordeaux',	'33000',	'Bordeaux',	'2026-01-29 19:17:19',	NULL,	'20c7a104e603cea6617254188aff75d3f0c2a2e9',	NULL,	1),
(13,	'michelle@mail.fr',	'[\"ROLE_USER\"]',	'$2y$13$cL/ZCQRihwN2spWYvXHRzeAMgiItsZf5MuVRpFv0TmfYy80/YSVT2',	'Bouquet',	'Michelle',	'123456',	'Pessac',	'33318',	'Pessac',	'2026-01-30 10:15:34',	NULL,	'963173b773620847aa1e1a82016276a116dbec83',	NULL,	1);

-- 2026-02-10 09:42:08 UTC