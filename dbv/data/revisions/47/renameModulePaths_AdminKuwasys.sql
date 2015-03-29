-- Kuwasys
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
  'Kuwasys',
  'root/administrator/Kuwasys',
  @moduleId
);
UPDATE `SystemModules`
  SET `executablePath` = "administrator/Kuwasys/Kuwasys.php"
  WHERE `ID` = @moduleId;

-- Classteachers
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
  'Classteachers',
  'root/administrator/Kuwasys/Classteachers',
  @moduleId
);
UPDATE `SystemModules`
  SET `executablePath` =
    "administrator/Kuwasys/Classteachers/Classteachers.php"
  WHERE `ID` = @moduleId;

-- Classes
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
  'Classes',
  'root/administrator/Kuwasys/Classes',
  @moduleId
);
UPDATE `SystemModules`
  SET `executablePath` =
    "administrator/Kuwasys/Classes/Classes.php"
  WHERE `ID` = @moduleId;

-- KuwasysUsers
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
  'KuwasysUsers',
  'root/administrator/Kuwasys/KuwasysUsers',
  @moduleId
);
UPDATE `SystemModules`
  SET `executablePath` =
    "administrator/Kuwasys/KuwasysUsers/KuwasysUsers.php"
  WHERE `ID` = @moduleId;