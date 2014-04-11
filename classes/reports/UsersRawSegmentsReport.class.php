<?php
include_once 'classes/Report.class.php';

class UsersRawSegmentsReport extends Report implements Reportable {
	public $REPORT_NAME		= 'Users: Segments';
	public $REPORT_DESC		= 'Segmented user data';
	public $REPORT_CLASS	= __CLASS__;

	public function execute(){
		// New Users: Non-churned, active
		$sql = 'SELECT "users"."login","users"."email","users"."first_name","users"."last_name","users"."type","users"."gender","users"."family_id",MAX(DATE("users"."created_at")) AS account_created_on,MAX(DATE("user_activity_logs"."created_at")) AS last_session,COUNT(*) AS total_sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("users"."created_at" > (NOW() - interval \'1 month\')) AND "user_activity_logs"."created_at" > (NOW() - interval \'2 weeks\') AND ' . $this->getAccountFilterList('users.id','') . ' GROUP BY "users"."login","users"."last_name","users"."first_name","users"."email","users"."type","users"."gender","users"."family_id" HAVING COUNT(*) BETWEEN 7 AND 8';
		$this->addDataSet('non-churned, active users',parent::getData($sql));

		// New Users: Churned, active
		$sql = 'SELECT "users"."login","users"."email","users"."first_name","users"."last_name","users"."type","users"."gender","users"."family_id",MAX(DATE("users"."created_at")) AS account_created_on,MAX(DATE("user_activity_logs"."created_at")) AS last_session,COUNT(*) AS total_sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("users"."created_at" > (NOW() - interval \'1 month\')) AND "user_activity_logs"."created_at" < (NOW() - interval \'2 weeks\') AND ' . $this->getAccountFilterList('users.id','') . ' GROUP BY "users"."login","users"."last_name","users"."first_name","users"."email","users"."type","users"."gender","users"."family_id" HAVING COUNT(*) BETWEEN 7 AND 8';
		$this->addDataSet('churned, active users',parent::getData($sql));

		// Core Users
		$sql = 'SELECT "users"."login","users"."email","users"."first_name","users"."last_name","users"."type","users"."gender","users"."family_id",MAX(DATE("users"."created_at")) AS account_created_on,MAX(DATE("user_activity_logs"."created_at")) AS last_session,COUNT(*) AS total_sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("users"."created_at" > (NOW() - interval \'1 month\')) AND "user_activity_logs"."created_at" > (NOW() - interval \'1 weeks\') AND ' . $this->getAccountFilterList('users.id','') . ' GROUP BY "users"."login","users"."last_name","users"."first_name","users"."email","users"."type","users"."gender","users"."family_id" HAVING COUNT(*) BETWEEN 10 AND 25';
		$this->addDataSet('core users',parent::getData($sql));

		// Über Users
		$sql = 'SELECT "users"."login","users"."email","users"."first_name","users"."last_name","users"."type","users"."gender","users"."family_id",MAX(DATE("users"."created_at")) AS account_created_on,MAX(DATE("user_activity_logs"."created_at")) AS last_session,COUNT(*) AS total_sessions FROM "user_activity_logs" JOIN "users" ON "users"."id" = "user_activity_logs"."user_id" WHERE "user_activity_logs"."action" = 1 AND ("users"."created_at" > (NOW() - interval \'1 months\')) AND "user_activity_logs"."created_at" > (NOW() - interval \'1 weeks\') AND ' . $this->getAccountFilterList('users.id','') . ' GROUP BY "users"."login","users"."last_name","users"."first_name","users"."email","users"."type","users"."gender","users"."family_id" HAVING COUNT(*) > 25';
		$this->addDataSet('uber users',parent::getData($sql));
	}

}