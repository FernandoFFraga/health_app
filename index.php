<?php

use App\Models\Day;

require("vendor/autoload.php");
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$dayModel = new Day();

if(!empty($_GET['d'])) {

    if($_GET['action'] == "PREV") {
        $select = date("Y-m-d", strtotime("-1 day", strtotime($_GET['d'])));
    } else if($_GET['action'] == "NEXT") {
        $select = date("Y-m-d", strtotime("+1 day", strtotime($_GET['d'])));
    } else {
        $select = $_GET['d'];
    }

    
} else {
    $select = date("Y-m-d");
}

echo $select;

$day = $dayModel->loadByDay(trim($select));
$date = date("d-m-Y", strtotime(trim($select)));

?>

<!doctype html>
<html lang="pt-br">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="<?= CONF_URL_BASE; ?>/assets/css/default.css?<?= filemtime("assets/css/default.css"); ?>">
<link rel="stylesheet" href="<?= CONF_URL_BASE; ?>/assets/css/default-media-queries.css?<?= filemtime("assets/css/default-media-queries.css"); ?>">
<script src="https://kit.fontawesome.com/cb4d4bdc9e.js" crossorigin="anonymous"></script>

<title>Health App - Home</title>
</head>
<body>

<div class="container-fluid" id="main">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-4 aligner">
            <div class="brand">
                <img src="<?= CONF_URL_BASE; ?>/assets/img/fernando.svg" width="50%" alt="">
            </div>
            <div class="controllers">
                <button id="ctrl-training" data-value="<?= $day->training ?? 0; ?>">
                    <i class="fas fa-dumbbell"></i>
                </button>
                <button id="ctrl-water" data-value="<?= $day->water ?? 0; ?>">
                    <i class="fas fa-tint"></i>
                </button>
                <button id="ctrl-food" data-value="<?= $day->food ?? 0; ?>">
                    <i class="fas fa-utensils"></i>
                </button>
                <button id="ctrl-sleep" data-value="<?= $day->sleep ?? 0; ?>">
                    <i class="fas fa-bed"></i>
                </button>
            </div>
            <div class="calendary">
                <div class="calendary-content">
                    <button id="ctrl-prev-date">
                        <i class="fas fa-arrow-circle-left"></i>
                    </button>
                    <span id="ctrl-date"><?= $date ?></span>
                    <button id="ctrl-next-date">
                        <i class="fas fa-arrow-circle-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    var day = "<?= $select; ?>";
    var basepath = "<?= CONF_URL_BASE; ?>";
</script>
<script src="<?= CONF_URL_BASE; ?>/assets/js/app.js?<?= filemtime("assets/js/app.js"); ?>"></script>
</body>
</html>