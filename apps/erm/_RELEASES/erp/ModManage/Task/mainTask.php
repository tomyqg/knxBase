<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
?>
<div id="TaskC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="TaskC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="TaskKeyData" id="TaskKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Task no.") ; ?>:</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_ITaskNr" id="_ITaskNr" value="" onkeypress="if(event.keyCode==13){requestUni( 'ModManage', 'Task', '/Common/hdlObject.php', 'getXMLComplete', MainInputField.value, '', '', null, showTaskAll) ;}else{return true ;} return false ; "/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button type="button" data-dojo-type="dijit/form/Button" onclick="selTask( 'ModManage', 'Task', 'getXMLComplete', document.forms['TaskKeyData']._ITaskNr, showTaskAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="TaskC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="TaskTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="TaskTc1Cp0" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Overview") ; ?>">
				<div id="TaskTc1Cp1Tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="TaskDataTCOvCpTcCp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "All") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerTask', '/Common/hdlObject.php', 'getTableTasks', '', 'divTaskAll', 'TaskNr', 'gotoTask', 'retToTask', '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerTask', '/Common/hdlObject.php', 'getTableTasks', '', 'divTaskAll', 'TaskNr', 'gotoTask', 'retToTask', '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divTaskAll"></div>
					</div>
					<div id="TaskDataTCOvCpTcCp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Open") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerTask', '/Common/hdlObject.php', 'getTableTasksOpen', '', 'divTaskOpen', 'TaskNr', 'gotoTask', 'retToTask', '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerTask', '/Common/hdlObject.php', 'getTableTasksOpen', '', 'divTaskOpen', 'TaskNr', 'gotoTask', 'retToTask', '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divTaskOpen"></div>
					</div>
					<div id="TaskDataTCOvCpTcCp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Closed") ; ?>"
						onShow="requestDataMiner( 'Base', 'DataMinerTask', '/Common/hdlObject.php', 'getTableTasksClosed', '', 'divTaskClosed', 'TaskNr', 'gotoTask', 'retToTask', '', null) ; return false ; ">
						<button type="button" data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerTask', '/Common/hdlObject.php', 'getTableTasksClosed', '', 'divTaskClosed', 'TaskNr', 'gotoTask', 'retToTask', '', null) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="divTaskClosed"></div>
					</div>
				</div>
			</div>
			<div id="TaskTc1Cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formTaskMain" id="formTaskMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowOption( FTr::tr( "Priority"), "_IPriority", Task::getRTaskPrio(), "B", "") ;
								rowEdit( FTr::tr( "Responsible user"), "_IRspUserId", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Escalation user"), "_IEscUserId", 32, 32, "", "") ;
								rowDate( FTr::tr( "Date registered:"), "_IDateReg", 10, 10, "", "") ;
								rowDate( FTr::tr( "Date reminded"), "_IDateRem", 10, 10, "", "") ;
								rowDate( FTr::tr( "Date escalated"), "_IDateEsc", 10, 10, "", "") ;
								rowDate( FTr::tr( "Date finished"), "_IDateFin", 5, 5, "", "") ;
								rowOption( FTr::tr( "Status current"), "_IStatus", Task::getRTaskStat(), "0", "") ;
								rowOption( FTr::tr( "Status finished"), "_IFinStatus", Task::getRTaskFinStat(), "0", "") ;
								rowEdit( FTr::tr( "Object"), "_IObject", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Object key"), "_IObjectKey", 32, 32, "", "", "",
												"gotoArtikel( document.forms['formTaskMain']._IObjectKey.value, retToTask, document.forms['TaskKeyData']._ITaskNr.value)") ;
								rowEdit( FTr::tr( "URLpara"), "_IURLpara", 32, 32, "", "") ;
								rowEdit( FTr::tr( "URLtarget"), "_IURLtarget", 64, 64, "", "") ;
								rowTextEdit( "Remarks", "_IRem", 64, 4, "",
											"") ;
								?>
							</table>
							<button type="button" data-dojo-type="dijit/form/Button" 
								onclick="requestUni( 'ModManage', 'Task', '/Common/hdlObject.php', 'upd', document.forms['TaskKeyData']._ITaskNr.value, '', '', 'formTaskMain', showTask) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

