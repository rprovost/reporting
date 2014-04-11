<?php
include_once 'classes/Report.class.php';

class WorthActiveAverageReport extends Report implements Reportable, HighChartable {
	public $REPORT_NAME			= 'Worth: Active Families Average Balances';
	public $REPORT_DESC			= 'Average balances for all active families (past month).';
	public $REPORT_CLASS		= __CLASS__;

	public $REPORT_CHART_TYPE	= 'bar';
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
		$sql = 'SELECT AVG(save_balance) AS SaveBalance, AVG(give_balance) AS GiveBalance, AVG(store_balance) AS SpendBalance FROM "users" WHERE "id" IN ('.$activeUsersString.') AND "type" = \'Kid\' AND (save_balance > 0 OR give_balance > 0 OR store_balance > 0)';

		// execute our query
		$balances = parent::getData($sql);

		// create some local variables for each necessary balance we need to output
		$this->saveBalance	= number_format(round($balances[0]['savebalance']/100,2),2);
		$this->giveBalance	= number_format(round($balances[0]['givebalance']/100,2),2);
		$this->spendBalance	= number_format(round($balances[0]['spendbalance']/100,2),2);

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
