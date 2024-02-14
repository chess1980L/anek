<?php


namespace core\base\model;

use api\controller\ApiBaseController;

use api\messages\RequestError;
use core\base\controller\Singleton;
use core\base\exceptions\DbException;
use core\base\Settings\Db;

use PDO;


class ApiModel
{
    protected $pdo;

    use Singleton;

    private function __construct()
    {

        try {
            $this->pdo = Db::Instance()->getPdo();
        } catch (\Exception $e) {
            throw new DbException('Ошибка подключения к базе данных: ' . $e->getMessage(), $e->getCode());
        }
    }

    public static function loginModel($login)
    {

        $pdo = self::Instance()->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM id_teleggram WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $login;
        } else {
            return false;
        }
    }


    static function apiSwitchModel($action, $ctg, $quantity, $login)
    {
        $pdo = self::Instance()->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        switch ($action) {
            case 'random':
                // Вызываем метод для случая, когда $action равно 'random'
                $result = self::handleRandom($pdo, $ctg, $quantity, $login);
                break;

            case 'last':
                // Вызываем метод для случая, когда $action равно 'last'
                $result = self::handleLast($pdo, $ctg, $quantity, $login);
                break;

            case 'Clrh':
                // Вызываем метод для случая, когда $action равно 'clrh'
                $result = self::handleClrh($pdo, $ctg, $quantity, $login);
                break;

            case 'list':
                // Вызываем метод для случая, когда $action равно 'list'
                $result = self::handleList($pdo);
                break;

            default:
                // Вызываем метод для случая, когда $action не совпадает с ни одним из вариантов
                $result = self::handleDefault($pdo, $ctg, $quantity);
                break;
        }

        ApiBaseController::encodeAndEcho($result);

    }

    static function handleRandom($pdo, $ctg, $quantity, $login)
    {

        // Находим id соответствующие переменной $login в таблице id_teleggram
        $idTelegram = self::getUserIdByLogin($pdo, $login);

        // Находим id шуток, которые уже были просмотрены пользователем в таблице idtelegram_joke
        $viewedJokes = self::getViewedJokesByUserId($pdo, $idTelegram);

        // Получаем все шутки, чтобы выбирать из них случайные шутки
        $allJokes = self::getJokes($pdo, $ctg, '*', $viewedJokes);

        // Проверяем, если количество всех шуток меньше или равно количеству случайных шуток,
        // то возвращаем все шутки
        if (count($allJokes) <= $quantity) {
            return $allJokes;
        }

        // Выбираем случайные шутки в количестве $quantity
        $randomJokes = [];
        $randomIndexes = array_rand($allJokes, $quantity);

        // Если возвращается одно случайное число, преобразуем его в массив
        if (!is_array($randomIndexes)) {
            $randomIndexes = [$randomIndexes];
        }

        // Получаем случайные шутки из индексов исходного массива
        foreach ($randomIndexes as $index) {
            $randomJokes[] = $allJokes[$index];
        }

        // Записываем выбранные шутки как просмотренные с помощью метода fillViewed()
        self::fillViewed($pdo, $login, array_column($randomJokes, 'id'));

        // Возвращаем выбранные случайные шутки
        return $randomJokes;
    }


    static function handleLast($pdo, $ctg, $quantity, $login)
    {


        // Находим id соответствующие переменной $login в таблице id_teleggram
        $idTelegram = self::getUserIdByLogin($pdo, $login);

        // Находим id шуток, которые уже были просмотрены пользователем в таблице idtelegram_joke
        $viewedJokes = self::getViewedJokesByUserId($pdo, $idTelegram);


        $jokes = self::getJokes($pdo, $ctg, $quantity, $viewedJokes);


        // Собираем id новых шуток
        $newJokeIds = array_column($jokes, 'id');

        self::fillViewed($pdo, $login, $newJokeIds);

        return $jokes;
    }

    static function handleClrh($pdo, $ctg, $quantity, $login)
    {


        // Находим id соответствующие переменной $login в таблице id_teleggram
        $sql = "SELECT id FROM id_teleggram WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $idTelegram = $result['id'];

        // Находим id шуток, которые уже были просмотрены пользователем в таблице idtelegram_joke
        $sql = "SELECT id FROM idtelegram_joke WHERE idtelegram = :idTelegram";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idTelegram', $idTelegram, PDO::PARAM_INT);
        $stmt->execute();
        $viewedJokes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Удаляем все связи с данным id из таблицы idtelegram_joke
        $sql = "DELETE FROM idtelegram_joke WHERE idtelegram = :idTelegram";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idTelegram', $idTelegram, PDO::PARAM_INT);
        $stmt->execute();

        return "Все просмотренные шутки были удалены";
    }


    static function getJokes($pdo, $ctg, $quantity, $viewedJokes)
    {
        if (empty($viewedJokes)) {
            // Если таблица просмотренных шуток пуста, выбираем шутки из таблицы joke
            if ($quantity == '*') {
                $sql = "SELECT id, joke FROM joke ORDER BY id DESC";
            } else {
                $sql = "SELECT id, joke FROM joke ORDER BY id DESC LIMIT :quantity";
            }
            $stmt = $pdo->prepare($sql);
            if ($quantity != '*') {
                $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            }
        } else {
            if (empty($ctg)) {
                // Если $ctg пусто, выбираем шутки из таблицы joke, исключая просмотренные шутки
                if ($quantity == '*') {
                    $sql = "SELECT id, joke FROM joke WHERE id NOT IN (" . implode(',',
                            $viewedJokes) . ") ORDER BY id DESC";
                } else {
                    $sql = "SELECT id, joke FROM joke WHERE id NOT IN (" . implode(',',
                            $viewedJokes) . ") ORDER BY id DESC LIMIT :quantity";
                }
                $stmt = $pdo->prepare($sql);
                if ($quantity != '*') {
                    $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                }
            } else {
                // Если $ctg не пусто, выбираем шутки соответствующие разделу из таблицы joke_tag, исключая просмотренные шутки
                if ($quantity == '*') {
                    $sql = "SELECT joke.id, joke.joke FROM joke
                    JOIN joke_tag ON joke.id = joke_tag.id_joke
                    JOIN tag ON joke_tag.id_tag = tag.id
                    WHERE tag.tag = :ctg AND joke.id NOT IN (" . implode(',', $viewedJokes) . ")
                    ORDER BY joke.id DESC";
                } else {
                    $sql = "SELECT joke.id, joke.joke FROM joke
                    JOIN joke_tag ON joke.id = joke_tag.id_joke
                    JOIN tag ON joke_tag.id_tag = tag.id
                    WHERE tag.tag = :ctg AND joke.id NOT IN (" . implode(',', $viewedJokes) . ")
                    ORDER BY joke.id DESC LIMIT :quantity";
                }
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':ctg', $ctg, PDO::PARAM_STR);
                if ($quantity != '*') {
                    $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                }
            }
        }
        $stmt->execute();
        $jokes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($jokes == null) {
            RequestError::displayErrorMessage(4);
            $ctg = '';
            return ApiModel::getJokes($pdo, $ctg, $quantity, $viewedJokes);
        }

        return $jokes;
    }


    static function handleList($pdo)
    {
        $sql = "SELECT tag FROM tag";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $tags;
    }

    static function handleDefault($pdo, $ctg, $quantity)
    {
        // Ваш код для случая, когда $action не совпадает с ни одним из вариантов
        // Верните результат этого кода
    }


    static function fillViewed($pdo, $login, $newJokeIds)
    {


        // Находим id соответствующие переменной $login в таблице id_teleggram
        $sql = "SELECT id FROM id_teleggram WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $idTelegram = $result['id'];

        foreach ($newJokeIds as $idJoke) {
            $sql = "INSERT INTO idtelegram_joke (idtelegram, idjoke) VALUES (:idTelegram, :idJoke)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':idTelegram', $idTelegram, PDO::PARAM_INT);
            $stmt->bindValue(':idJoke', $idJoke, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    static function getUserIdByLogin($pdo, $login)
    {
        $sql = "SELECT id FROM id_teleggram WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }


    static function getViewedJokesByUserId($pdo, $idTelegram)
    {
        $sql = "SELECT idjoke FROM idtelegram_joke WHERE idtelegram = :idTelegram";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idTelegram', $idTelegram, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

}