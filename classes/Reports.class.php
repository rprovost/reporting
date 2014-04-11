<?
include_once 'classes/interfaces.php';

class Reports {

	const REPORT_PATH = 'classes/reports';

	function __construct() {}

	function getReport($report){
		include_once self::REPORT_PATH.'/'.$report.'.class.php';
		$report = new $report;
		return $report;
	}

	function getReports() {
		$reports	= array();
		$filters	= array('.','..');
		$files		= scandir(self::REPORT_PATH);

		foreach($files AS $file) {
			if(!in_array($file,$filters)){
				include_once self::REPORT_PATH.'/'.$file;

				$pieces = explode('.',$file);
				$class = $pieces[0];

				// attempt to instantiate the class
				$report = new $class;

				// if the class instantiates and is Reportable, it's valid
				if($report instanceof Reportable) {
					$reports[] = new $class;
				}
			}
		}

		return $reports;
	}
}
