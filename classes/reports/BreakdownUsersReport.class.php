<?php
include_once 'classes/Report.class.php';

class BreakdownUsersReport extends Report implements Reportable, Highchartable {
	public $REPORT_NAME			= 'Breakdown Report: Users';
	public $REPORT_DESC			= 'Total and Active (within last month) family breakdown';
	public $REPORT_CLASS		= __CLASS__;

	public $REPORT_CHART_TYPE	= 'bar';
	public $REPORT_CHART_LABEL	= 'category';
	public $REPORT_CHART_DATA	= 'familiyusers';

	public $hcDisplayLegend		= true;

	public function execute() {
		/*********************************/
		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS ActiveUsers FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \'1 month\')) ' . $this->getAccountFilterList('user_id','and');
		$activeusers = parent::getData($sql);
		$activeusers = $activeusers[0]['activeusers'];

		$sql = 'SELECT COUNT(DISTINCT family_id) AS Logins FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."id" WHERE "user_activity_logs"."action" = 1 AND ("user_activity_logs"."created_at" > (NOW() - interval \'1 month\')) ' . $this->getAccountFilterList('user_id','and');
		$activefamilies = parent::getData($sql);
		$activefamilies = $activefamilies[0]['logins'];

		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS ActiveParents FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."id" WHERE "user_activity_logs"."action" = 1 AND "users"."type" = \'Adult\' AND ("user_activity_logs"."created_at" > (NOW() - interval \'1 month\')) ' . $this->getAccountFilterList('user_id','and');
		$activeparents = parent::getData($sql);
		$activeparents = $activeparents[0]['activeparents'];

		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS ActiveKids FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."id" WHERE "user_activity_logs"."action" = 1 AND "users"."type" = \'Kid\' AND ("user_activity_logs"."created_at" > (NOW() - interval \'1 month\')) ' . $this->getAccountFilterList('user_id','and');
		$activekids = parent::getData($sql);
		$activekids = $activekids[0]['activekids'];


		$this->addDataSet('Active',array(
				array(
					'category'		=> 'Users',
					'familiyusers'	=> $activeusers
				),
				array(
					'category'		=> 'Families',
					'familiyusers'	=> $activefamilies
				),
				array(
					'category'		=> 'Parents',
					'familiyusers'	=> $activeparents
				),
				array(
					'category'		=> 'Kids',
					'familiyusers'	=> $activekids
				)
			)
		);

		/*********************************/
		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS Users FROM "users" WHERE "deleted_at" IS NULL' . $this->getAccountFilterList('users.id','and');
		$users = parent::getData($sql);
		$users = $users[0]['users'];

		$sql = 'SELECT COUNT(DISTINCT family_id) AS Families FROM "users" WHERE ' . $this->getAccountFilterList('id','');
		$families = parent::getData($sql);
		$families = $families[0]['families'];

		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS FamiliesParents FROM "users" WHERE "users"."type" = \'Adult\' ' . $this->getAccountFilterList('id','and');
		$parents = parent::getData($sql);
		$parents = $parents[0]['familiesparents'];

		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS FamiliesKids FROM "users" WHERE "users"."type" = \'Kid\' ' . $this->getAccountFilterList('id','and');
		$kids = parent::getData($sql);
		$kids = $kids[0]['familieskids'];

		$this->addDataSet('Total',array(
				array(
					'category'		=> 'Users',
					'familiyusers'	=> $users
				),
				array(
					'category'		=> 'Families',
					'familiyusers'	=> $families
				),
				array(
					'category'		=> 'Parents',
					'familiyusers'	=> $parents
				),
				array(
					'category'		=> 'Kids',
					'familiyusers'	=> $kids
				)
			)
		);
	}

	public function getHighChart() {
		return array(
				'type'			=> 'bar',
				'column'		=> 'category',
				'series'		=> 'familiyusers',
				'height'		=> 290,
				'margin'		=> 75,
				'stacked'		=> true,
				'displayValues'	=> false
			);
	}
}
