<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

require_once 'classes/Reports.class.php';

$reports = new Reports();
?>
<html>
<head>
	<title>TYKOON REPORTING [beta]</title>
	<link rel="stylesheet" href="reporting.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
	<script src="js/highcharts/highcharts.js"></script>
	<script>
		$(function() {
			$( "#reports" ).sortable({ handle: '.report_name' });
		});
	</script>
</head>

<body>
	<div class="header">
		<div class="logo"><img src="images/logo.png"></div>
		<div class="title">REPORTING TOOL <span class="beta">[beta]</span></div>
	</div>
	<ul id="reports">
		<li class="notifier">
			<div class="title_line">
				<div class="report_name">Messages</div>
				<div class="report_formats">
					<a href="#" onclick="$('.notifier').fadeOut();return false;" title="Hide Messages"><img class="icon" style="width:14px;padding-top:6px;" src="images/icon_close.png"></a>
				</div>
			</div>
			<div class="notifications">
				<ol>
					<li class="notification">Added bucket balance average report. <span class="date">[2012-08-01]</span></li>
					<li class="notification">Added GDP reports. <span class="date">[2012-08-01]</span></li>
					<li class="notification">Core code refactoring for charts. <span class="date">[2012-08-01]</span></li>
					<li class="notification">Indicator for export-only reports. <span class="date">[2012-08-01]</span></li>
					<li class="notification">Added new Breakdown report. <span class="date">[2012-07-30]</span></li>
					<li class="notification">Updated reports that rely on the event table. <span class="date">[2012-07-30]</span></li>
					<li class="notification">Added ability to close/hide reports. <span class="date">[2012-07-30]</span></li>
					<li class="notification">Drag to sort/rearrange reports. <span class="date">[2012-07-30]</span></li>
					<li class="notification">Cleaned up report naming. <span class="date">[2012-07-30]</span></li>
					<li class="notification">Reports are now filtering out internal users. <span class="date">[2012-07-26]</span></li>
					<li class="notification">Added this nifty notifications area. <span class="date">[2012-07-26]</span></li>
					<li class="notification">Reports now support multiple dataset overlays. <span class="date">[2012-07-25]</span></li>
					<li class="notification">Added two task reports. <span class="date">[2012-07-25]</span></li>
				</ol>
			</div>
		</li>
	<? foreach($reports->getReports() AS $report) { ?>
		<li class="report <?= $report->REPORT_CLASS ?>">
			<div class="title_line">
				<div class="report_name">
					<?= $report->REPORT_NAME ?>
				</div>
				<div class="report_formats">
					<a href="report.php?r=<?= $report->REPORT_CLASS ?>&format=csv"><img class="icon" src="images/icon_csv.png"></a>
					<a href="report.php?r=<?= $report->REPORT_CLASS ?>&format=json"><img class="icon" src="images/icon_json.png"></a>
					<a href="report.php?r=<?= $report->REPORT_CLASS ?>&format=xml"><img class="icon" src="images/icon_xml.png"></a>
					<a href="#" onclick="$('.report.<?= $report->REPORT_CLASS ?>').fadeOut();return false;" title="Hide Report"><img class="icon" style="width:14px;padding-bottom:4px;" src="images/icon_close.png"></a>
				</div>
			</div>
			<div class="report_description">
				<?= $report->REPORT_DESC ?>

				<? if ($report instanceof HighChartable) { ?>
				<div class="highchart">
					<script>
						$(function () {
							$.getJSON('report.php?r=<?= $report->REPORT_CLASS ?>&format=hc', function(data) {
								// Set the HighChart data to a local variable
								var config = data;

								// Add HighChart display configuration information
								config.chart.renderTo		= "<?= $report->REPORT_CLASS ?>";
								config.chart.height			= ($('.<?= $report->REPORT_CLASS ?>').height() - 26);
								config.legend.y				= -16;

								// Render the Highchart graph
								var chart = new Highcharts.Chart(config);

								console.log(config);
							});
						});
					</script>
					<div id="<?= $report->REPORT_CLASS ?>" style="max-height:350px;max-width:350px;margin: 0 auto"></div>
				</div>
				<? } else { ?>
				<div class="nochart">No Chart Available</div>
				<? } ?>
			</div>
		</li>
	<? } ?>
	</ul>
<body>
</html>
