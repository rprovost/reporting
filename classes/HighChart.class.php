<?php

class HighChart {

	protected $chartType;
	protected $series;
	protected $xAxis;
	protected $yAxis;
	protected $legend = false;

	// instantiate and allow for the setting of the chart type
	public function __construct($type='bar'){
		$this->chartType = $type;
	}

	// allow this to be set outside of the contructor
	public function setChartType($type) {
		$this->chartType = $type;
	}

	public function setSeries($series) {
		$this->series = $series;
	}

	public function setXAxis($series) {
		$this->xAxis = $series;
	}

	public function setYAxis($series) {
		$this->yAxis = $series;
	}

	public function displayLegend($state = false) {
		$this->legend = $state;
	}

	public function toJSON(){
		return json_encode($this->getConfig());
	}

	// returns the JSON object that is used to render a charts using HighChart
	public function getConfig() {
		$HighChartConfig = array(
				'chart' => array(
						'type'			=> $this->chartType,
						'marginTop'		=> 25,
						'marginRight'	=> 50,
						'marginBottom'	=> 140,
//						'height'		=> 290,
						'reflow'		=> false
					),
				'credits' => array(
						'enabled' => false
					),
				'legend' => array(
						'enabled' => $this->legend
					),
				'title' => array(
						'text' => ''
					),
				'plotOptions' => array(
						'area' => array(
								'stacking' => false,
								'marker' => array(
										'enabled' => false
									)
							),
					),
				'xAxis' => array(
						'title' => array(
								'text' => ''
							),
						'categories' => $this->xAxis,
						'labels' => array(
								'rotation' => ($this->chartType == 'bar') ? 0 : 90,
								'align' => ($this->chartType == 'bar') ? 'right' : 'left',
							)
					),
				'yAxis' => array(
						'title' => array(
								'text' => ''
							),
						'plotLines' => array(
								array(
									'value'	=> 0,
									'width'	=> 1,
									'color'	=> '#808080'
								)
							)
					),
				'series' => $this->series
			);
		return $HighChartConfig;
	}
}