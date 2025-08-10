CREATE TABLE `IT202-Pokemon` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `pokedex_id` INT,
  `ability_1` VARCHAR(50),
  `ability_2` VARCHAR(50),
  `ability_3` VARCHAR(50),
  `moves` TEXT, -- large text for long move lists
  `hp` SMALLINT UNSIGNED,
  `attack` SMALLINT UNSIGNED,
  `defense` SMALLINT UNSIGNED,
  `sp_attack` SMALLINT UNSIGNED,
  `sp_defense` SMALLINT UNSIGNED,
  `speed` SMALLINT UNSIGNED,
  `type_1` VARCHAR(20),
  `type_2` VARCHAR(20),
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_api` TINYINT(1) DEFAULT 1,
  UNIQUE KEY `unique_pokedex` (`pokedex_id`)
);
