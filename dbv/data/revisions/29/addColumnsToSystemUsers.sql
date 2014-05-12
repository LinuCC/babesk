ALTER TABLE SystemUsers ADD religion varchar(3) NOT NULL DEFAULT '';
ALTER TABLE SystemUsers ADD foreign_language varchar(30) NOT NULL DEFAULT '';
ALTER TABLE SystemUsers ADD course varchar(30) NOT NULL DEFAULT '';
ALTER TABLE SystemUsers ADD special_course varchar(30) NOT NULL DEFAULT '';