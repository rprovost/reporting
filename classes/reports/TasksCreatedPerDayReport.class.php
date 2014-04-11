<?php
include_once 'classes/Report.class.php';

class TasksCreatedPerDayReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME		= 'Tasks: Created Per Day';
	public $REPORT_DESC		= 'Number of tasks proposed in the last 2-weeks';
	public $REPORT_CLASS	= __CLASS__;

	public $REPORT_CHART_TYPE	= 'area';
	public $REPORT_CHART_LABEL	= 'date';
	public $REPORT_CHART_DATA	= 'tasks';

	public function execute() {		
		$this->addData('all tasks');
		$this->addData('parent', '"proposed_by_kid" = \'f\'');
		$this->addData('kid', '"proposed_by_kid" = \'t\'');
	}
	
	private function addData($heading, $extraFilters = '') {
	  $sql = 'SELECT COUNT(*) AS Tasks, DATE(tasks.created_at) AS Date FROM "tasks, versions" WHERE versions.item_type = "Task" AND versions.item_id = tasks.id AND ("tasks.created_at" > (NOW() - interval \'13 days\'))' . $this->getAccountFilterList('versions.whodunnit','and') . $this->getAccountFilterList('tasks.kid_id','and') . $extraFilters . 'GROUP BY Date ORDER BY Date';
	  $this->addDataSet('kid',parent::getData($sql));
	}
}
