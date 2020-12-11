<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="Merchant_ebaBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="Merchant_ebayCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="Merchant_EbayKeyData" id="Merchant_EbayKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Merchant id.") ; ?>:</th>
							<td>
								<input type="text" name="_IMerchant_EbayId" id="_IMerchant_EbayId" value="ebay"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="Merchant_ebayCPData" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="Merchant_EbayTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="Merchant_EbayTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="Orders">
				<div id="content">
					<div id="depdata">
						<div id="TableEbay_OrderRoot">
							<?php tableBlock( "itemViews['dtvEbay_Order']", "formEbay_OrderTop") ; ?>
							<table id="TableEbay_Order" eissClass="Ebay_Order" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="OrderID"><?php echo FTr::tr( "Order id.") ; ?></th>
										<th eissAttribute="BuyerUserID"><?php echo FTr::tr( "Buyer user id.") ; ?></th>
										<th eissAttribute="PaidTime"><?php echo FTr::tr( "Date paid") ; ?></th>
										<th eissAttribute="ShippedTime"><?php echo FTr::tr( "Date shipped.") ; ?></th>
										<th eissAttribute="_AddressID"><?php echo FTr::tr( "Ebay shipping address id.") ; ?></th>
										<th eissAttribute="_CuOrdrNo" eissLinkTo="KdBest" colspan="3" eissFunctions="custom" eissCustFunc="insertOrder"><?php echo FTr::tr( "Own order no.") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="Merchant_EbayTc1cp2" data-dojo-type="dijit/layout/ContentPane" title="Items">
				<div id="content">
					<div id="depdata">
						<div id="TableEbay_ItemRoot">
							<?php tableBlock( "itemViews['dtvEbay_Item']", "formEbay_ItemTop") ; ?>
							<table id="TableEbay_Item" eissClass="Ebay_Item" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="ItemID"><?php echo FTr::tr( "Item id.") ; ?></th>
										<th eissAttribute="SKU"><?php echo FTr::tr( "SKU ie. Article no.") ; ?></th>
										<th eissAttribute="_TransactionID"><?php echo FTr::tr( "Ebay transaction id.") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="Merchant_EbayTc1cp3" data-dojo-type="dijit/layout/ContentPane" title="Transactions" onshow=";">
				<div id="content">
					<div id="depdata">
						<div id="Ebay_TransactionRoot">
							<?php tableBlock( "itemViews['dtvEbay_Transaction']", "formEbay_TransactionTop") ; ?>
							<table id="TableEbay_Transaction" eissClass="Ebay_Transaction" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="TransactionID"><?php echo FTr::tr( "Transaction Id.") ; ?></th>
										<th eissAttribute="Email"><?php echo FTr::tr( "Buyer.Email") ; ?></th>
										<th eissAttribute="CreatedDate"><?php echo FTr::tr( "Date") ; ?></th>
										<th eissAttribute="SKU" eissLinkTo="Artikel" colspan="2"><?php echo FTr::tr( "Article No.") ; ?></th>
										<th eissAttribute="ArticleDescr1"><?php echo FTr::tr( "Description") ; ?></th>
										<th eissAttribute="QuantityPurchased"><?php echo FTr::tr( "Quantity") ; ?></th>
										<th eissAttribute="_OrderID"><?php echo FTr::tr( "Ebay order id.") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="Merchant_EbayTc1cp4" data-dojo-type="dijit/layout/ContentPane" title="Addresses">
				<div id="content">
					<div id="depdata">
						<div id="TableEbay_ShippingAddressRoot">
							<?php tableBlock( "itemViews['dtvEbay_ShippingAddress']", "formEbay_ShippingAddressTop") ; ?>
							<table id="TableEbay_ShippingAddress" eissClass="Ebay_ShippingAddress" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="AddressID"><?php echo FTr::tr( "Address Id.") ; ?></th>
										<th eissAttribute="Name"><?php echo FTr::tr( "Name") ; ?></th>
										<th eissAttribute="Street1"><?php echo FTr::tr( "Street 1") ; ?></th>
										<th eissAttribute="Street2"><?php echo FTr::tr( "Street 2") ; ?></th>
										<th eissAttribute="PostalCode"><?php echo FTr::tr( "ZIP") ; ?></th>
										<th eissAttribute="CityName"><?php echo FTr::tr( "City") ; ?></th>
										<th eissAttribute="_CustNo" eissLinkTo="Kunde" colspan="3" eissFunctions="custom" eissCustFnc="insertCustomer"><?php echo FTr::tr( "Own customer no.") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

