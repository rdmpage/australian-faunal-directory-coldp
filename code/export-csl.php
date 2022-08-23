<?php

// export references as CSL

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
// convert database row to CSL
function data_to_csl($obj)
{
	$csl = new stdclass;
	
	foreach ($obj as $k => $v)
	{
		switch ($k)
		{
			case 'PUBLICATION_GUID':
				$csl->id = $v;
				break;
		
			case 'PUB_TYPE':
				switch ($v)
				{
					case 'Article in Journal':
						$csl->type = 'article-journal';
						break;

					case 'Book':
						$csl->type = 'book';
						break;

					case 'Chapter in Book':
						$csl->type = 'chapter';
						break;

					case 'URL':
						$csl->type = 'webpage';
						break;
						
					case 'This Work':
						$csl->type = 'dataset';
						break;

					case 'Section in an Article':
						$csl->type = 'article';
						break;

					case 'Miscellaneous':
					default:
						$csl->type = 'article';
						break;
				}
				break;
			
			case 'PUB_TITLE':
				$csl->title = strip_tags($v);
				break;
				
			case 'PUB_PAGES':
				$csl->page = $v;
				break;
				
			case 'PUB_PARENT_JOURNAL_TITLE':
				$csl->{'container-title'} = $v;
				break;
				
			case 'PUB_PUBLISHER':
				$csl->publisher = $v;
				break;		
				
			case 'PUB_AUTHOR':
				$result = parse_author_string($v);
				if (count($result->author) > 0)
				{
					$csl->author = $result->author;
				}
				break;
				
			case 'PUB_YEAR':
				if (is_numeric($v))
				{
					if (!isset($csl->issued))
					{
						$csl->issued = new stdclass;
						$csl->issued->{'date-parts'} = array();
						$csl->issued->{'date-parts'}[0] = array();						
					}
					$csl->issued->{'date-parts'}[0][] = (Integer)$v;
				}
				break;
				
				/*
				// available after processing
				
			case 'volume':
			case 'issue':
				$csl->{$k} = $v;
				break;
				
			case 'issn':
			case 'eissn':
				if (!isset($csl->ISSN))
				{
					$csl->ISSN = array();
				}
				$csl->ISSN[] = $v;
				break;
				
			case 'doi':
				$csl->DOI = $v;
				break;

			case 'url':
				$csl->URL = $v;
				break;

			case 'pdf':
				$csl->link = array();	
				$link = new stdclass;
				$link->URL = $obj->pdf;
				$link->{'content-type'} = "application/pdf";
	
				$csl->link[] = $link;					
				break;
				*/
						
			default:
				break;
		}
	}
	
	return $csl;
}

//----------------------------------------------------------------------------------------

$sql = 'SELECT * FROM bibliography WHERE PUB_AUTHOR="Otto, J.C. & Hill, D.E." LIMIT 10';

$data = do_query($sql);

foreach ($data as $obj)
{
	// print_r($obj);

	$csl = data_to_csl($obj);
	
	print_r($csl);
	
	echo json_encode($csl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
}

?>
