<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
ini_set('memory_limit','512M');

require_once 'classes/Reports.class.php';

$reports = new Reports();
$report = $reports->getReport($_GET['r']);

$report->execute();

if(isset($_GET['format'])) {
	if($_GET['format'] == 'csv') {
		header('content-type: text/csv');
		header('content-disposition:attachment; filename="'.$report->REPORT_CLASS.'-'.date('Y-m-d').'.csv"');
		echo $report->asCSV();
	}

	if($_GET['format'] == 'json') {
		header('content-type: application/json');
		//header('content-disposition:attachment; filename="'.$report->REPORT_CLASS.'-'.date('Y-m-d').'.json"');
		echo $report->asJSON();
	}

	if($_GET['format'] == 'xml') {
		header('content-type: application/xml');
		//header('content-disposition:attachment; filename="'.$report->REPORT_CLASS.'-'.date('Y-m-d').'.xml"');
		echo $report->asXML();
	}

	if($_GET['format'] == 'hc') {
		header('content-type: application/json');
		//header('content-disposition:attachment; filename="'.$report->REPORT_CLASS.'-'.date('Y-m-d').'.xml"');
		echo $report->asHighChart();
	}
}
