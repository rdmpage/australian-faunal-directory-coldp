<?php

// Convert to SQL

date_default_timezone_set('UTC');

//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

$basedir = dirname(dirname(__FILE__)) . '/taxa';

$files = scandir($basedir);

// debugging
//$files=array('Aboetheta.csv');

foreach ($files as $filename)
{
	if (preg_match('/\.csv$/', $filename))
	{	
		$filename = $basedir . '/' . $filename;
		
		$ok = true;
		
		// is it HTML?
		$fp = fopen($filename, 'r');
		$data = fread($fp, 100);
		fclose($fp);
		
		if (preg_match('/<\!doctype html>/', $data))
		{
			$ok = false;
		}

		if ($ok)	
		{
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
									case 'NAME_LAST_UPDATE':
									case 'TAXON_LAST_UPDATE':
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
					
						echo 'INSERT OR IGNORE INTO taxa(' . join(',', $keys) . ') VALUES (' .  join(',', $values) . ');' . "\n";
					
					
					}
				}
				$row_count++;
			}
		}
	}
}
		
?>
