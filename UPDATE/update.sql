INSERT INTO `payment_gateway` (`id`, `gateway_name`, `gateway_short_info`, `gateway_info`, `status`) VALUES (14, 'SSLCOMMERZ', 'Payment for Bangladesh', NULL, '0');

INSERT INTO `payment_gateway` (`id`, `gateway_name`, `gateway_short_info`, `gateway_info`, `status`) VALUES (15, 'CinetPay', 'CinetPay for West Africa and Central Africa', NULL, '0');

ALTER TABLE `settings` ADD `tmdb_api_language` VARCHAR(20) NOT NULL DEFAULT 'en-US' AFTER `tmdb_api_key`;