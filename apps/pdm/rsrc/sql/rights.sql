#
#
#
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject`
    (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES
    (NULL, 'dbt_OBJECT_*', 'dbt', 'def.OBJECT.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES
    (NULL, 'pdmMaster', 'dbt_OBJECT_*', '1');

#
#
#
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject`
    (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES
    (NULL, 'dbt_OBJECT_store', 'dbt', 'def.OBJECT.store', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES
    (NULL, 'pdmMaster', 'dbt_OBJECT_store', '1');

#
#
#
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject`
    (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES
    (NULL, 'dbt_OBJECT_read', 'dbt', 'def.OBJECT.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES
    (NULL, 'pdmMaster', 'dbt_OBJECT_read', '1');

#
#
#
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject`
    (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES
    (NULL, 'dbt_OBJECTSurvey_read', 'dbt', 'def.v_OBJECTSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES
    (NULL, 'pdmMaster', 'dbt_OBJECTSurvey_read', '1');

