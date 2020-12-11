<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<div id="UserBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="UserCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="UserKeyData" id="UserKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "User id") ; ?>:</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IUserId" id="_IUserId" value=""
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" onclick="selUser( 'ModSys', 'User', document.forms['UserKeyData']._IUserId.value, 'getXMLComplete', showUserAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Name") ; ?></th>
							<td colspan="4">
								<input type="text" name="_DFirstName" id="VOID" size="35" value="" /><br/>
								<input type="text" name="_DLastName" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="UserCPMain" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="UserTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="UserTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form name="formUserMain" id="formUserMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Userid"), "_IUserId", 16, 16, "", "") ;
								rowEdit( FTr::tr( "Organization name"), "_IOrgName1", 32, 32, "", "") ;
								rowEdit( FTr::tr( "more Organization name"), "_IOrgName2", 32, 32, "", "") ;
								rowEdit( FTr::tr( "First (given) name"), "_IFirstName", 16, 32, "", "") ;
								rowEdit( FTr::tr( "Last name"), "_ILastName", 16, 32, "", "") ;
								rowEditDbl( FTr::tr( "Street / No."), "_IStreet", 20, 32, "", "_INumber", 6, 10, "", "") ;
								rowEditDbl( FTr::tr( "ZIP / City"), "_IZIP", 6, 8, "", "_ICity", 20, 32, "", "") ;
								rowEdit( FTr::tr( "Province"), "_IProvince", 16, 32, "", "") ;
								rowOption( FTr::tr( "Country"), "_ICountry", Opt::getRLaender(), "de", "") ;
								rowOption( FTr::tr( "Language"), "_ILang", Opt::getRLangCodes(), "de_de", "") ;
								rowEdit( FTr::tr( "Phone"), "_ITelephone", 20, 32, "", "") ;
								rowEdit( FTr::tr( "Fax"), "_IFAX", 20, 32, "", "") ;
								rowEdit( FTr::tr( "Mobil"), "_ICellphone", 20, 32, "", "") ;
								rowEdit( FTr::tr( "E-Mail"), "_IeMail", 20, 32, "", "") ;
								?>
							</table> 
							<button type="button" data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'add', 'formUserMain') ;">
								<?php echo FTr::tr( "Create") ;?>
							</button>
							<button type="button" data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formUserMain') ;">
								<?php echo FTr::tr( "Update") ;?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="UserTc1cp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Access Rights") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form name="formUserModi" id="formUserModi" enctype="multipart/form-data">
							<table><?php
								rowDate( FTr::tr( "Date:"), "_IRegistration", 10, 10, "", "") ;
								rowDate( FTr::tr( "Valid from"), "_IValidFrom", 10, 10, "", "") ;
								rowDate( FTr::tr( "Valid to"), "_IValidTo", 10, 10, "", "") ;
								rowDate( FTr::tr( "Last Access"), "_IDateLastAcc", 10, 10, "", "") ;
								rowTextEdit( FTr::tr( "Package(s)"), "_IPackages", 40, 6, "", "MasterData, Purchasing, Sales,<br/>Logistics, Functions, Other,<br/>ModSys") ;
								rowTextEdit( FTr::tr( "Module(s)"), "_IModules", 40, 6, "", "") ;
								?>
							</table> 
							<button type="button" data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formUserModi') ;">
								<?php echo FTr::tr( "Update") ;?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="UserTc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Password") ; ?>">
				<div id="content">
					<div id="maindata">
						<form name="formUserAccess" id="formUserAccess" enctype="multipart/form-data">
							<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
							<table><?php
								rowEdit( FTr::tr( "Password"), "_IPassword", 24, 64, "", "") ;
								rowEdit( FTr::tr( "MD5Password"), "_IMD5Password", 24, 64, "", "") ;
								?>
							</table> 
							<br/>
							<button type="button" data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formUserAccess') ;">
								<?php echo FTr::tr( "Update") ;?>
							</button>
						</form> 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>