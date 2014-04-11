<?php
include_once 'classes/Report.class.php';

class WorthActiveBreakdownReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME			= 'Worth: Active Families';
	public $REPORT_DESC			= 'Balances for all active families (past month).';
	public $REPORT_CLASS		= __CLASS__;

	public $REPORT_CHART_TYPE	= 'pie';
	public $REPORT_CHART_LABEL	= 'category';
	public $REPORT_CHART_DATA	= 'balance';

	protected $saveBalance;
	protected $giveBalance;
	protected $spendBalance;

	protected $period = '1 month';

	public function execute() {
		// get active users list
		$activeUsersString = $this->getActiveUsers($this->period,true);

		// get the balances for all of our "active" families
		$sql = 'SELECT SUM(save_balance) AS SaveBalance, SUM(give_balance) AS GiveBalance, SUM(store_balance) AS SpendBalance FROM "users" WHERE "id" IN ('.$activeUsersString.')';

		// execute our query
		$balances = parent::getData($sql);

		// create some local variables for each necessary balance we need to output
		$this->saveBalance	= round($balances[0]['savebalance']/100,2);
		$this->giveBalance	= round($balances[0]['givebalance']/100,2);
		$this->spendBalance	= round($balances[0]['spendbalance']/100,2);

		// create the report dataset
		$this->addDataSet('GDP',array(
				array(
					'category'	=> 'Save',
					'balance'	=> $this->saveBalance
				),
				array(
					'category'	=> 'Give',
					'balance'	=> $this->giveBalance
				),
				array(
					'category'	=> 'Spend',
					'balance'	=> $this->spendBalance
				)
			)
		);
	}
}
