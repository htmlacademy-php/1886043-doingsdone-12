SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS `doingsdone` DEFAULT CHARACTER SET utf8 ;
USE `doingsdone` ;

-- -----------------------------------------------------
-- Table `doingsdone`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `doingsdone`.`user` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_date` DATE NOT NULL,
  `user_email` VARCHAR(80) NULL,
  `name` VARCHAR(80) NOT NULL,
  `password` VARCHAR(80) NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `doingsdone`.`project`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `doingsdone`.`project` (
  `project_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_name` VARCHAR(45) NULL,
  `creator_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`project_id`),
  UNIQUE INDEX `project_id_UNIQUE` (`project_id` ASC),
  INDEX `creator_id_idx` (`creator_id` ASC),
  CONSTRAINT `creator_id`
    FOREIGN KEY (`creator_id`)
    REFERENCES `doingsdone`.`user` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `doingsdone`.`task`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `doingsdone`.`task` (
  `task_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_creation_date` TIMESTAMP NULL,
  `task_status` TINYINT NULL,
  `task_name` VARCHAR(45) NOT NULL,
  `file_adress` VARCHAR(80) NULL,
  `finish_date` DATE NULL,
  `project_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`task_id`),
  UNIQUE INDEX `task_id_UNIQUE` (`task_id` ASC),
  INDEX `project_id_idx` (`project_id` ASC),
  CONSTRAINT `project_id`
    FOREIGN KEY (`project_id`)
    REFERENCES `doingsdone`.`project` (`project_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
