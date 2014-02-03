<?php

interface IDataImporter {

	public function __construct($pathToFile);

	public function openFile();
	public function parseFile();
	public function getContent();
	public function getKeys();
}

?>