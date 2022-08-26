<?php

error_reporting(E_ALL);


//----------------------------------------------------------------------------------------

$filename = 'formatted.csv';
//$filename = 'test.csv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		"\t" 
		);
		
	$go = is_array($row);
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$obj = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
				}
			}
		
			// print_r($obj);	
			
			$terms = array();
			
			if (isset($obj->PUB_PARENT_JOURNAL_TITLE))
			{
				$terms['journal'] = $obj->PUB_PARENT_JOURNAL_TITLE;
				$terms['journal'] = str_replace('(', '\(', $terms['journal']);
				$terms['journal'] = str_replace(')', '\)', $terms['journal']);
				$terms['journal'] = str_replace('[', '\[', $terms['journal']);
				$terms['journal'] = str_replace(']', '\]', $terms['journal']);
				$terms['journal'] = str_replace('/', '\/', $terms['journal']);
			}
			
			if (isset($terms['journal']))
			{
				//fwrite(STDERR, "$journal\n");
			
				$matched = false;				
				if (!$matched)
				{
					if (preg_match('/<em>' . $terms['journal'] . '<\/em><\/a> <strong>(No. )?(?<volume>\d+(\.\d)?)<\/strong>(\((?<issue>[^\)]+)\))?:/', $obj->PUB_FORMATTED, $m))
					{
						$terms['volume'] = $m['volume'];
			
						if (isset($m['issue']))
						{
							$terms['issue'] = $m['issue'];
						}
						$matched = true;
					}
				}
			
				// series info
				// </em></a> 13 <strong>9</strong>:
				// </em></a> N.S. <strong>4</strong>
				if (!$matched)
				{
					if (preg_match('/<em>' . $terms['journal'] . '<\/em><\/a>\s+\(?(?<series>[\w\.]+)\)?\s+<strong>(?<volume>\d+)<\/strong>(\((?<issue>[^\)]+)\))?:/', $obj->PUB_FORMATTED, $m))
					{
						$terms['series'] = $m['series'];
						$terms['volume']= $m['volume'];
			
						if (isset($m['issue']))
						{
							$terms['issue'] = $m['issue'];
						}
						$matched = true;
					}
				}

				// </em></a> Suppl. <strong>4</strong>
				if (!$matched)
				{
					$journal = $terms['journal'];
					$journal = str_replace('(', '\(', $journal);
					$journal = str_replace(')', '\)', $journal);
			
					if (preg_match('/<em>' . $terms['journal']. '<\/em><\/a>\s+\Suppl.\s+<strong>(?<volume>\d+)<\/strong>:/', $obj->PUB_FORMATTED, $m))
					{
						$terms['volume']= $m['volume'];
						$matched = true;
					}
				}
			}
			
			
			if (isset($obj->PUB_PAGES))
			{
				$matched = false;
				
				if (!$matched)
				{
					if (preg_match('/^(pp.\s+)?(?<spage>\d+)\s*-\s*(?<epage>\d+)\.?$/', $obj->PUB_PAGES, $m))
					{
						$terms['spage'] = $m['spage'];
						$terms['epage'] = $m['epage'];
						$matched = true;
					}
		
				}
		
				// 241-251, pl. 5, figs. 1-4
				if (!$matched)
				{
					if (preg_match('/^(?<spage>\d+)-(?<epage>\d+)[,|;]?\s+/', $obj->PUB_PAGES, $m))
					{
						$terms['spage'] = $m['spage'];
						$terms['epage'] = $m['epage'];
						$matched = true;
					}
		
				}
			
				if (!$matched)
				{
					if (preg_match('/^(?<spage>\d+)$/', $obj->PUB_PAGES, $m))
					{
						$terms['spage'] = $m['spage'];
						$matched = true;
					}
		
				}

				if (!$matched)
				{
					if (preg_match('/^(?<spage>\d+),\s+/', $obj->PUB_PAGES, $m))
					{
						$terms['spage'] = $m['spage'];
						$matched = true;
					}
		
				}

				if (!$matched)
				{
					if (preg_match('/^(?<spage>\d+)\s+pp./', $obj->PUB_PAGES, $m))
					{
						$terms['spage'] = $m['spage'];
						$matched = true;
					}
		
				}

			}
			
			if (count($terms) > 1)
			{
				// print_r($terms);
				
				echo 'UPDATE bibliography SET ';
				
				$term_count = 0;
				
				foreach ($terms as $k => $v)
				{
					switch ($k)
					{
						case 'journal':
							break;
							
						default:
							if ($term_count > 0)
							{
								echo ", ";
							}
							echo $k . '="' . $v . '"';
					
							$term_count++;
							break;
							
					}
				
				}
			
			
				echo ' WHERE PUBLICATION_GUID="' . $obj->PUBLICATION_GUID . '";' . "\n";
			}
			
			
			
			
		}
	}	
	$row_count++;
}
?>

