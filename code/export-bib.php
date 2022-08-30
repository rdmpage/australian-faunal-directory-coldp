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
	
$key_mapping = array
(
'PUBLICATION_GUID' 			=> 'ID',			
'PUB_TYPE' 					=> 'type', 			
'PUB_AUTHOR' 				=> 'author', 		
'PUB_TITLE'					=> 'title',			
'PUB_PARENT_JOURNAL_TITLE' 	=> 'containerTitle',
'issn' 						=> 'issn',			
'PUB_YEAR' 					=> 'issued',		
'PUB_PARENT_BOOK_TITLE' 	=> 'collectionTitle',
'PUB_PAGES'					=> 'page',			
'PUB_PUBLISHER' 			=> 'publisher',		
'doi' 						=> 'doi'			
);



$headings = array_values($key_mapping);
$headings[] = 'link';

echo join("\t", $headings) . "\n";

// get distinct ids
$sql = 'SELECT DISTINCT PUBLICATION_GUID FROM bibliography';

//$sql .= ' WHERE issn="0003-4150"';

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
		//print_r($obj);
				
		$output = new stdclass;
		
		foreach ($obj as $k => $v)
		{
			switch ($k)
			{
				case 'PUBLICATION_GUID':
					$output->{$key_mapping[$k]} = $v;							
					$output->link = 'https://biodiversity.org.au/afd/publication/' . $v;							
					break;
					
				case 'PUB_TYPE':
					switch ($v)
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
						
				default:
					if (isset($key_mapping[$k]))
					{
						$output->{$key_mapping[$k]} = $v;
					}
					break;
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
