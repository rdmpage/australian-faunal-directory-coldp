<?php


error_reporting(E_ALL);


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
function get_containers()
{
	$containers = array();

	$sql = 'SELECT DISTINCT PUB_PARENT_JOURNAL_TITLE 
	FROM bibliography 
	WHERE PUB_PARENT_JOURNAL_TITLE IS NOT NULL 
	ORDER BY PUB_PARENT_JOURNAL_TITLE';

	$data = do_query($sql);

	foreach ($data as $obj)
	{
		$containers[] = $obj->PUB_PARENT_JOURNAL_TITLE;
	}
	
	return $containers;

}

//----------------------------------------------------------------------------------------
function get_container_count($limit = 100)
{
	$containers = array();

	$sql = 'SELECT COUNT(PUBLICATION_GUID) AS c, PUB_PARENT_JOURNAL_TITLE 
	FROM bibliography 
	WHERE PUB_PARENT_JOURNAL_TITLE IS NOT NULL
	GROUP BY PUB_PARENT_JOURNAL_TITLE
	ORDER BY c DESC
	';

	$data = do_query($sql);

	foreach ($data as $obj)
	{
		$containers[$obj->PUB_PARENT_JOURNAL_TITLE] = $obj->c;
	}
	
	if (count($containers) > $limit)
	{
		$containers = array_slice($containers, 0, $limit, true);
	}
	
	return $containers;

}


//----------------------------------------------------------------------------------------
function get_works_in_container($containerTitle)
{
	$works = array();
	
	$sql = 'SELECT * 
	FROM bibliography 
	WHERE PUB_PARENT_JOURNAL_TITLE = "' . str_replace('"', '""', $containerTitle) . '"
	ORDER BY PUB_YEAR
	';

	$data = do_query($sql);

	foreach ($data as $obj)
	{
		$work = new stdclass;
		
		$work->id = $obj->PUBLICATION_GUID;	
		
		if (isset($obj->PUB_YEAR))
		{
			$work->year = $obj->PUB_YEAR;
		}
		
		if (isset($obj->PUB_TITLE))
		{
			$work->title = $obj->PUB_TITLE;
		}
		
		if (isset($obj->doi))
		{
			$work->doi = $obj->doi;
		}
	
		$works[$work->id] = $work;
	}
	
	return $works;
	
}


//----------------------------------------------------------------------------------------
function works_by_decade($works)
{
	$decades = array();
	
	foreach ($works as $work)
	{
		if (isset($work->year))
		{
			if (is_numeric($work->year))
			{
				$decade = floor($work->year / 10);
			
				if (!isset($decades[$decade]))
				{
					$decades[$decade] = array();
				}
				$decades[$decade][] = $work;
			}
		}
	}
	
	return $decades;
}

//----------------------------------------------------------------------------------------
function works_to_html($works)
{
	echo '<div>';
	
	foreach ($works as $work)
	{
		echo '<div style="float:left;width:14px;height:14px;">';
		
		
		echo '<a href=""';
		
		if (isset($work->title))
		{
			echo ' title="' . htmlentities($work->title) . '"';
		}
		
		echo '>';
		
		echo '<div style="width:12px;height:12px;background-color:green;margin:1px;';
		
		$opacity = 0.1;
		
		if (isset($work->doi))
		{
			$opacity += 0.2;
		}
		
		echo 'opacity:' . $opacity;
		echo '">';
		echo '</div>';
		echo '</a>';
		
		
		echo '</div>';
	
	}
	
	echo '</div>';

}

//----------------------------------------------------------------------------------------
function decades_to_html($decades)
{
	echo '<div>';
	
	foreach ($decades as $decade => $works)
	{
		echo '<div style="float:left;width:14px;height:14px;">';
		echo '</div>';
		
		foreach ($works as $work)
		{
			echo '<div style="float:left;width:14px;height:14px;">';
		
			echo '<a href=""';
		
			if (isset($work->title))
			{
				echo ' title="' . htmlentities($work->title) . '"';
			}
		
			echo '>';
		
			echo '<div style="width:12px;height:12px;background-color:green;margin:1px;';
		
			$opacity = 0.1;
		
			if (isset($work->doi))
			{
				$opacity += 0.2;
			}
		
			echo 'opacity:' . $opacity . ';';
			echo '">';
			echo '</div>';
			
			echo '</a>';
		
		
			echo '</div>';
	
		}
	}
	
	echo '</div>';

}


$containers = get_containers();
//print_r($containers);

$containers = get_container_count(200);
//print_r($containers);

foreach ($containers as $containerTitle => $c)
{
	echo "<h3>$containerTitle</h3>";

	$works = get_works_in_container($containerTitle);
	//print_r($works);

	echo '<div style="display:block;overflow:auto;">';
	echo works_to_html($works);

	//echo '<div style="clear:both;"/>';
	//echo "<h5>$containerTitle</h5>";

	$decades = works_by_decade($works);

	//echo decades_to_html($decades);

	echo '</div>';
	//echo '<div style="clear:both;"/>';
}
?>


