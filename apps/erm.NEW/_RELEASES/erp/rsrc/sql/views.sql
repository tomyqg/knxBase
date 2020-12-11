#
# list all AppUser having Role
#

#
# Address related views
#

DROP VIEW IF EXISTS v_AddressAddressContactSurvey ;
CREATE VIEW v_AddressAddressContactSurvey AS
	SELECT A.Id, A.AddressNo, AC.AddressContactNo, CONCAT( A.Name1, " ", A.Name2) AS AddressName, CONCAT( AC.FirstName, " ", AC.LastName) AS Name
		FROM Address AS A
		LEFT JOIN AddressContact AS AC on AC.AddressNo = A.AddressNo
	ORDER BY A.AddressNo, AC.AddressContactNo
;

DROP VIEW IF EXISTS v_AddressContactSurvey ;
CREATE VIEW v_AddressContactSurvey AS
	SELECT AC.Id, AC.AddressNo, AC.AddressContactNo, AC.FirstName, AC.LastName
		FROM AddressContact AS AC
	ORDER BY AC.AddressContactNo
;

#
# Customer related views
#

DROP VIEW IF EXISTS v_CustomerCustomerContactSurvey ;
CREATE VIEW v_CustomerCustomerContactSurvey AS
	SELECT C.Id, C.CustomerNo, CC.CustomerContactNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS CustomerName, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM Customer AS C
		LEFT JOIN CustomerContact AS CC on CC.CustomerNo = C.CustomerNo
	ORDER BY C.CustomerNo, CC.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerContactSurvey ;
CREATE VIEW v_CustomerContactSurvey AS
	SELECT CC.Id, CC.CustomerNo, CC.CustomerContactNo, CC.FirstName, CC.LastName
		FROM CustomerContact AS CC
	ORDER BY CC.CustomerContactNo
;

#
# Supplier related views
#

DROP VIEW IF EXISTS v_SupplierSupplierContactSurvey ;
CREATE VIEW v_SupplierSupplierContactSurvey AS
	SELECT S.Id, S.SupplierNo, SC.SupplierContactNo, CONCAT( S.SupplierName1, " ", S.SupplierName2) AS SupplierName, CONCAT( SC.FirstName, " ", SC.LastName) AS Name
		FROM Supplier AS S
		LEFT JOIN SupplierContact AS SC on SC.SupplierNo = S.SupplierNo
	ORDER BY S.SupplierNo, SC.SupplierContactNo
;

DROP VIEW IF EXISTS v_SupplierContactSurvey ;
CREATE VIEW v_SupplierContactSurvey AS
	SELECT SC.Id, SC.SupplierNo, SC.SupplierContactNo, SC.FirstName, SC.LastName
		FROM SupplierContact AS SC
	ORDER BY SC.SupplierContactNo
;

DROP VIEW IF EXISTS v_SupplierDiscountSurvey ;
CREATE VIEW v_SupplierDiscountSurvey AS
	SELECT SD.Id, SD.SupplierNo, SD.DiscountClass, SD.Quantity, SD.Discount
		FROM SupplierDiscount AS SD
	ORDER BY SD.DiscountClass
;

#
# Article related views
#

DROP VIEW IF EXISTS v_ArticleSurvey ;
CREATE VIEW v_ArticleSurvey AS
	SELECT A.Id, A.ArticleNo, A.ERPNo, A.ArticleDescription1, A.ArticleDescription2
		FROM Article AS A
	ORDER BY A.ArticleNo
;
DROP VIEW IF EXISTS v_ArticlePurchasePriceRelSurvey ;
CREATE VIEW v_ArticlePurchasePriceRelSurvey AS
	SELECT APPR.*
		FROM ArticlePurchasePriceRel AS APPR
;
DROP VIEW IF EXISTS v_ArticlePurchasePriceSurvey ;
CREATE VIEW v_ArticlePurchasePriceSurvey AS
	SELECT APP.*, APPR.ArticleNo
		FROM ArticlePurchasePrice AS APP
		LEFT JOIN ArticlePurchasePriceRel AS APPR ON APPR.SupplierNo = APP.SupplierNo AND APPR.SupplierArticleNo = APP.SupplierArticleNo
;
DROP VIEW IF EXISTS v_ArticleSalesPriceCache_1 ;
CREATE VIEW v_ArticleSalesPriceCache_1 AS
	SELECT ASPC.Id, ASPC.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS Description, ASPC.QuantityPerPU, ASPC.Quantity, ASPC.Price, ASPC.MarketId
		FROM ArticleSalesPriceCache AS ASPC
		LEFT JOIN Article AS A ON A.ArticleNo = ASPC.ArticleNo ;
;
#
# Module:	Sales
#

#
# Main Class:	CustomerCart
# Class:
#

DROP VIEW IF EXISTS v_CustomerCartSurvey ;
CREATE VIEW v_CustomerCartSurvey AS
	SELECT CO.Id, CO.CustomerCartNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerCart AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerCartItemList ;
CREATE VIEW v_CustomerCartItemList AS
	SELECT COI.Id, COI.CustomerCartNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.Quantity, COI.Price
		FROM CustomerCartItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

#
# Main Class:	CustomerRFQ
# Class:
#

DROP VIEW IF EXISTS v_CustomerRFQSurvey ;
CREATE VIEW v_CustomerRFQSurvey AS
	SELECT CO.Id, CO.CustomerRFQNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerRFQ AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerRFQItemList ;
CREATE VIEW v_CustomerRFQItemList AS
	SELECT COI.Id, COI.CustomerRFQNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.Quantity, COI.Price
		FROM CustomerRFQItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

#
# Main Class:	CustomerOffer
# Class:
#

DROP VIEW IF EXISTS v_CustomerOfferSurvey ;
CREATE VIEW v_CustomerOfferSurvey AS
	SELECT CO.Id, CO.CustomerOfferNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerOffer AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerOfferItemList ;
CREATE VIEW v_CustomerOfferItemList AS
	SELECT COI.Id, COI.CustomerOfferNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.Quantity, COI.Price
		FROM CustomerOfferItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

#
# Main Class:	CustomerTempOrder
# Class:
#

DROP VIEW IF EXISTS v_CustomerTempOrderSurvey ;
CREATE VIEW v_CustomerTempOrderSurvey AS
	SELECT CO.Id, CO.CustomerOrderNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerTempOrder AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerTempOrderItemList ;
CREATE VIEW v_CustomerTempOrderItemList AS
	SELECT COI.Id, COI.CustomerOrderNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.Quantity, COI.QuantityDelivered, COI.QuantityInvoiced, COI.Price
		FROM CustomerTempOrderItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

#
# Main Class:	CustomerOrder
# Class:
#

DROP VIEW IF EXISTS v_CustomerOrderSurvey ;
CREATE VIEW v_CustomerOrderSurvey AS
	SELECT CO.Id, CO.CustomerOrderNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerOrder AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerOrderItemList ;
CREATE VIEW v_CustomerOrderItemList AS
	SELECT COI.Id, COI.CustomerOrderNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.Quantity, COI.QuantityDelivered, COI.QuantityInvoiced, COI.Price
		FROM CustomerOrderItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

# Usage:	consolidation of CustomerOrder

DROP VIEW IF EXISTS v_CustomerOrderItemConsolidateOrder ;
CREATE VIEW v_CustomerOrderItemConsolidateOrder AS
	SELECT COIC.Id, COIC.ItemNo, COIC.SubItemNo, COIC.CustomerOrderNo, COIC.Quantity, COIC.Price, COIC.TotalPrice, A.TaxClass, T.Percentage
		FROM CustomerOrderItem AS COIC
			JOIN Article AS A ON A.ArticleNo = COIC.ArticleNo
			JOIN Tax AS T ON T.TaxClass = A.TaxClass
;
DROP VIEW IF EXISTS v_CustomerOrderItemConsolidateDeliveries ;
CREATE VIEW v_CustomerOrderItemConsolidateDeliveries AS
	SELECT COIC.Id, COIC.CustomerOrderNo, COIC.ItemNo, COIC.SubItemNo, COIC.ArticleNo, SUM( CDI.QuantityDelivered) AS DeliveredTotal
		FROM CustomerOrderItem AS COIC
			JOIN CustomerDelivery AS CD ON CD.CustomerOrderNo = COIC.CustomerOrderNo
			JOIN CustomerDeliveryItem AS CDI ON CDI.ItemNo = COIC.ItemNo AND CDI.ArticleNo = COIC.ArticleNo AND CDI.CustomerDeliveryNo = CD.CustomerDeliveryNo AND  CDI.CustomerDeliveryNo IS NOT NULL
;
DROP VIEW IF EXISTS v_CustomerOrderItemConsolidateInvoices ;
CREATE VIEW v_CustomerOrderItemConsolidateInvoices AS
	SELECT COIC.Id, COIC.CustomerOrderNo, COIC.ItemNo, COIC.SubItemNo, COIC.ArticleNo, SUM( CII.QuantityInvoiced) AS InvoicedTotal
		FROM CustomerOrderItem AS COIC
			JOIN CustomerInvoice AS CI ON CI.CustomerOrderNo = COIC.CustomerOrderNo
			JOIN CustomerInvoiceItem AS CII ON CII.ItemNo = COIC.ItemNo AND CII.ArticleNo = COIC.ArticleNo AND CII.CustomerInvoiceNo = CI.CustomerInvoiceNo AND  CII.CustomerInvoiceNo IS NOT NULL
;

#
# Main Class:	CustomerCommission
# Class:
#

DROP VIEW IF EXISTS v_CustomerCommissionSurvey ;
CREATE VIEW v_CustomerCommissionSurvey AS
	SELECT CO.Id, CO.CustomerCommissionNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerCommission AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerCommissionItemList ;
CREATE VIEW v_CustomerCommissionItemList AS
	SELECT COI.Id, COI.CustomerCommissionNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.Quantity, COI.QuantityDeliveredAlready, COI.QuantityDelivered, COI.Price
		FROM CustomerCommissionItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

#
# Main Class:	CustomerDelivery
# Class:
#

DROP VIEW IF EXISTS v_CustomerDeliverySurvey ;
CREATE VIEW v_CustomerDeliverySurvey AS
	SELECT CO.Id, CO.CustomerDeliveryNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerDelivery AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerDeliveryItemList ;
CREATE VIEW v_CustomerDeliveryItemList AS
	SELECT COI.Id, COI.CustomerDeliveryNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.Quantity, COI.QuantityDelivered, COI.QuantityInvoiced, COI.Price
		FROM CustomerDeliveryItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

#
# Main Class:	CustomerInvoice
# Class:
#

DROP VIEW IF EXISTS v_CustomerInvoiceSurvey ;
CREATE VIEW v_CustomerInvoiceSurvey AS
	SELECT CO.Id, CO.CustomerInvoiceNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS Customer, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM CustomerInvoice AS CO
		LEFT JOIN Customer AS C ON C.CustomerNo = CO.CustomerNo
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CO.CustomerNo AND CC.CustomerContactNo = CO.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerInvoiceItemList ;
CREATE VIEW v_CustomerInvoiceItemList AS
	SELECT COI.Id, COI.CustomerInvoiceNo, COI.ItemNo, COI.SubItemNo, COI.ArticleNo, CONCAT( A.ArticleDescription1, " ", A.ArticleDescription2) AS ArticleDescription, COI.CustomerDeliveryNo, COI.Quantity, COI.QuantityInvoiced, COI.Price
		FROM CustomerInvoiceItem AS COI
		LEFT JOIN Article AS A ON A.ArticleNo = COI.ArticleNo
;

#
# Main Class:	CustomerDelivery
# Class:
#

DROP VIEW IF EXISTS v_TaxSurvey ;
CREATE VIEW v_TaxSurvey AS
	SELECT T.*
		FROM Tax AS T
;
