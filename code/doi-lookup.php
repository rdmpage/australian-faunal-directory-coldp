<?php

// Look for DOI using CrossRef

error_reporting(E_ALL);

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
function find_doi($string)
{
	$doi = '';
	
	$url = 'https://mesquite-tongue.glitch.me/search?q=' . urlencode($string);
	
	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	if ($data != '')
	{
		$obj = json_decode($data);
		
		//print_r($obj);
		
		if (count($obj) == 1)
		{
			if ($obj[0]->match)
			{
				$doi = $obj[0]->id;
			}
		}
		
	}
	
	return $doi;
			
}	

//----------------------------------------------------------------------------------------
function find_doi_openurl($openurl)
{
	$doi = '';
	
	$openurl = str_replace('&amp;', '&', $openurl);
	
	$url = 'http://www.crossref.org/openurl?pid=r.page@bio.gla.ac.uk' . '&' . $openurl .  '&noredirect=true&format=unixref';
		
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
	
	return $doi;
			
}	

//----------------------------------------------------------------------------------------

$sql = 'SELECT * FROM bibliography WHERE issn="1175-5326" AND doi IS NULL'; // Zootaxa

// Records of the Australian Museum
$sql = 'SELECT * FROM bibliography WHERE issn="0067-1975" AND doi IS NULL';

// Australian Journal of Zoology
$sql = 'SELECT * FROM bibliography WHERE issn="0004-959X" AND doi IS NULL';

$sql = 'SELECT * FROM bibliography WHERE issn IN ("2118-9773", "1070-9428", "1320-6133") AND doi IS NULL';
$sql = 'SELECT * FROM bibliography WHERE issn IN ("1230-2821","1217-8837","0079-8835","1863-7221","2535-0730","0161-8202","0022-149X","0260-1230","0947-5745","0307-6970","1477-2000") AND doi IS NULL';

//$sql = 'SELECT * FROM bibliography WHERE issn IN ("0307-6970") AND doi IS NULL';
//$sql = 'SELECT * FROM bibliography WHERE PUBLICATION_GUID="72f69096-8333-40c2-b6e6-b919fbf7b2e3"';


$data = do_query($sql);

foreach ($data as $obj)
{
	// print_r($obj);
	
	$doi = '';
	
	
	$terms = array();
	
	if (0)
	{
	
		$keys = array('PUB_AUTHOR', 'PUB_YEAR', 'PUB_TITLE', 'PUB_PARENT_JOURNAL_TITLE', 'volume', 'PUB_PAGES');
	
	
		foreach ($keys as $k)
		{
			if (isset($obj->{$k}))
			{
				$terms[] = $obj->{$k};
			}
		}
	
		$q = join(" ", $terms);
	
		echo "-- " . $q . "\n";
	
		// echo "-- " . strip_tags($obj->PUB_FORMATTED) . "\n";
	
		$doi = find_doi($q);
	}
	else
	{
		$keys = array('PUB_AUTHOR', 'PUB_YEAR', 'PUB_TITLE', 'PUB_PARENT_JOURNAL_TITLE', 'issn', 'volume', 'PUB_PAGES');


		$parameters = array();
		foreach ($keys as $k)
		{
			if (isset($obj->{$k}))
			{
				switch ($k)
				{
					case 'PUB_PARENT_JOURNAL_TITLE':
						$parameters['title'] = $obj->{$k};
						break;

					case 'issn':
						$parameters['issn'] = $obj->{$k};
						break;
						
					case 'volume':
						$parameters['volume'] = $obj->{$k};
						break;

					case 'PUB_YEAR':
						$parameters['date'] = $obj->{$k};
						break;

					case 'PUB_PAGES':
						$pages = explode("-", $obj->{$k});
						$parameters['spage'] = $pages[0];
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
		
	
	}
	if ($doi != '')
	{
		echo "-- $doi\n";
		
		$go = true;
		
		// sanity check for zootaxa
		if (preg_match('/zootaxa/', $doi))
		{
			if (!preg_match('/^Zootaxa$/', $obj->PUB_PARENT_JOURNAL_TITLE))
			{
				$go = false;
			}
			
			if (!$go)
			{
				echo "-- *** spurious match ***\n";
			}
		}
		
		if ($go)
		{
			echo 'UPDATE bibliography SET doi="' . $doi . '" WHERE PUBLICATION_GUID ="' . $obj->PUBLICATION_GUID . '";' . "\n";
		}
	}
	
	echo "\n";	
}


?>
