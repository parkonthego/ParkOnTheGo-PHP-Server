-- MySQL Script generated by MySQL Workbench
-- 06/03/16 22:47:30
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema parkonthego
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;

CREATE TABLE IF NOT EXISTS `user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(155) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `middle_name` VARCHAR(100) NULL,
  `username` VARCHAR(255) NULL,
  `display_name` VARCHAR(255) NULL,
  `is_valid` TINYINT(1) NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `parking_slot`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `parking_slot` ;

CREATE TABLE IF NOT EXISTS `parking_slot` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  `description` VARCHAR(255) NULL,
  `price` DOUBLE NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `reservation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `reservation` ;

CREATE TABLE IF NOT EXISTS `reservation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `parking_id` INT NOT NULL,
  `starting_time` DATETIME NULL,
  `end_time` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `cost` DOUBLE NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `parking_id`
    FOREIGN KEY (`parking_id`)
    REFERENCES `parking_slot` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `user_id_idx` ON `reservation` (`user_id` ASC);

CREATE INDEX `parking_id_idx` ON `reservation` (`parking_id` ASC);

CREATE UNIQUE INDEX `single_reservation` ON `reservation` (`starting_time` ASC, `end_time` ASC, `parking_id` ASC);


-- -----------------------------------------------------
-- Table `payment_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `payment_history` ;

CREATE TABLE IF NOT EXISTS `payment_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `merchant_payment_id` VARCHAR(255) NOT NULL,
  `review_note` TEXT NULL,
  `amount` DOUBLE NOT NULL,
  `datetime` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `user_id` INT NOT NULL,
  `reservation_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `user_pay_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `reservation_id`
    FOREIGN KEY (`reservation_id`)
    REFERENCES `reservation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `user_pay_id_idx` ON `payment_history` (`user_id` ASC);

CREATE INDEX `reservation_id_idx` ON `payment_history` (`reservation_id` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;