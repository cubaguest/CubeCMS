INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
('LOGIN_TIME', 'Doba po které je uživatel automaticky odhlášen (s)', '3600', '0', 'number'),
('IMAGE_THUMB_W', 'Výchozí šířka miniatury', '150', '0', 'number'),
('IMAGE_THUMB_H', 'Výchozí výška miniatury', '150', '0', 'number'),
('SMTP_SERVER', 'Adresa smtp serveru pro odesílání pošty', 'localhost', '0', 'string'),
('SMTP_SERVER_PORT', 'Port smtp serveru pro odesílání pošty', '25', '0', 'number'),
('SMTP_SERVER_USERNAME', 'Uživatelské jméno smtp serveru pro odesílání pošty', null, '0', 'string'),
('SMTP_SERVER_PASSWORD', 'Uživatelské heslo smtp serveru pro odesílání pošty', null, '0', 'string'),
('NOREPLAY_MAIL', 'Název schránky odesílané pošty', null, '0', 'string'),
('SHORT_TEXT_TAGS', 'tagy, které jsou povoleny ve zkrácených výpisech', '<strong><a><em><span>', '0', 'string');
