<?php
include_once 'classes/Report.class.php';

class ActiveUsersReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME			= 'Active Users';
	public $REPORT_DESC			= 'The number of active users for the period whos account was created prior to the period.';
	public $REPORT_CLASS		= __CLASS__;
	public $REPORT_CHART_TYPE	= 'bar';

	public $REPORT_CHART_LABEL	= 'usertype';
	public $REPORT_CHART_DATA	= 'users';

	protected $weekly = '7 days';
	protected $monthly = '1 month';

	public function execute() {
		// WEEKLY
		$sql = 'SELECT COUNT(DISTINCT user_activity_logs.user_id) AS Sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->weekly.'\')) AND ("users"."created_at" < (NOW() - interval \''.$this->weekly.'\'))' . $this->getAccountFilterList('user_id','and');
		$allusers = parent::getData($sql);
		$allusers = $allusers[0]['sessions'];

		$sql = 'SELECT COUNT(DISTINCT user_activity_logs.user_id) AS Sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->weekly.'\')) AND ("users"."created_at" < (NOW() - interval \''.$this->weekly.'\')) AND "type" = \'Adult\'' . $this->getAccountFilterList('user_id','and');
		$adults = parent::getData($sql);
		$adults = $adults[0]['sessions'];

		$sql = 'SELECT COUNT(DISTINCT user_activity_logs.user_id) AS Sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->weekly.'\')) AND ("users"."created_at" < (NOW() - interval \''.$this->weekly.'\')) AND "type" = \'Kid\'' . $this->getAccountFilterList('user_id','and');
		$kids = parent::getData($sql);
		$kids = $kids[0]['sessions'];

		$this->addDataSet('Weekly',array(
				array(
					'usertype'	=> 'All Users',
					'users'		=> $allusers
				),
				array(
					'usertype'	=> 'Adults',
					'users'		=> $adults
				),
				array(
					'usertype'	=> 'Kids',
					'users'		=> $kids
				)
			)
		);

		// MONTHLY
		$sql = 'SELECT COUNT(DISTINCT user_activity_logs.user_id) AS Sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->monthly.'\')) AND ("users"."created_at" < (NOW() - interval \''.$this->monthly.'\'))' . $this->getAccountFilterList('user_id','and');
		$allusers = parent::getData($sql);
		$allusers = $allusers[0]['sessions'];

		$sql = 'SELECT COUNT(DISTINCT user_activity_logs.user_id) AS Sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->monthly.'\')) AND ("users"."created_at" < (NOW() - interval \''.$this->monthly.'\')) AND "type" = \'Adult\'' . $this->getAccountFilterList('user_id','and');
		$adults = parent::getData($sql);
		$adults = $adults[0]['sessions'];

		$sql = 'SELECT COUNT(DISTINCT user_activity_logs.user_id) AS Sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \''.$this->monthly.'\')) AND ("users"."created_at" < (NOW() - interval \''.$this->monthly.'\')) AND "type" = \'Kid\'' . $this->getAccountFilterList('user_id','and');
		$kids = parent::getData($sql);
		$kids = $kids[0]['sessions'];

		$this->addDataSet('Monthly',array(
				array(
					'usertype'	=> 'All Users',
					'users'		=> $allusers
				),
				array(
					'usertype'	=> 'Adults',
					'users'		=> $adults
				),
				array(
					'usertype'	=> 'Kids',
					'users'		=> $kids
				)
			)
		);
	}
}
