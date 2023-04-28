<?php
$data = json_decode(file_get_contents('data.json'));

// emulate network delay
// sleep(1);

$dir = SORT_ASC;
$sort = null;
$limit = 10;
$offset = 0;
if (isset($_GET['limit'])) {
    $limit = (int) $_GET['limit'];
    if ($limit < 1) {
        $limit = 1;
    }
}
if (isset($_GET['offset'])) {
    $offset = (int) $_GET['offset'];
    if ($offset < 0) {
        $offset = 0;
    }
}
if (isset($_GET['sort'])) {
    foreach (explode(',', $_GET['sort']) as $column) {
        //TODO handle multiple sort columns, use array, later check for array or string
        if (stristr($_GET['sort'], ':')) {
            $parts = explode(':', $_GET['sort'], 2);
            $sort = preg_replace('/[^a-z0-9\.\_\-]/i', '', $parts[0]);
            if ($parts[1] == 'dsc') {
                $dir = SORT_DESC;
            }
        }
    }
}
// do filters
if (isset($_GET['search']) && !isset($_GET['column_search'])) {
    $search = preg_replace('/[^a-z0-9\s\.\_\-]/i', '', $_GET['search']);
    $data = array_filter($data, function($o) use ($search) {
        foreach (get_object_vars($o) as $k=>$v) {
            if (stristr($v, $search)) {
                return true;
            }
        }
        return false;
    });
}

if (isset($_GET['column_search'])) {
    $columnParts = [];
    foreach ($_GET['column_search'] as $column) {
        if (stristr($column, ':')) {
            $columnParts[] = explode(':', $column, 2);
        }
    }

    $data = array_filter($data, function($o) use ($columnParts) {
        $inColumn = [];
        foreach ($columnParts as $columnPart) {
            if (!property_exists($o, $columnPart[0])) {
                continue;
            }

            if (stristr($o->{$columnPart[0]}, $columnPart[1])) {
                $inColumn[] = 1;
            } else {
                $inColumn[] = 0;
            }
        }

        return array_product($inColumn);
    });
}

// do sort - todo handle multiple column sort
if ($sort) {
    $cols = array_column($data, $sort);
    array_multisort($cols, $dir, $data);
}

// get total
$count = count($data);
// do slice for pagination
$data = array_slice($data, $offset, $limit);

header('Access-Control-Allow-Origin', 'http://localhost:8080');
echo json_encode([
    'data' => $data,
    'total' => $count,
]);
