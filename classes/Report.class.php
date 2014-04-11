<?
require_once 'classes/DatabaseConnection.class.php';
require_once 'classes/HighChart.class.php';

abstract class Report {

	protected $db;
	protected $data;

	protected $hcDisplayLegend = false;

	function __construct() {
	}

	// Get a list of defined 
	protected function getActiveUsers($period = '13 days', $string = false){
		$sql = 'SELECT "users"."id" FROM "users" JOIN "user_activity_logs" ON "user_activity_logs"."user_id" = "users"."id" WHERE ("user_activity_logs"."created_at" > (NOW() - interval \''.$period.'\')) ' . $this->getAccountFilterList('users.id','AND');
		$results = self::getData($sql);
		$activeUsers = array_map(function($item) {return $item['id'];}, $results);

		if ($string) {
			return implode(',',$activeUsers);
		} else {
			return $activeUsers;
		}
	}

	/***
	 *	Run a query against the database
	 **/
	protected function getData($sql) {
		$this->db = new DatabaseConnection();
		$db = $this->db->connect();
		if(!$results = $this->db->query($sql)){
			echo '<pre>'.mysql_error($db).'</pre>';
			echo '<pre>'.$sql.'</pre>';
			exit;
		};

		$data = array();
		while ($result = pg_fetch_assoc($results)) {
			$data[] = $result;
		}
		return $data;
	}

	/***
	 *	Return the columns for the specified data-set
	 **/
	protected function getColumns($dataset) {
		foreach ($dataset[0] AS $c => $v) {
			$columns[] = $c;
		}
		return $columns;
	}

	protected function addDataSet($name,$data) {
		$this->data[$name] = $data;
	}

	/***
	 *	Filter out all internal user accounts and their family members
	 *
	 *	Note:	I don't care so much about properly breaking out functionality properly, I just need to get this
	 *			done and it's not all that unreadable (IMHO)
	 **/
	protected function getAccountFilterList($field,$concat) {
		// get family ids for all "tykoon" users so we can filter out their families as well
		$sql = 'SELECT family_id FROM users WHERE email LIKE \'%@tykoon.com\' OR login LIKE \'%@tykoon.com\'';
		$results = $this->getData($sql);

		// create family id array
		foreach ($results AS $family) {
			// there are results with no family id specified - we will add this one manually
			if (is_numeric($family['family_id'])) {
				// add a valid family id to the list
				$familyIds[] = $family['family_id'];
			}
		}

		// manually add some additional families that aren't specifically "tykoon" employees
		array_push($familyIds,'106','108','109','110','112','121','145','149','154','216','242','471','505','530','541','553','596','608','616','697','711','720','721','722','724','728','729','734','735','736','743','755','757','761','767','795','796','797','813','839','862','971','983','984','991','1133','1211','1212','1275','1458','1461','1462','1477','1486','1516','1517','1534','1535','1538','1543','1544','1623','1624','1666','1743','1916','1983','2008','2494','3116','3210','3338','3341','3383','3384','3530','3583','3634','3730','3788','3829','3850','3856','3880','3923','4884','4973','4974','5082','5085','5175','5176','5183','5241','5242','5315','5525','5534','5555','5556','5557','5558','5560','5578','5605','5640','6004','6030');

		// convert the array into a pretty string that we can use in an sql statement
		$familyIdsString = implode(',',$familyIds);

		// get user ids since family ids aren't used outside of the users table
		$sql = 'SELECT id FROM users WHERE family_id IN ('.$familyIdsString.') OR family_id IS NULL';
		$results = $this->getData($sql);

		// create family id array
		foreach ($results AS $user) {
			$users[] = $user['id'];
		}
		// convert the array into a pretty string that we can use in an sql statement
		$userString = implode(',',$users);

		// build the filter for the "where" condition
		$filter = ' ' . strtoupper($concat) . ' ' . $field . ' NOT IN ('.$userString.') ';

		// return the complete filter ("where" condition in the query)
		return $filter;
	}

	// Abstracted this
	protected function getHighChartType() {
		return $this->REPORT_CHART_TYPE;
	}

	/***
	 *	OUTPUT THE DATA RESULTS AS A CSV FILE
	 **/
	public function asCSV() {
		// define the dataset container
		$datasets = '';

		// loop through each dataset in the report
		foreach ($this->data AS $datasetname => $dataset) {

			// define the line container
			$line = '';

			foreach ($dataset AS $records) {
				$line .= '"'.implode('","',$records).'"'.chr(13);
			}

			// add dataset name to block
			$datasets .= '"'.$datasetname.'"'.chr(13);

			// add columns
			$datasets .= '"'. implode('","',$this->getColumns($dataset)) . '"' . chr(13);

			// append record to dataset
			$datasets .= $line . chr(13);
		}

		// return the final CSV
		return $datasets;
	}

	/***
	 *	OUTPUT THE DATA RESULTS AS A JSON OBJECT
	 **/
	public function asJSON() {
		// define the dataset container
		$datasets = '';

		// loop through each dataset in the report
		foreach ($this->data AS $datasetname => $dataset) {

			// define the line container
			$line	= '';

			// loop through each record in the dataset
			foreach ($dataset AS $records) {
				// open the record
				$line .= '{';

				foreach ($records AS $recordname => $record) {
					// insert each row
					$line .= '"'.$recordname.'":"'.$record.'",';
				}

				// remove the trailing comma from all records
				$line = substr($line,0,-1);

				// close the record
				$line .= '},';
			}

			// strip trailing comma from record
			$line = substr($line,0,-1);

			// append record to dataset
			$datasets .= '{';
			$datasets .= '"name":"'.$datasetname.'",';
			$datasets .= '"data":[' . $line . ']';
			$datasets .= '},';
		}

		// strip the trailing comma from the dataset list
		$datasets = substr($datasets,0,-1);

		// return the final JSON
		return '{"'.$this->REPORT_CLASS.'":[' . $datasets . ']}';
	}

	/***
	 *	OUTPUT THE DATA RESULTS AS XML
	 **/
	public function asXML() {
		$xml = '<'.$this->REPORT_CLASS.'>';

		// loop through each dataset in the report
		foreach ($this->data AS $datasetname => $dataset) {
			$xml .= '<dataset name="'.$datasetname.'">';
			foreach ($dataset AS $records) {
				$xml .= '<data>';
				foreach ($records AS $key => $record) {
					$xml .= '<'.$key.'>'.$record.'</'.$key.'>';
				}
				$xml .= '</data>';
			}
			$xml .= '</dataset>';
		}

		$xml .= '</'.$this->REPORT_CLASS.'>';

		return $xml;
	}

	/***
	 *	OUTPUT THE DATA RESULTS AS A HIGHCHART CONFIG DOCUMENT
	 **/
	public function asHighChart() {
		// instantiate the highchart object
		$hc = new HighChart();

		// set the chart type
		$hc->setChartType($this->getHighChartType());

		// toggle display of chart legend
		$hc->displayLegend($this->hcDisplayLegend);

		// set the x and y axis data
		$hc->setSeries($this->getHighChartSeries($this->REPORT_CHART_DATA,$this->REPORT_CHART_LABEL));
		$hc->setXAxis($this->getHighChartXAxis($this->REPORT_CHART_LABEL));

		// send the data back as a json object
		return $hc->toJSON();
	}

	// HighCharts - Determine the values for the X-Axis
	protected function getHighChartXAxis($label){
		foreach ($this->data AS $datasetname => $dataset) {
			foreach ($dataset AS $records) {
				$labels[] = $records[$label];
			}
		}
		return $labels;
	}

	// HighCharts - Determine the data series that needs to be displayed
	protected function getHighChartSeries($data,$label){
		foreach ($this->data AS $datasetname => $dataset) {
			$tmp = array();
			$tmp['name'] = $datasetname;
			$tmp['data'] = array();

			foreach ($dataset AS $records) {
				$tmp['data'][] = array($records[$label],(float) $records[$data]);
				//$tmp['data'][] = (float) $records[$data];
			}
			$series[] = $tmp;
		}
		return $series;
	}

}
