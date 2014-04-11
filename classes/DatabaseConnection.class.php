<?
require_once '../../connection.db';

class DatabaseConnection {

	protected $db;
	protected $settings;

	function connect() {
		$connection = 'host='.DB_HOST.' port='.DB_PORT.' dbname='.DB_NAME.' user='.DB_USER.' password='.DB_PWD;
		$this->db = pg_connect($connection);
	}

	function query($sql) {
		return pg_query($this->db, $sql);
	}
}
