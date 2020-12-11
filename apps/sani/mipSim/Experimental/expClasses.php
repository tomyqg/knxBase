<?php
/**
 * Created by PhpStorm.
 * User: khwelter
 * Date: 06.09.2017
 * Time: 11:36
 */

require_once( "khClasses.php") ;                    // auftrag:         Auftragsdaten
require_once( "khAuftragAdapter.php") ;             // auftrag:         Auftragsdaten
require_once( "khAuftrag.php") ;                // auftrag:         Auftragsdaten
require_once( "khAuftragPositionAdapter.php") ;    // auftrag:         Auftragsdaten
require_once( "khAuftragPosition.php") ;        // auftrag:         Auftragsdaten
require_once( "khAuftragWFAdapter.php") ;            // auftrag:         Auftragsdaten
require_once( "khAuftragWF.php") ;            // auftrag:         Auftragsdaten
require_once( "khFibuLog.php") ;
require_once( "HMVcloud.php") ;             // *:               HMV-Tabellen
require_once( "khKassenBelegAdapter.php") ;
require_once( "khKassenBeleg.php") ;
require_once( "khKassenBelegPositionAdapter.php") ;
require_once( "khKassenBelegPosition.php") ;
require_once( "khMwst.php") ;               // Mehrwertsteuers�tze
require_once( "khSKRKonto.php") ;           // fibu_cfg:        Kontenrahmen Konten, nur eine Referenz zur Umsetzung
require_once( "khWarengruppe.php") ;        // warengruppen:
require_once( "khWorkflowStep.php") ;       // auftrag_wf:
require_once( "MIPKostenvoranschlag.php") ;                // auftrag_wf:
require_once( "MIPAnhang.php") ;                // auftrag_wf:
require_once( "MIPEigenanteil.php") ;                // auftrag_wf:
require_once( "MIPZuzahlung.php") ;                // auftrag_wf:
require_once( "MIPPosition.php") ;                // auftrag_wf:
require_once( "MIPVersorgung.php") ;                // auftrag_wf:
require_once( "MIPVersicherter.php") ;                // auftrag_wf:
require_once( "MIPZusatzanschrift.php") ;                // auftrag_wf:
