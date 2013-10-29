-- Default values for general settings
INSERT INTO `global_settings`
	(name, value) VALUES
	-- If Schooltype is used or not in the program
	('schooltypeEnabled', 1),
	-- Should the User change the Password on first Login
	('firstLoginChangePassword', 1),
	-- Should the User be asked for his Email-Address on first Login
	('firstLoginChangeEmail', 1),
	-- Should the User be forced to type his Email-Address on first Login
	('firstLoginForceChangeEmail', 0),
	-- The Helptext getting displayed when the User clicks on help in the
	-- login screen
	('webLoginHelptext', 'Hier Hilfe einfügen'),
	-- The general Helptext when the User clicks on Help when logged in
	('helptext', 'Hier Hilfe einfügen'),
	-- When the User logs in, he can be redirected to a Module
	-- (Style is 'Headmodule|Module')
	('webHomepageRedirectTarget', ''),
	-- The delay in seconds of the redirection of webHomepageRedirectTarget
	('webHomepageRedirectDelay', 3),
	-- Settings for Smtp-Email-Server to use
	('smtpHost', ''),
	('smtpUsername', ''),
	('smtpPassword', ''),
	('smtpFromName', ''),
	('smtpFrom', ''),
	-- Settings for Creating PDFs
	-- The Logo of the PDF-File
	('pdfDefaultLogopath', ''),
	('pdfDefaultHeaderHeading', ''),
	('pdfDefaultHeaderText', ''),
	-- The hash of the preset password
	('presetPassword', '');
