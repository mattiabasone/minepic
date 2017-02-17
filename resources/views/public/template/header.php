<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $title; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo $description; ?>" />
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <meta name="robots" content="Index, Follow"/>
    <meta name="author" content="Mattia: info[AT]minepic.org" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo url('/assets/img/favicon.ico'); ?>" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo url('/assets/css/typeahead.css'); ?>" />
    <link rel="stylesheet" href="<?php echo url('/assets/css/style.css'); ?>" />
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
</head>
<body>

<div id="master_header">
    <div class="container">
        <div class="inner">
            <div class="logo"><a href="<?php echo url('/'); ?>"><img src="<?php echo url('/avatar/128/be1cac3b60f04e0dba12c77cc8e0ec21'); ?>" alt="MinePic Logo" /></a></div>
            <div class="title">
                <a href="<?php echo url('/'); ?>"><h1>MinePic</h1></a>
                <div id="subtitle" class="subtitle unselectable">
                    <?php echo $randomMessage; ?>
                </div>
            </div>
            <br />
        </div>
    </div>
</div><!-- master_header -->
<div id="main-cont" class="main-cont container">
