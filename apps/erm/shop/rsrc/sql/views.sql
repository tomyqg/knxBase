#
#
#

DROP VIEW IF EXISTS v_ArticleSalesPriceCacheForShop ;
CREATE VIEW v_ArticleSalesPriceCacheForShop AS
    SELECT ASPC.*, ROUND( ASPC.Price * (100.0 + T.Percentage) / 100.0, 2) AS SalesPriceTaxIn FROM ArticleSalesPriceCache AS ASPC
        LEFT JOIN Article AS A ON A.ArticleNo = ASPC.ArticleNo
        LEFT JOIN Tax AS T ON T.TaxClass = A.TaxClass
;
