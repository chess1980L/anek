<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Анекдоты фильтрованные</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
            integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>


</head>
<body>

<div class="container">

    <div class="p-3 mb-2 bg-light text-dark">

        <div class="row">
            <div class="col-sm">
                <h1><a href="/" style="text-decoration:none; color:black;">Анекдоты фильтрованные</a></h1>
            </div>
            <div class="col-sm">

                <div class='d-flex justify-content-end'>

                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">


                        Вход администраторов
                    </button>


                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Введите логин и пароль</h5>
                                    <button type="button" id="close" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="text" class="form-control" placeholder="Username"
                                           aria-label="Username">

                                    <label for="inputPassword5" class="form-label">Пароль</label>
                                    <input type="password" id="inputPassword5" class="form-control"
                                           aria-describedby="passwordHelpBlock">

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-primary">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-danger" id="exit" data-username="<?= $data['username'] ?>">
                            Выход <?= $data['username'] ?></button>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="menu">
        <?php foreach ($data['tags'] as $tag): ?>
            <?php $tagLowerCase = mb_strtolower($tag['tag'], 'UTF-8'); ?>
            <a href="<?= $tagLowerCase ?>" class="btn btn-outline-primary"
               data-id="<?= $tag['id'] ?>"><?= $tag['tag'] ?></a>
        <?php endforeach; ?>
    </div>
    <br>


    <div class="admin" hidden>
        <div class="row">
            <div class="col-sm">
                <div class="input-group">
                    <textarea name="joke" class="form-control" placeholder="Добавить Анекдот" rows="10"
                              aria-label="With textarea" id="joke"></textarea>
                    <button type="button" class="btn btn-info" id="okJoke">OK</button>
                </div>

                <br>

                <div class="row">
                    <div class="col-4">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="ID" aria-label="ID" id="my-input">
                            <button type="button" class="btn btn-info" data-id="1" id="my-btn">OK</button>
                        </div>
                    </div>

                    <div class="col-8">
                        <button type="button" class="btn btn-danger" id="deleteJokeBtn">Удалить анекдот</button>
                        <button type="button" class="btn btn-danger" id="deleteTagBtn">Удалить тег</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="input-group mb-3">

                            <button type="button" class="btn btn-info" id="updateTagOK">изменить тег</button>
                        </div>
                    </div>

                </div>

                <div class="col-6">

                    <button type="button" class="btn btn-success" hidden>посмотреть статистику</button>


                </div>


            </div>


            <div class="col-sm">

                <div class="tag" id="tag">

                    <div class="form-check" hidden>
                        <input class="form-check-input" name="работа" type="checkbox">
                        <label class="form-check-label" for="flexCheckDefault">
                            хахахах
                        </label>
                    </div>


                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Добавить тег" aria-label="ID" id="newTag">
                    <button type="button" class="btn btn-info" id="okNewTag">OK</button>

                </div>


            </div>

        </div>

    </div>

    <p class="link-warning link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" hidden>Warning
        link</p>

    <div id="dataJoke">
        <dataJoke>
            <?php foreach ($data['jokes'] as $joke): ?>
                <div class="p-3 mb-2 bg-light text-dark">
                    <div class="row">
                        <div class="col-sm">
                            <div id="<?= $joke['id'] ?>">
                                <? echo preg_replace("/\n/s", '<br/>', $joke['joke']); ?> <br>
                                <small class="text-muted text-orange"
                                       style="font-size: smaller;"><?= date('d.m.Y H:i',
                                        strtotime($joke['joke_date'])) ?></small>
                            </div>
                            <button type="button" class="btn btn-link" id="<?= $joke['id'] ?>" style="display: none;">
                                редактировать
                            </button>

                            <p><a href="/anekdot-id-<?= $joke['id'] ?>/"
                                  class="link-info link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Info
                                    link</a></p>
                        </div>
                        <div class="col-sm" id="<?= $joke['id'] ?>">
                            <?php foreach ($joke['tags'] as $tag): ?>
                                <?php $lowercaseTag = mb_strtolower($tag['tag'], 'UTF-8'); ?>
                                <a href="/<?= $lowercaseTag ?>" class="">
                                    <button type="button" class="btn btn-outline-info btn-sm">
                                        <?= $tag['tag'] ?>
                                    </button>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </dataJoke>
    </div>


    <div id="myElement" class="d-flex flex-column justify-content-center align-items-center" style="height: 1vh;">
        <?= $data['pagination'] ?>
    </div>

    <div class="d-flex flex-column justify-content-end align-items-center" style="height: 1vh;">
        <div class="jsP">
            <!-- Ваше содержимое -->
        </div>
    </div>

</div>

<?php
include 'DatePicker.php';
?>


<script src="/javascript/<?= $data['src'] ?>"></script>
</body>
</html>