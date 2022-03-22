-- -----------------------------------------------------
-- Table `clientsapp`.`clients`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `client_code` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  UNIQUE INDEX `client code_UNIQUE` (`client_code` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `clientsapp`.`contacts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `email_address` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`contact_id`),
  UNIQUE INDEX `email address_UNIQUE` (`email_address` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `clientsapp`.`clientscontacts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientscontacts` (
  `ccid` INT NOT NULL AUTO_INCREMENT,
  `client_id` INT NOT NULL COMMENT 'The client',
  `contact_id` INT NOT NULL COMMENT 'The contact',
  PRIMARY KEY (`ccid`),
  INDEX `fk_clientscontacts_1_idx` (`client_id` ASC),
  INDEX `fk_clientscontacts_2_idx` (`contact_id` ASC),
  CONSTRAINT `fk_clientscontacts_1`
    FOREIGN KEY (`client_id`)
    REFERENCES `clients` (`client_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_clientscontacts_2`
    FOREIGN KEY (`contact_id`)
    REFERENCES `contacts` (`contact_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
