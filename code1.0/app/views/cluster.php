<!DOCTYPE html>
<html>
<head>
    <title>Swoole-PHP</title>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="1">
    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">当前服务器为:<b><?php echo $name['user_name']; ?></b> for Mysql</div>
    </div>
</div>
</body>
</html>