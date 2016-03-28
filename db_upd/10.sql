INSERT INTO  `tbl_profiles_fields` (
`id` ,
`varname` ,
`title` ,
`field_type` ,
`field_size` ,
`field_size_min` ,
`required` ,
`match` ,
`range` ,
`error_message` ,
`other_validator` ,
`default` ,
`widget` ,
`widgetparams` ,
`position` ,
`visible`
)
VALUES (
NULL ,  'city',  'City',  'VARCHAR',  '50',  '1',  '3',  '',  '',  'Incorrect City (length between 1 and 50 characters).',  '',  '',  '',  '',  '0',  '3'
);
INSERT INTO `tbl_profiles_fields` (`id`, `varname`, `title`, `field_type`, `field_size`, `field_size_min`, `required`, `match`, `range`, `error_message`, `other_validator`, `default`, `widget`, `widgetparams`, `position`, `visible`) VALUES (NULL, 'ip', 'IP', 'VARCHAR', '16', '7', '0', '', '', 'Incorrect IP', '', '', '', '', '0', '0');
