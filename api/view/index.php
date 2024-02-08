<!DOCTYPE html>
<html>
<head>
    <title>Пример формы с закрепленной строкой</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-bottom: 70px;
        }

        .fixed-bottom {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
            padding: 10px;
        }

        .fixed-top {
            position: fixed;
            top: 0;
            right: 0;
            padding: 10px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<div class="fixed-top">
    <div class="container">
        <div class="row justify-content-end">
            <div class="col-auto">
                <span id="login"></span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col">
            <div class="card mb-3">
                <div class="card mb-3">
                    <div class="card-body" id="output" style="white-space: pre-wrap;"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="fixed-bottom">
    <div class="container">
        <div class="row">
            <div class="col">
                <input type="text" id="bottomInput" class="form-control" placeholder="Вода">
            </div>
            <div class="col-auto">
                <button id="bottomButton" class="btn btn-primary">Отправить</button>
            </div>
        </div>
    </div>
</div>

<script src="js/ApiJs.js"></script>
</body>
</html>