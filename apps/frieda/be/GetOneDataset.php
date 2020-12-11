<?php
  require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;

  //$_POST['name'] = string TableName
  //$_POST['id'] = integer DatasetId
  FDbg::setLevel( 999) ;
	FDbg::enable() ;

  $owner = new Object();

  $DataTable = new wapDatasource($owner, (string)$_POST['name']);


  $owner.onDatasourceLoaded($_scr, $_xml)
  {
      ech (string)$_xml
  }

  $DataTable.load("", (integer)$_POST['id'], "");
/*
  $TestObject = new FDbObject((string)$_POST['name']);
  $TestObject->setId((integer)$_POST['id']);



  $DATA = $TestObject->getAsXML()->getReply();
  FDbg::trace(2, FDbg::mdTrcInfo1, "GetOneDataset.php", "Ajax", "GetOneDataset", "Reply: ".utf8_encode($DATA));
  echo (string)$DATA;
  */
?>
