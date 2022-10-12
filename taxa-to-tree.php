<?php

require_once(dirname(__FILE__) . '/tree.php');

//----------------------------------------------------------------------------------------

$filename = 'taxa.tsv';


$nodes = array();
$tree = new Tree();

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
		
			print_r($obj);
			
			 //ID	parentID	nameID
			 
			$nodes[$obj->ID] = new Node($obj->ID);
			
			if (!isset($nodes[$obj->parentID]))
			{
				$nodes[$obj->parentID] = new Node($obj->parentID);	
			}
			
			if ($nodes[$obj->parentID]->GetChild())
			{
				$sibling = $nodes[$obj->parentID]->GetChild()->GetRightMostSibling();
				$sibling->SetSibling($nodes[$obj->ID]);
			}
			else
			{						
				$nodes[$obj->parentID]->SetChild($nodes[$obj->ID]);
			}
			$nodes[$obj->ID]->SetAncestor($nodes[$obj->parentID]);
				
		}
	}	
	$row_count++;
}

$roots= array();
foreach ($nodes as $id => $node)
{
	if (!$node->GetAncestor())
	{
		$roots[] = $id;
	}
}

print_r($roots);

echo count($roots) . " trees in the forest\n";






?>

