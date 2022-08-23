<?php

// export taxa for ColDP

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

$headings = array('ID', 'parentID', 'nameID');

$keys = array('TAXON_GUID', 'PARENT_TAXON_GUID', 'NAME_GUID');


echo join("\t", $headings) . "\n";

while (!$done)
{
	$sql = 'SELECT * FROM taxa WHERE rowid IN (
  SELECT rowid FROM taxa WHERE PARENT_TAXON_GUID IS NOT NULL LIMIT ' . $page . ' OFFSET ' . $offset . ');';
  
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
					case 'TAXON_GUID':
						$output->ID = $obj->$k;
						break;
						
					case 'PARENT_TAXON_GUID':
						$output->parentID = $obj->$k;
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
