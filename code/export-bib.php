<?php

// export distinct references for ColDP

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

$headings = array('ID', 'type', 'author', 'title', 'containerTitle', 'issued', 'collectionTitle', 'page', 'publisher', 'link');

$keys = array('PUBLICATION_GUID', 'PUB_TYPE', 'PUB_AUTHOR', 'PUB_TITLE', 
	'PUB_PARENT_JOURNAL_TITLE', 'PUB_YEAR', 'PUB_PARENT_BOOK_TITLE', 'PUB_PAGES', 'PUB_PUBLISHER');


echo join("\t", $headings) . "\n";

// get distinct ids
$sql = 'SELECT DISTINCT PUBLICATION_GUID FROM bibliography';

$ids = array();

$data = do_query($sql);

foreach ($data as $obj)
{
	$ids[] = $obj->PUBLICATION_GUID;
}

// get each reference in turn
foreach ($ids as $PUBLICATION_GUID)
{
	$sql = 'SELECT * FROM bibliography WHERE PUBLICATION_GUID="' . $PUBLICATION_GUID . '" LIMIT  1';
	
	// echo $sql . "\n";

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
					case 'PUBLICATION_GUID':
						$output->ID = $obj->$k;
						
						$output->link = 'https://biodiversity.org.au/afd/publication/' . $obj->$k;
						break;
						
					case 'PUB_TYPE':
						switch ($obj->$k)
						{
							case 'Article in Journal':
								$output->type = 'article-journal';
								break;

							case 'Book':
								$output->type = 'book';
								break;

							case 'Chapter in Book':
								$output->type = 'chapter';
								break;

							case 'URL':
								$output->type = 'webpage';
								break;
						
							case 'This Work':
								$output->type = 'dataset';
								break;

							case 'Section in an Article':
								$output->type = 'article';
								break;

							case 'Miscellaneous':
							default:
								$output->type = 'article';
								break;
						}
						break;
						
					case 'PUB_AUTHOR':
						$output->author = $obj->$k;
						break;
						
					case 'PUB_TITLE':
						$output->title = strip_tags($obj->$k);
						break;						
					
					case 'PUB_PARENT_JOURNAL_TITLE':
						$output->containerTitle = $obj->$k;
						break;

					case 'PUB_YEAR':
						$output->issued = $obj->$k;
						break;
						
					case 'PUB_PARENT_BOOK_TITLE':
						$output->collectionTitle = $obj->$k;
						break;

					case 'PUB_PAGES':
						$output->page = $obj->$k;
						break;

					case 'PUB_PUBLISHER':
						$output->publisher = $obj->$k;
						break;
						
				
					default:
						break;
				}
			}
		}
		
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

}

?>
