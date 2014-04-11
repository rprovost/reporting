<?php
include_once 'classes/Report.class.php';

class BreakdownTasksReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME			= 'Breakdown Report: Tasks';
	public $REPORT_DESC			= 'A breakdown of active tasks by state for the last 2-weeks';
	public $REPORT_CLASS		= __CLASS__;

	public $REPORT_CHART_TYPE	= 'bar';
	public $REPORT_CHART_LABEL	= 'state';
	public $REPORT_CHART_DATA	= 'count';

	public $hcDisplayLegend		= true;

	public function execute() {
		$totalTasks	= 0;
		$taskState	= array();

    $tasks = $this->queryTasks();

		// Get the total number of tasks (no need for two queries when we can calculate this easily enough)
		foreach ($tasks AS $state) {
			$totalTasks += $state['count'];

			array_push($taskState,array(
					'state' => $state['state'],
					'count'	=> $state['count']
				));
		}

		// sort the states alphabetically
		$this->aasort($taskState,'state');

		// add the total to the array
		array_unshift($taskState,array(
				'state' => 'total',
				'count'	=> $totalTasks
			));

		// add everything to the return
		$this->addDataSet('all users',$taskState);


		//**************************************************************************************************************
		$totalTasks	= 0;
		$taskState	= array();

    $tasks = $this->queryTasks('"proposed_by_kid" = \'f\'');

		// Get the total number of tasks (no need for two queries when we can calculate this easily enough)
		foreach ($tasks AS $state) {
			$totalTasks += $state['count'];

			array_push($taskState,array(
					'state' => $state['state'],
					'count'	=> $state['count']
				));
		}

		// sort the states alphabetically
		$this->aasort($taskState,'state');

		// add the total to the array
		array_unshift($taskState,array(
				'state' => 'total',
				'count'	=> $totalTasks
			));

		// add everything to the return
		$this->addDataSet('parents',$taskState);


		//**************************************************************************************************************
		$totalTasks	= 0;
		$taskState	= array();

    // $sql = 'SELECT COUNT(*) AS Count, "state" AS State FROM "tasks" WHERE "proposed_by_kid" = \'t\' AND ("updated_at" > (NOW() - interval \'13 days\'))' . $this->getAccountFilterList('adult_id','and') . $this->getAccountFilterList('kid_id','and') . 'GROUP BY State';
    // $tasks = parent::getData($sql);
    $tasks = $this->queryTasks('"proposed_by_kid" = \'t\'');

		// Get the total number of tasks (no need for two queries when we can calculate this easily enough)
		foreach ($tasks AS $state) {
			$totalTasks += $state['count'];

			array_push($taskState,array(
					'state' => $state['state'],
					'count'	=> $state['count']
				));
		}

		// sort the states alphabetically
		$this->aasort($taskState,'state');

		// add the total to the array
		array_unshift($taskState,array(
				'state' => 'total',
				'count'	=> $totalTasks
			));

		// add everything to the return
		$this->addDataSet('kids',$taskState);

	}

	// jacked from stackoverflow
	function aasort (&$array, $key) {
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		$array=$ret;
	}
	
	private function queryTasks($extraFilters = '') {
	  $sql = 'SELECT COUNT(*) AS Count, status AS State FROM tasks, versions WHERE versions.item_type = "Task" AND versions.item_id = tasks.id AND (tasks.updated_at > (NOW() - interval \'13 days\'))' . $this->getAccountFilterList('versions.whodunnit','and') . $this->getAccountFilterList('tasks.kid_id','and') . 'GROUP BY State';
		$tasks = parent::getData($sql);
	}
}
