SELECT hersteller_id, count(*)  FROM `t_bremse` group by hersteller_id

SELECT hersteller_id, br_bezeichnung, count(*)  FROM `t_bremse` where hersteller_id = "#.00000001" group by br_bezeichnung
