<?php

require_once('UKM/Autoloader.php');

use UKMNorge\Database\SQL\Query;
use UKMNorge\Innslag\Innslag;
use UKMNorge\Innslag\Personer\Person;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$search = new stdClass();
	$search->type = $_POST['type'];
	$search->search = mb_strtolower($_POST['search']);

	if (strpos($_POST['search'], '*') !== false) {
		$operand = 'LIKE';
		$_POST['search'] = str_replace('*', '%', $_POST['search']);
	} else {
		$operand = '=';
	}

	if ($_POST['type'] == 'innslag') {
		$key = 'innslag';
		$query = Innslag::getLoadQuery();

		$query .= " WHERE (`smartukm_band`.`b_name`) $operand '#search'";
	} else {
		$key = 'personer';
		$query = Person::getLoadQuery(); // SELECT * FROM `smartukm_participant`
		switch ($_POST['type']) {
			default:
				$query .= "
				WHERE LOWER(`p_firstname`) $operand '#search'
				   OR LOWER(`p_lastname`) $operand '#search'
				   OR LOWER(CONCAT(`p_firstname`,' ',`p_lastname`)) $operand '#search'";
				if (is_numeric($_POST['search'])) {
					$query .= "
						OR `p_phone` $operand '#search'";
				}
		}
	}
	$sql = new Query($query, ['search' => $_POST['search']]);
	$res = $sql->run();

	#echo $sql->debug();
	$results = [];
	while ($row = Query::fetch($res)) {
		try {
			switch ($key) {
				case 'personer':
					$objekt = new Person($row);
					break;
				case 'innslag':
					$objekt = Innslag::getById($row['b_id']);
					break;
				default:
					throw new Exception('Kan ikke søke etter innslag av type ' . $key);
			}

			$results[$key][] = $objekt;
		} catch (Exception $e) {
			//ignore
		}
	}
	UKMdeltakere::addViewData('searchtable', $key);
}
UKMdeltakere::addViewData('results', $results);
UKMdeltakere::addViewData('search', $search);