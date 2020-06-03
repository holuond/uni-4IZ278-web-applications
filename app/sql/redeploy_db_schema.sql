-- DROP SCHEMA IF EXISTS
SET foreign_key_checks = 0;
DROP TABLE IF EXISTS `holo00`.`test_results`;
DROP TABLE IF EXISTS `holo00`.`tests`;
DROP TABLE IF EXISTS `holo00`.`users`;
SET foreign_key_checks = 1;

-- CREATE SCHEMA
CREATE TABLE `holo00`.`tests`
(
    `test_id`         INT(10)      NOT NULL AUTO_INCREMENT,
    `user_id`         INT(10)      NOT NULL,
    `name`            VARCHAR(150) NOT NULL,
    `xml`             TEXT         NOT NULL,
    `activation_time` INT(10),
    PRIMARY KEY (`test_id`)
) ENGINE = InnoDB;

CREATE TABLE `holo00`.`users`
(
    `user_id`    INT(10)      NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(100) NOT NULL,
    `password`   VARCHAR(100) NOT NULL,
    `isexaminer` BOOLEAN      NOT NULL,
    `isexaminee` BOOLEAN      NOT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE (`email`)
) ENGINE = InnoDB;

CREATE TABLE `holo00`.`test_results`
(
    `test_id`         INT(10) NOT NULL,
    `user_id`         INT(10) NOT NULL,
    `score`           DECIMAL(5, 4),
    `submission_time` INT(10),
    `answersheet`     TEXT,
    PRIMARY KEY (`test_id`, `user_id`)
) ENGINE = InnoDB;

ALTER TABLE `tests`
    ADD CONSTRAINT `tests_foreign_key_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `test_results`
    ADD CONSTRAINT `test_results_foreign_key_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `test_results`
    ADD CONSTRAINT `test_results_foreign_key_test_id` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;