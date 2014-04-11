<?php
include_once 'classes/Report.class.php';

class UsersRegistrationMethodReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME				= 'Users: Registration Method';
	public $REPORT_DESC				= 'Breakdown of users by traditional or Facebook registration.';
	public $REPORT_CLASS			= __CLASS__;

	public $REPORT_CHART_TYPE		= 'bar';
	public $REPORT_CHART_LABEL		= 'service';
	public $REPORT_CHART_DATA		= 'users';

	protected $totalUsers;
	protected $facebookUsers;
	protected $traditionalUsers;

	public function execute() {
		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS Users FROM "users" WHERE "deleted_at" IS NULL' . $this->getAccountFilterList('users.id','and');
		$this->totalUsers = parent::getData($sql);
		$this->totalUsers = (float) $this->totalUsers[0]['users'];

		$sql = 'SELECT COUNT(DISTINCT concat(email,login)) AS Users FROM "users" LEFT JOIN "services" ON "services"."user_id" = "users"."id" WHERE "users"."deleted_at" IS NULL AND "services"."provider" LIKE \'facebook\''.$this->getAccountFilterList('users.id','and');
		$this->facebookUsers = parent::getData($sql);
		$this->facebookUsers = (float) $this->facebookUsers[0]['users'];

		$this->traditionalUsers = (float) $this->totalUsers - $this->facebookUsers;

		$this->addDataSet('registration types',array(
				array(
					'service'	=> 'Total Users',
					'users'		=> $this->totalUsers
				),
				array(
					'service'	=> 'Tykoon',
					'users'		=> $this->traditionalUsers
				),
				array(
					'service'	=> 'Facebook',
					'users'		=> $this->facebookUsers
				)
			)
		);
	}
}
