<?php

//DATABASE
define("DB_DRIVER", "mysql");
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_DATABASE", "gpxbeta");
define("DB_CHARSET", "utf8");

//ERROR HANDLING
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//
define("DEFAULT_CONTROLLER","index");
define("DEFAULT_ACTION","login");

define("PATH",$_SERVER['DOCUMENT_ROOT']);

//TABLES
define("BOXTYPE", "gpx_boxtype");
define("CHARTACCOUNT", "gpx_chartaccounts");
define("SALARYCOMPENSATION", "gpx_salary_compensation");
define("ALLOWANCEDISBURSEMENT", "gpx_allowance_disbursement");
define("FINANCIALLIQUIDATION", "gpx_financial_liquidation");
define("REMITTANCE", "gpx_remittance");
define("USERS", "gpx_users");


?>