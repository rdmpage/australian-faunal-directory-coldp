<?php

// export synonyms for ColDP

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


$page 	= 100;
$offset = 0;
$done 	= false;

$headings = array('ID', 'taxonID', 'nameID');

$keys = array('CONCEPT_GUID', 'TAXON_GUID', 'NAME_GUID');

// need to think about taxonomic versus nomenclatural synonyms

/*
NAME_TYPE	NAME_SUBTYPE
Valid Name	
Synonym	synonym
Generic Combination	
Common Name	
Synonym	nomen nudum
Synonym	nomen dubium
Synonym	subjective synonym
Synonym	junior homonym
Synonym	replacement name
Common Name	General
Synonym	subsequent misspelling
Synonym	nomen oblitum
Synonym	emendation
Synonym	invalid name
Synonym	original spelling
Common Name	Preferred
Synonym	nomen protectum
Synonym	objective synonym
*/


echo join("\t", $headings) . "\n";

while (!$done)
{
	$sql = 'SELECT * FROM taxa WHERE rowid IN (
  SELECT rowid FROM taxa WHERE NAME_TYPE IN ("Generic Combination", "Synonym") 
  AND CONCEPT_GUID IS NOT NULL
  LIMIT ' . $page . ' OFFSET ' . $offset . ');';
  
  	$data = do_query($sql);

	foreach ($data as $obj)
	{
		// print_r($obj);
				
		$output = new stdclass;
		
		foreach ($keys as $k)
		{
			if (isset($obj->$k))
			{
				switch ($k)
				{
					case 'CONCEPT_GUID':
						$output->ID = $obj->$k;
						break;
						
					case 'TAXON_GUID ':
						$output->taxonID = $obj->$k;
						break;
						
					case 'NAME_GUID':
						$output->nameID = $obj->$k;
						break;
										
					default:
						break;
				}
			}
		}
		
		// print_r($output);
		
		// translate to ColDP
		$row = array();

		foreach ($headings as $k)
		{
			if (isset($output->{$k}))
			{
				$row[] = $output->{$k};
			}
			else
			{
				$row[] = '';
			}
		}
		
		echo join("\t", $row) . "\n";
		
	}

	if (count($data) < $page)
	{
		$done = true;
	}
	else
	{
		$offset += $page;
		if ($offset > 5) { $done = true; }
	}
	

}

?>
