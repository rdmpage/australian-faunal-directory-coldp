<?php

// look for DOI using local database

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/author-parsing.php');

$pdo = new PDO('sqlite:../afd.db');


//----------------------------------------------------------------------------------------
function do_query($sql)
{
	global $pdo;
	
	$stmt = $pdo->query($sql);

	$data = array();

	while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

		$item = new stdclass;
		
		$keys = array_keys($row);
	
		foreach ($keys as $k)
		{
			if ($row[$k] != '')
			{
				$item->{$k} = $row[$k];
			}
		}
	
		$data[] = $item;
	
	
	}
	
	return $data;	
}

//----------------------------------------------------------------------------------------
function find_doi_openurl($openurl)
{
	$doi = '';
	
	$openurl = str_replace('&amp;', '&', $openurl);
	
	$url = 'http://localhost/old/microcitation/www/index.php?' . $openurl;
		
	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);
	
	//echo $url . "\n";
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	//echo $data;
	
	$obj = json_decode($data);
	
	if ($obj)
	{
	
		if (count($obj->results) == 1)
		{
			$doi = $obj->results[0]->doi;
		}
	}
	
	/*
	if ($data != '')
	{
		$dom= new DOMDocument;
		$dom->loadXML($data);
		$xpath = new DOMXPath($dom);
		
		$xpath_query = '//journal_article[@publication_type="full_text"]/doi_data/doi';
		$xpath_query = '//journal_article/doi_data/doi';
		$nodeCollection = $xpath->query ($xpath_query);
		
		foreach($nodeCollection as $node)
		{
			$doi = $node->firstChild->nodeValue;
		}
		
		
	}
	*/
	
	return $doi;
			
}	

//----------------------------------------------------------------------------------------

$sql = 'SELECT * FROM bibliography WHERE issn="0374-5481" AND series IS NOT NULL AND doi IS NULL'; 
$sql = 'SELECT * FROM bibliography WHERE issn="0374-5481" AND PUB_YEAR LIKE "18%" AND volume IS NOT NULL AND doi IS NULL'; 




$data = do_query($sql);

foreach ($data as $obj)
{
	// print_r($obj);
	
	$doi = '';
	
	
	$terms = array();
		$keys = array('PUB_AUTHOR', 'PUB_YEAR', 'PUB_TITLE', 'PUB_PARENT_JOURNAL_TITLE', 'issn', 'series', 'volume', 'spage');


	$parameters = array();
	foreach ($keys as $k)
	{
		if (isset($obj->{$k}))
		{
			switch ($k)
			{

				case 'issn':
					$parameters['issn'] = $obj->{$k};
					break;
					
					/*
				case 'series':
					$parameters['series'] = $obj->{$k};
					break;
					*/

				case 'volume':
					$parameters['volume'] = $obj->{$k};
					break;
					
				case 'spage':
					$parameters['page'] = $obj->{$k};
					break;						

				case 'PUB_YEAR':
					$parameters['year'] = $obj->{$k};
					break;
					
				default:
					break;
			
			}
			
		}
	}
	
	echo "-- " . $obj->PUB_TITLE . "\n";
	
	// print_r($parameters);
	
	$doi = '';
	
	if (count($parameters) >= 4)
	{
		$openurl = http_build_query($parameters);
			
		$doi = find_doi_openurl($openurl);
	}
		
	if ($doi != '')
	{
		echo "-- $doi\n";
		echo 'UPDATE bibliography SET doi="' . $doi . '" WHERE PUBLICATION_GUID ="' . $obj->PUBLICATION_GUID . '";' . "\n";
	}
	
	echo "\n";	
}


?>
