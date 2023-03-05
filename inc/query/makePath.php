<?php
require_once('../entity/matrix.php');

$matrix = new Matrix((int)$_POST['r'], (int)$_POST['c'], $_POST['m']);

$start = array_map('intVal', explode(",", $_POST['s']));
--$start[0]; --$start[1];

$end = array_map('intVal', explode(",", $_POST['e']));
--$end[0]; --$end[1];

echo $matrix->execute($start, $end);