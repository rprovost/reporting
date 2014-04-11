<?php
include_once 'classes/Report.class.php';

class UsersSessionsPerDayReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME			= 'Users: Sessions Per Day';
	public $REPORT_DESC			= 'How many user sessions in each day for the 2-weeks';
	public $REPORT_CLASS		= __CLASS__;
	public $REPORT_CHART_TYPE	= 'area';

	public $REPORT_CHART_LABEL	= 'date';
	public $REPORT_CHART_DATA	= 'sessions';

	protected $period 			= '13 days';

	public function execute() {
		$sql = 'SELECT COUNT(*) AS Sessions, DATE(created_at) AS Date FROM "user_activity_logs" WHERE "user_activity_logs"."action" = 1 AND ("created_at" > (NOW() - interval \''.$this->period.'\'))' . $this->getAccountFilterList('user_id','and') . 'GROUP BY Date ORDER BY Date';
		$this->addDataSet('all users',parent::getData($sql));

		$sql = 'SELECT COUNT(*) AS Sessions, DATE(user_activity_logs.created_at) AS Date FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->period.'\')) AND "type" = \'Adult\'' . $this->getAccountFilterList('user_id','and') . 'GROUP BY Date ORDER BY Date';
		$this->addDataSet('parents',parent::getData($sql));

		$sql = 'SELECT COUNT(*) AS Sessions, DATE(user_activity_logs.created_at) AS Date FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->period.'\')) AND "type" = \'Kid\'' . $this->getAccountFilterList('user_id','and') . 'GROUP BY Date ORDER BY Date';
		$this->addDataSet('kids',parent::getData($sql));
	}
}
