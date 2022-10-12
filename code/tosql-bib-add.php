<?php

// Convert to SQL but only add if not already in database
// INSERT OR IGNORE INTO

date_default_timezone_set('UTC');

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
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

$basedir = dirname(dirname(__FILE__)) . '/bibliography';

$files = scandir($basedir);

// debugging
//$files=array('ACTEONIMORPHA-bibliography.csv');
//$files=array('STRATIOMYIDAE-bibliography.csv');

foreach ($files as $filename)
{
	if (preg_match('/\.csv$/', $filename))
	{	
		$filename = $basedir . '/' . $filename;
	
		$row_count = 0;
	
		$file = @fopen($filename, "r") or die("couldn't open $filename");
			
		$file_handle = fopen($filename, "r");
		while (!feof($file_handle)) 
		{
			$row = fgetcsv(
				$file_handle, 
				0, 
				',',
				'"'
				);
				
			//print_r($row);
			
			if ($row_count == 0)
			{
				$column_keys = $row;
			}
			else
			{
				if (is_array($row))
				{
					$obj = new stdclass;
					
					$keys = array();
					$values = array();
					
					foreach ($row as $k => $v)
					{
						if ($v != '')
						{
							switch ($column_keys[$k])
							{
								case 'PUBLICATION_LAST_UPDATE':
									$v = str_replace('T', ' ', $v);
									$v = str_replace('+0000', '', $v);
									
									$v = date( 'Y-m-d H:i:s', strtotime($v));
									break;
									
								default:
									break;
							}
							
							$keys[] = $column_keys[$k];
							
							$values[] = '"' . str_replace('"', '""', $v) . '"';
						}
					}
				
					//print_r($obj);
					
					echo 'INSERT OR IGNORE INTO bibliography(' . join(',', $keys) . ') VALUES (' .  join(',', $values) . ');' . "\n";
				}
			}
			$row_count++;
		}

	}
}
		
?>
