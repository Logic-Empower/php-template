CREATE USER 'lehosting_f1advisoryservices'@'localhost' IDENTIFIED BY RANDOM PASSWORD;

GRANT SELECT, INSERT, UPDATE, DELETE ON lehosting_f1advisoryservices.* TO 'lehosting_f1advisoryservices'@'localhost';