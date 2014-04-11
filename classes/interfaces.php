<?

interface Reportable {
	public function execute();
	public function asCSV();
	public function asJSON();
	public function asXML();
}

interface HighChartable {
	public function asHighChart();
}