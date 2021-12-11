<?php

require("vendor/autoload.php");

use App\Models\Day;

if(!empty($_POST)) {
    $modelDay = new Day();
    $day = $modelDay->bootstrap($_POST);
    $day->save();
    $day->datebr = date("d-m-Y", strtotime($day->day));

    header('Content-Type: application/json');
    echo json_encode((array) $day->data());
    exit;
}