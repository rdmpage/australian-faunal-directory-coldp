<?php

// export names for ColDP

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


$page 	= 100;
$offset = 0;
$done 	= false;

$headings = array('ID', 'scientificName', 'authorship', 'rank', 'uninomial', 'genus', 
	'infragenericEpithet', 'specificEpithet', 'infraspecificEpithet', 'code',
	'referenceID', 'publishedInYear', 'remarks');

$keys = array('NAME_GUID', 'SCIENTIFIC_NAME', 'AUTHOR', 'RANK', 'FAMILY', 'GENUS', 'SUBGENUS',
	'SPECIES', 'SUBSPECIES', 
	'NAME_TYPE', 'NAME_SUBTYPE',
	'PUBLICATION_GUID', 'YEAR', 'QUALIFICATION');


echo join("\t", $headings) . "\n";

while (!$done)
{
	$sql = 'SELECT * FROM taxa WHERE rowid IN (
  SELECT rowid FROM taxa WHERE SCIENTIFIC_NAME IS NOT NULL LIMIT ' . $page . ' OFFSET ' . $offset . ');';
  
  //$sql = 'SELECT * FROM taxa WHERE TAXON_GUID="753809e2-4e92-4881-a4bd-cd5d3a7e6744"';
  
  	$data = do_query($sql);

	foreach ($data as $obj)
	{
		//print_r($obj);
				
		$output = new stdclass;
		
		$output->code = 'ICZN';
		
		foreach ($keys as $k)
		{
			if (isset($obj->{$k}))
			{
				switch ($k)
				{
					case 'NAME_GUID':
						$output->ID = $obj->{$k};
						break;
						
					case 'SCIENTIFIC_NAME':
						$output->scientificName = $obj->{$k};
						break;
						
					case 'AUTHOR':
						$output->authorship = $obj->{$k};
						break;
						
					case 'RANK':
						$output->rank = strtolower($obj->{$k});
						break;	
						
					case 'FAMILY':
						if (isset($obj->RANK) && $obj->RANK == 'Family')
						{
							$output->uninomial = $obj->{$k};
						}					
						break;	
						
					case 'GENUS':
						if (isset($obj->RANK) && ($obj->RANK == 'Genus'))
						{
							$output->uninomial = $obj->{$k};
						}	
						else
						{
							$output->genus = $obj->{$k};
						}		
						break;			

					case 'SUBGENUS':
						$output->infragenericEpithet = $obj->{$k};
						break;					
															
					case 'SPECIES':
						$output->specificEpithet = $obj->{$k};
						break;

					case 'SUBSPECIES':
						$output->infraspecificEpithet = $obj->{$k};
						break;
						
					case 'PUBLICATION_GUID':
						$output->referenceID = $obj->{$k};
						break;

					case 'YEAR':
						$output->publishedInYear = $obj->{$k};
						break;

					case 'QUALIFICATION':
						$output->remarks = $obj->{$k};
						break;
						
					// sattus of names
					case 'NAME_TYPE':
						break;
						
					case 'NAME_SUBTYPE':
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
		//if ($offset > 5) { $done = true; }
	}
	

}

?>
