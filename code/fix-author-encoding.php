<?php

// Fix author name encoding issues


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

$filename = dirname(__FILE__) . '/authorfixes.csv';
$file_handle = fopen($filename, "r");

$count = 0;

while (!feof($file_handle)) 
{
	$line = trim(fgets($file_handle));
	
	if ($line != '')
	{	
		$parts = explode(",", $line);
		
		if ($count > 0)
		{
			//print_r($parts);
			
			$sql = 'SELECT * FROM bibliography WHERE PUB_AUTHOR LIKE("%' . $parts[0] . '%")';
			
			$data = do_query($sql);

			foreach ($data as $obj)
			{
				$pub_author = $obj->{'PUB_AUTHOR'};
				$pub_guid = $obj->{'PUBLICATION_GUID'};
				
				$old_author = $parts[0];
				$old_author = str_replace('?', '\?', $old_author);
				
				$pattern = '/' . $old_author . '/u';
				
				$pub_author = preg_replace($pattern, $parts[1], $pub_author);
				
				//echo $parts[0] . ' ' . $parts[1] . ' ' . $pub_author . "\n";
				
				$sql = 'UPDATE bibliography SET PUB_AUTHOR="' . $pub_author . '" WHERE PUBLICATION_GUID="' . $pub_guid . '";' ;
				
				echo $sql . "\n";
			}
		}
		$count++;
	}
}		

?>
