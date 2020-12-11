#
#	VIEWs.sql
#	=========
#
#	Path:	lib/sys/SQL/
#
#	Product id.:
#	Version:
#
#	Revision history
#
#	Date			Rev.	Who		what
#	----------------------------------------------------------------------------
#	2015-03-23				khw		inception;
#
#	ToDo
#
#	Date			what
#	----------------------------------------------------------------------------
#
#	@package		??
#	@subpackage	System
#	@author		khwelter
#

#
#
#

DROP VIEW IF EXISTS AddressSurvey ;
CREATE VIEW AddressSurvey AS
	SELECT C.Id, C.AddressNo, CONCAT( C.Name1, ", ", C.Name2) AS Name, C.ZIP, CONCAT( CC.FirstName, " ", CC.LastName) AS Contact
		FROM Address AS C
		LEFT JOIN AddressContact AS CC on CC.AddressNo = C.AddressNo ;

DROP VIEW IF EXISTS AddressContactSurvey ;
CREATE VIEW AddressContactSurvey AS
	SELECT CC.Id, C.AddressNo, CC.AddressContactNo, CONCAT( C.Name1, ", ", C.Name2) AS Name, CONCAT( CC.Salutation, ' ', CC.Title, ' ', CC.FirstName, ' ', CC.LastName) AS Contact
		FROM Address AS C
		JOIN AddressContact AS CC ON CC.AddressNo = C.AddressNo ;

DROP VIEW IF EXISTS ArticleSurvey ;
CREATE VIEW ArticleSurvey AS
	SELECT A.Id, A.ArticleNo, CONCAT( A.ArticleDescription1, ", ", A.ArticleDescription2) AS Description
		FROM Article AS A ;


DROP VIEW IF EXISTS SupplierSurvey ;
CREATE VIEW SupplierSurvey AS
	SELECT S.Id, S.SupplierNo, SC.SupplierContactNo, CONCAT( S.SupplierName1, ", ", S.SupplierName2) AS Name, S.ZIP, CONCAT( SC.FirstName, " ", SC.LastName) AS Contact
		FROM Supplier AS S
		LEFT JOIN SupplierContact AS SC on SC.SupplierNo = S.SupplierNo ;

DROP VIEW IF EXISTS SupplierContactSurvey ;
CREATE VIEW SupplierContactSurvey AS
	SELECT SC.*, CONCAT( SC.Salutation, ' ', SC.Title, ' ', SC.FirstName, ' ', SC.LastName) AS Contact
		FROM SupplierContact AS SC ;

DROP VIEW IF EXISTS CustomerSurvey ;
CREATE VIEW CustomerSurvey AS
	SELECT C.Id, C.CustomerNo, CONCAT( C.CustomerName1, ", ", C.CustomerName2) AS Name, C.ZIP, CONCAT( CC.FirstName, " ", CC.LastName) AS Contact
		FROM Customer AS C
		LEFT JOIN CustomerContact AS CC on CC.CustomerNo = C.CustomerNo ;

DROP VIEW IF EXISTS CustomerContactSurvey ;
DROP VIEW IF EXISTS v_CustomerContactSurvey ;
CREATE VIEW v_CustomerContactSurvey AS
	SELECT CC.Id, C.CustomerNo, CC.CustomerContactNo, CONCAT( C.CustomerName1, ", ", C.CustomerName2) AS Name, CONCAT( CC.Salutation, ' ', CC.Title, ' ', CC.FirstName, ' ', CC.LastName) AS Contact, CC.FirstName, CC.LastName
		FROM Customer AS C
		JOIN CustomerContact AS CC ON CC.CustomerNo = C.CustomerNo ;

DROP VIEW IF EXISTS CustomerOrderSurvey ;
CREATE VIEW CustomerOrderSurvey AS
	SELECT CO.Id, CO.CustomerOrderNo,CO.CustomerNo, CO.CustomerContactNo, CONCAT( C.CustomerName1, ", ", C.CustomerName2) AS Name, CONCAT( CC.Salutation, ' ', CC.Title, ' ', CC.FirstName, ' ', CC.LastName) AS Contact
		FROM CustomerOrder AS CO
	 	LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo ;

DROP VIEW IF EXISTS CustomerOrderItemList ;
CREATE VIEW CustomerOrderItemList AS
	SELECT COI.Id, COI.CustomerOrderNo, COI.ItemNo, COI.ArticleNo,COI.Quantity, COI.Price, CONCAT( A.ArticleDescription1, ", ", A.ArticleDescription2) AS ArticleDescription
		FROM CustomerOrderItem AS COI
	 	LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo

DROP VIEW IF EXISTS ProductGroupItemList ;
CREATE VIEW ProductGroupItemList AS
	SELECT PGI.Id, PGI.ProductGroupNo, PGI.ItemNo, PGI.CompProductGroupNo, PG.ProductGroupName, PGI.CompArticleGroupNo, AG.ArticleGroupName, PGI.CompArticleNo, A.ArticleDescription1 FROM ProductGroupItem AS PGI
		LEFT JOIN ProductGroup AS PG ON PG.ProductGroupNo = PGI.CompProductGroupNo
		LEFT JOIN ArticleGroup AS AG ON AG.ArticleGroupNo = PGI.CompArticleGroupNo
		LEFT JOIN Article AS A ON A.ArticleNo = PGI.CompArticleNo ;

DROP VIEW IF EXISTS ArticleGroupItemList ;
CREATE VIEW ArticleGroupItemList AS
	SELECT AGI.Id, AGI.ArticleGroupNo, AGI.ItemNo, AGI.CompProductGroupNo, PG.ProductGroupName, AGI.CompArticleGroupNo, AG.ArticleGroupName, AGI.CompArticleNo, A.ArticleDescription1 FROM ArticleGroupItem AS AGI
		LEFT JOIN ProductGroup AS PG ON PG.ProductGroupNo = AGI.CompProductGroupNo
		LEFT JOIN ArticleGroup AS AG ON AG.ArticleGroupNo = AGI.CompArticleGroupNo
		LEFT JOIN Article AS A ON A.ArticleNo = AGI.CompArticleNo ;
#
#
#
