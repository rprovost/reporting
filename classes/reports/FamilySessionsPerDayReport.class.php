<?php
include_once 'classes/Report.class.php';

class FamilySessionsPerDayReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME		= 'Families: Sessions Per Day';
	public $REPORT_DESC		= 'Number of family sessions each day (last 2-weeks)';
	public $REPORT_CLASS	= __CLASS__;

	public $REPORT_CHART_TYPE	= 'area';
	public $REPORT_CHART_LABEL	= 'date';
	public $REPORT_CHART_DATA	= 'sessions';

	protected $period	= '13 days';

	public function execute() {
		$sql = '
			SELECT
				COUNT(DISTINCT family_id) AS Sessions,
				DATE(user_activity_logs.created_at) AS Date
			FROM "user_activity_logs"

			JOIN "users" ON "users"."id" = "user_activity_logs"."user_id"
			
			WHERE
				"user_activity_logs"."action" = 1 AND
				("user_activity_logs"."created_at" > (NOW() - interval \''.$this->period.'\'))
				' . $this->getAccountFilterList('user_id','and') . '

			GROUP BY Date ORDER BY Date';
		$this->addDataSet('logged in',parent::getData($sql));

		$sql = '
			SELECT
				COUNT(DISTINCT family_id) AS Sessions,
				DATE(user_activity_logs.created_at) AS Date
			FROM "user_activity_logs"

			JOIN "users" ON "users"."id" = "user_activity_logs"."user_id"
			
			WHERE
				"user_activity_logs"."action" = 1 AND
				"type" = \'Adult\' AND
				("user_activity_logs"."created_at" > (NOW() - interval \''.$this->period.'\'))
				' . $this->getAccountFilterList('user_id','and') . '

			GROUP BY Date ORDER BY Date';
		$this->addDataSet('adults',parent::getData($sql));


		$sql = '
			SELECT
				COUNT(DISTINCT family_id) AS Sessions,
				DATE(user_activity_logs.created_at) AS Date
			FROM "user_activity_logs"

			JOIN "users" ON "users"."id" = "user_activity_logs"."user_id"
			
			WHERE
				"user_activity_logs"."action" = 1 AND
				"type" = \'Kid\' AND
				("user_activity_logs"."created_at" > (NOW() - interval \''.$this->period.'\'))
				' . $this->getAccountFilterList('user_id','and') . '

			GROUP BY Date ORDER BY Date';
		$this->addDataSet('kids',parent::getData($sql));

	}
}
