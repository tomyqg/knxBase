<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<div id="JobsC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="JobsC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="JobsKeyData" id="JobsKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Job id") ; ?>:</th>
							<td>
								<input type="image" src="/licon/yellow/18/left.png" onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IId" id="_IId" value="" onkeypress="if(event.keyCode==13){requestUni( 'ModSys', 'Jobs', '/Common/hdlObject.php', 'getXMLComplete', document.forms['JobsKeyData']._IId.value, '', '', null, showJobsAll) ;}else{return true ;} return false ; "/>
							</td>
							<td>
								<input type="image" src="/licon/yellow/18/right.png" onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button type="button" data-dojo-type="dijit/form/Button" onclick="selJobs( 'ModSys', 'Jobs', 'getXMLComplete', document.forms['JobsKeyData']._IId, showJobsAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td class="space" width="200"></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="JobsC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="JobsTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="JobsTc1Cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Overview") ; ?>">
				<div id="JobsTc1Cp1Tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="JobsDataTCOvCpTcCp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Hourly") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsHourly', '', 'divJobsHourly', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsHourly', '', 'divJobsHourly', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divJobsHourly"></div>
					</div>
					<div id="JobsDataTCOvCpTcCp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Daily") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsDaily', '', 'divJobsDaily', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsDaily', '', 'divJobsDaily', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divJobsDaily"></div>
					</div>
					<div id="JobsDataTCOvCpTcCp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Weekly") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsWeekly', '', 'divJobsWeekly', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsWeekly', '', 'divJobsWeekly', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divJobsWeekly"></div>
					</div>
					<div id="JobsDataTCOvCpTcCp4" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Monthly") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsMonthly', '', 'divJobsMonthly', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsMonthly', '', 'divJobsMonthly', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divJobsMonthly"></div>
					</div>
					<div id="JobsDataTCOvCpTcCp5" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Interval") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsInterval', '', 'divJobsInterval', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerJobs', '/Common/hdlObject.php', 'getTableJobsInterval', '', 'divJobsInterval', 'Id', 'screenJobs', 'retToJobs', '', value, '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divJobsInterval"></div>
					</div>
				</div>
			</div>
			<div id="JobsTc2Cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formJobsMain" id="formJobsMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowOption( FTr::tr( "Schedule"), "_ISchedule", Jobs::getRSchedule(), "daily", "") ;
								rowEdit( FTr::tr( "Position"), "_IPosition", 5, 5, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", Jobs::getRStatus(), "0", "") ;
								rowEdit( FTr::tr( "Job name"), "_IJobName", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Script"), "_IScript", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Parameters"), "_IParameters", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Logfile"), "_ILogfile", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Mail receiver"), "_IMailRcvr", 64, 64, "", "") ;
								rowEdit( FTr::tr( "opt. interval"), "_IPeriode", 8, 8, "", "") ;
								rowDisplay( FTr::tr( "Last run"), "_ILastRun", 32, 32, "", "") ;
								rowDisplay( FTr::tr( "Last duration"), "_ILastDuration", 32, 32, "", "") ;
								?>
							</table>
							<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModSys', 'Jobs', '/Common/hdlObject.php', 'upd', document.forms['JobsKeyData']._IId.value, '', '', 'formJobsMain', showJobs) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="JobsTc2Cp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Functions") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<button type="button" data-dojo-type="dijit/form/Button"
							onClick="requestScript( 'scripts', 'export fullSiteName=<?php echo $fullSiteName ; ?> ; ' + document.forms['formJobsMain']._IScript.value + ' ' , null, null) ;">
							<?php echo FTr::tr( "Run job") ; ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

