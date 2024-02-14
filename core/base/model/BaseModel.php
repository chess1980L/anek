<?php

namespace core\base\model;

use core\base\exceptions\DbException;
use core\base\controller\Singleton;
use core\base\settings\Db;
use core\user\controller\Pagination;
use PDO;

class BaseModel
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

    static function checkModelCookie($username, $password = '')
    {
        $pdo = self::Instance()->pdo;

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($password) {
            $stmt = $pdo->prepare("SELECT * FROM user WHERE user = ? AND password = ?");
            $stmt->execute([$username, $password]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            $stmt = $pdo->prepare("SELECT * FROM user WHERE user = ?");
            $stmt->execute([$username]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return "adminJS.js";
            } else {
                return false;
            }
        }
    }

    static function processCrud($params)
    {

        if ($params['action'] === 'r') {
            return self::readDatabase($params);
        } else {
            if ($params['action'] === 'c') {
                return self::createLine($params);
            } else {
                if ($params['action'] === 'd') {
                    return self::deleteRecord($params);
                } else {
                    if ($params['action'] === 'u') {
                        return self::updateJoke($params);
                    } else {
                        return 'Invalid action';
                    }
                }
            }
        }

    }

    static function readDatabase($params = null)
    {
        $pdo = self::Instance()->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($params['paginationJs'])) {

            if (isset($params['tag']) && $params['tag'] != '') {
                $tagId = $pdo->prepare("SELECT id FROM tag WHERE tag = ?");
                $tagId->execute([$params['tag']]);
                $tagId = $tagId->fetch(PDO::FETCH_ASSOC)['id'];

                $countJokes = $pdo->prepare("SELECT COUNT(DISTINCT id_joke) AS count FROM joke_tag WHERE id_tag = ?");
                $countJokes->execute([$tagId]);
                $countJokes = $countJokes->fetch(PDO::FETCH_ASSOC)['count'];
            } else {
                // Иначе считаем количество всех анекдотов
                $countJokes = $pdo->query("SELECT COUNT(*) FROM joke")->fetchColumn();
            }

            $countPage = (int)ceil($countJokes / LIMIT);

            if ($countPage < 2) {
                return '';
            }

            if (!isset($params['currentElement'])) {
                $currentElement = 0;
            } else {
                $currentElement = $params['currentElement'];
            }

            $pagin = Pagination::pagination($currentElement, $countPage);

            return $pagin;
        }

//  получаем количество анекдотов у юзера
        if (isset($params['user']) && ($params['user'] === '*')) {
            $stmt = $pdo->prepare("SELECT user FROM user");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $users = array();
            foreach ($result as $row) {
                $users[] = $row['user'];
            }
            return $users;
        }
        if (isset($params['user']) && isset($params['startDate']) && isset($params['endDate'])) {
            $user = $params['user'];
            $startDate = $params['startDate'];
            $endDate = $params['endDate'];

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM joke WHERE user = :user AND joke_date BETWEEN :startDate AND :endDate");
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->execute();

            $count = $stmt->fetchColumn();

            return $count;
        }

        if (isset($params[0]) && ($params[0] == 'duplicates')) {
            $stmt = $pdo->prepare("
        SELECT joke.id, joke.joke
        FROM joke
        LEFT JOIN joke_tag ON joke.id = joke_tag.id_joke
        LEFT JOIN tag ON tag.id = joke_tag.id_tag
        GROUP BY joke.id
    ");
            $stmt->execute();
            $jokes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($jokes as $joke) {
                $result['jokes'][] = [
                    'id' => $joke['id'],
                    'joke' => $joke['joke']
                ];
            }

            return $result;
        }

// получить количество анекдотов
        if (isset($params['jokes']) && $params['jokes'] == '*') {
            if (isset($params['tag'])) {
                $tag = $params['tag'];
                $countStmt = $pdo->prepare("
            SELECT COUNT(joke.id)
            FROM joke
            LEFT JOIN joke_tag ON joke.id = joke_tag.id_joke
            LEFT JOIN tag ON tag.id = joke_tag.id_tag
            WHERE tag.tag = :tag
        ");
                $countStmt->execute([':tag' => $tag]);
                $count = $countStmt->fetchColumn();
                return $count;
            } else {
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM joke");
                $countStmt->execute();
                $count = $countStmt->fetchColumn();
                return $count;
            }
        }

        // Проверяем, существует ли идентификатор в массиве и в таблице joke
        if (isset($params['jokes']) && isset($params['jokes']['id'])) {

            $id = $params['jokes']['id'];
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM joke WHERE id = :id");
            $checkStmt->execute([':id' => $id]);
            $count = $checkStmt->fetchColumn();
            if ($count == 0) {
                // Если идентификатор не существует, возвращаем true
                return true;
            }
            $stmt = $pdo->prepare("
            SELECT joke.id, joke.joke, GROUP_CONCAT(tag.id SEPARATOR ',') AS tags, joke_date, user
            FROM joke
            LEFT JOIN joke_tag ON joke.id = joke_tag.id_joke
            LEFT JOIN tag ON tag.id = joke_tag.id_tag
            WHERE joke.id = :id
            GROUP BY joke.id
        ");
            $stmt->execute([':id' => $params['jokes']['id']]);
            $joke = $stmt->fetch(PDO::FETCH_ASSOC);
            // Если строка найдена, то возвращаем ее
            if ($joke) {
                $tags = [];
                if (!empty($joke['tags'])) {
                    $tagIds = explode(',', $joke['tags']);
                    $tagStmt = $pdo->prepare("
                    SELECT id, tag
                    FROM tag
                    WHERE id IN (" . implode(',', array_fill(0, count($tagIds), '?')) . ")
                ");
                    $tagStmt->execute($tagIds);
                    $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
                }
                return [
                    'jokes' => [
                        [
                            'id' => $joke['id'],
                            'joke' => $joke['joke'],
                            'tags' => $tags,
                            'joke_date' => $joke['joke_date'],
                            'user' => $joke['user']
                        ]
                    ],
                    'tags' => $tags
                ];
            }
        }

// выбрать все теги
        if (isset($params['tags']) && $params['tags'] == '*') {
            $tagStmt = $pdo->prepare("SELECT tag FROM tag");
            $tagStmt->execute();
            $tags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
            return $tags;
        }


// выборка анекдотов по тегу
        if ((isset($params[0]) && is_array($params[0]) && !empty($params[0][0])) or (isset($params['jsTag']))) {

            if (isset($params['jsTag'])) {
                $tagName = $params['jsTag'];
            } else {
                $tagName = $params[0][0];
            }

            $limit = LIMIT;

            if (isset($params['offset'])) {
                $offset = $limit * $params['offset'];
            } else {
                $offset = 0;
            }

            $data = self::getJokesByTag($tagName, $limit, $offset);
            $tagStmt = $pdo->prepare("SELECT id, tag FROM tag");
            $tagStmt->execute();
            $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
            $data['tags'] = $tags;

            return $data;
        }


// Проверяем наличие ключа "offset" в массиве $params
        if (isset($params['offset'])) {
            // Если ключ существует, выбираем нужное количество шуток со смещением равным значению "offset"
            $limit = LIMIT;
            $offset = $limit * $params['offset'];
            $stmt = $pdo->prepare("
        SELECT joke.id, joke.joke, GROUP_CONCAT(tag.id SEPARATOR ',') AS tags, joke_date, user
        FROM joke
        LEFT JOIN joke_tag ON joke.id = joke_tag.id_joke
        LEFT JOIN tag ON tag.id = joke_tag.id_tag
        GROUP BY joke.id
        ORDER BY joke.id DESC
        LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        if (isset($params['tags']) && $params['tags'] == '**') {

            $tagStmt = $pdo->prepare("SELECT id, tag FROM tag");
            $tagStmt->execute();
            $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
            $data['tags'] = $tags;
            return $data;

        } else {
            // Если ключ не существует, выбираем последние 20 шуток и их теги
            $stmt = $pdo->prepare("
        SELECT joke.id, joke.joke, GROUP_CONCAT(tag.id SEPARATOR ',') AS tags, joke_date, user
        FROM joke
        LEFT JOIN joke_tag ON joke.id = joke_tag.id_joke
        LEFT JOIN tag ON tag.id = joke_tag.id_tag
        GROUP BY joke.id
        ORDER BY joke.id DESC
        LIMIT :limit");
            $stmt->bindValue(':limit', LIMIT, PDO::PARAM_INT);
        }
        $stmt->execute();
        $jokes = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Создаем массив для шуток и тегов
        $data = ['jokes' => [], 'tags' => []];
// Заполняем массив шутками и их тегами
        foreach ($jokes as $joke) {
            $tags = [];
            if (!empty($joke['tags'])) {
                $tagIds = explode(',', $joke['tags']);
                $tagStmt = $pdo->prepare("
            SELECT id, tag
            FROM tag
            WHERE id IN (" . implode(',', array_fill(0, count($tagIds), '?')) . ")");
                $tagStmt->execute($tagIds);
                $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $data['jokes'][] = [
                'id' => $joke['id'],
                'joke' => preg_replace("/\n/", "<br/>", $joke['joke']),
                'tags' => $tags,
                'joke_date' => $joke['joke_date'],
                'user' => $joke['user']
            ];
        }
// Заполняем массив тегами
        $tagStmt = $pdo->prepare("SELECT id, tag FROM tag");
        $tagStmt->execute();
        $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
        $data['tags'] = $tags;
        return $data;
    }

    public static function getJokesByTag($tagName, $limit = null, $offset = 0)
    {
        $pdo = self::Instance()->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT joke.id, joke.joke, joke.joke_date, joke.user, GROUP_CONCAT(tag.id) AS tag_ids, GROUP_CONCAT(tag.tag) AS tags
   FROM joke
   INNER JOIN joke_tag ON joke.id = joke_tag.id_joke
   INNER JOIN tag ON tag.id = joke_tag.id_tag
   WHERE joke.id IN (
       SELECT joke.id
       FROM joke
       INNER JOIN joke_tag ON joke.id = joke_tag.id_joke
       INNER JOIN tag ON tag.id = joke_tag.id_tag
       WHERE tag.tag = :tagName
   )
   GROUP BY joke.id
   ORDER BY joke.id DESC";

        if ($limit) {
            $sql .= " LIMIT :offset, :limit";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':tagName', $tagName, PDO::PARAM_STR);

        if ($limit) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        $jokes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = ['jokes' => []];
        foreach ($jokes as $joke) {
            $tagIds = explode(',', $joke['tag_ids']);
            $tags = explode(',', $joke['tags']);
            $tagArray = [];

            for ($i = 0; $i < count($tagIds); $i++) {
                $tagArray[] = [
                    'id' => $tagIds[$i],
                    'tag' => $tags[$i]
                ];
            }

            $data['jokes'][] = [
                'id' => $joke['id'],
                'joke' => $joke['joke'],
                'tags' => $tagArray,
                'joke_date' => $joke['joke_date'],
                'user' => $joke['user']
            ];
        }

        return $data;
    }

    static function createLine($params)
    {
        $pdo = self::Instance()->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $jokeValue = $params['joke']['joke'];
        if (isset($params['joke']) && isset($params['joke']['joke']) && $params['joke']['joke'] != '') {


            if (self::checkingDuplicates($jokeValue) === true) {

                // Find all ids associated with tags
                $tagIds = array();
                if (isset($params['joke']['tags'])) {
                    $tagValues = $params['joke']['tags'];
                    foreach ($tagValues as $value) {
                        $stmt = $pdo->prepare('SELECT id FROM tag WHERE tag = ?');
                        $stmt->execute([$value]);
                        $id = $stmt->fetchColumn();
                        if ($id) {
                            $tagIds[] = $id;
                        } else {
                            // If tag does not exist, create a new tag and get its id
                            $stmt = $pdo->prepare('INSERT INTO tag (tag) VALUES (?)');
                            $stmt->execute([$value]);
                            $tagIds[] = $pdo->lastInsertId();
                        }
                    }
                }
                $user = isset($params['user']) ? $params['user'] : '';
                $jokeDate = date('Y-m-d');

                $stmt = $pdo->prepare('INSERT INTO joke (joke, user, joke_date) VALUES (?, ?, ?)');
                $stmt->execute([$jokeValue, $user, $jokeDate]);
                $jokeId = $pdo->lastInsertId();

                $stmt = $pdo->prepare('INSERT INTO joke_tag (id_joke, id_tag) VALUES (?, ?)');
                foreach ($tagIds as $tagId) {
                    $stmt->execute([$jokeId, $tagId]);
                }
                $data = self::readDatabase();
                return $data;
            } elseif (is_numeric(self::checkingDuplicates($jokeValue))) {
                return self::checkingDuplicates($jokeValue);
            }
        }
        if (isset($params['tag']) && isset($params['tag']['value']) && $params['tag']['value'] != '') {
            $tagValue = $params['tag']['value'];

            $stmt = $pdo->prepare('INSERT INTO tag (tag) VALUES (?)');
            $stmt->execute([$tagValue]);
            $tagId = $pdo->lastInsertId();

            $response = array('status' => 'success');
            return $response;
        }

        return false; // Return false in case of error

    }

    static public function checkingDuplicates($jokeValue)
    {
        $jokeValue = trim($jokeValue); // Убираем пробелы по краям
        $jokeValue = preg_replace('/\s+/', ' ', $jokeValue); // Убираем двойные пробелы

        $jokeValue = preg_replace('/[^a-zA-Zа-яА-Я0-9\s]/u', '',
            $jokeValue); // Убираем специальные символы тире, переносы строк и знаки пунктуации
        $jokeValue = str_replace(',', '', $jokeValue); // Убираем запятые
        $jokeValue = str_replace('.', '', $jokeValue); // Убираем точки
        $jokeValue = str_replace('!', '', $jokeValue); // Убираем восклицательные знаки
        $jokeValue = str_replace('?', '', $jokeValue); // Убираем вопросительные знаки

        $data = self::readDatabase(['duplicates']);
        $jokes = $data['jokes'];

        foreach ($jokes as $joke) {
            $joke['joke'] = trim($joke['joke']); // Убираем пробелы по краям
            $joke['joke'] = preg_replace('/\s+/', ' ', $joke['joke']); // Убираем двойные пробелы

            $joke['joke'] = preg_replace('/[^a-zA-Zа-яА-Я0-9\s]/u', '',
                $joke['joke']); // Убираем специальные символы тире, переносы строк и знаки пунктуации
            $joke['joke'] = str_replace(',', '', $joke['joke']); // Убираем запятые
            $joke['joke'] = str_replace('.', '', $joke['joke']); // Убираем точки
            $joke['joke'] = str_replace('!', '', $joke['joke']); // Убираем восклицательные знаки
            $joke['joke'] = str_replace('?', '', $joke['joke']); // Убираем вопросительные знаки

            if ($joke['joke'] == $jokeValue) {
                return $joke['id']; // Если найден дубликат, возвращаем true
            }
        }

        return true; // Если дубликат не найден, возвращаем false
    }

    public static function deleteRecord($data)
    {
        $pdo = self::Instance()->pdo;

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $jokeId = isset($data['joke']['id']) ? $data['joke']['id'] : null; // Получаем ID шутки из данных
        $tagValue = isset($data['tag']['value']) ? $data['tag']['value'] : null; // Получаем значение тега из данных

        if (!empty($jokeId)) { // Если существует ID шутки
            $stmt = $pdo->prepare('SELECT id FROM joke WHERE id = ?');
            $stmt->execute([$jokeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) { // Если ID шутки не существует в таблице "joke"
                return true; // Возвращаем true
            }

            $stmt = $pdo->prepare('DELETE FROM joke_tag WHERE id_joke = ?');
            $jokeTagDeleted = $stmt->execute([$jokeId]); // Удаляем связи шутки с тегами из таблицы joke_tag

            $stmt2 = $pdo->prepare('DELETE FROM joke WHERE id = ?');
            $jokeDeleted = $stmt2->execute([$jokeId]); // Удаляем шутку из таблицы joke

            if ($jokeDeleted && $jokeTagDeleted) { // Если оба удаления прошли успешно
                $data = self::readDatabase();
                return $data;
            }
        }
        if (!empty($tagValue)) { // Если существует значение тега

            $data = self::getJokesByTag($tagValue);
            $jokes = $data['jokes'];

            foreach ($jokes as $joke) {

                $count = count($joke['tags']);

                if ($count < 2) {
                    return false;
                }
            }

            $stmt = $pdo->prepare('SELECT id FROM tag WHERE tag = ?');
            $stmt->execute([$tagValue]);
            $tagId = $stmt->fetchColumn();


            $stmt3 = $pdo->prepare('DELETE FROM joke_tag WHERE id_tag = ?');
            $jokeTagDeleted = $stmt3->execute([$tagId]); // Удаляем связи тега с шутками из таблицы joke_tag

            $stmt2 = $pdo->prepare('DELETE FROM tag WHERE id = ?');
            $tagDeleted = $stmt2->execute([$tagId]); // Удаляем тег из таблицы tag


            if ($tagDeleted && $jokeTagDeleted) { // Если оба удаления прошли успешно
                return true;
            }
        }
    }

    static function updateJoke($params)
    {
        $pdo = self::Instance()->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($params['tag']) && isset($params['tag']['value']) && isset($params['tag']['updateValue'])) {
            $value = $params['tag']['value'];
            $updateValue = $params['tag']['updateValue'];

            // Ищем тег со значением $value и меняем его на значение $updateValue
            $sql = "UPDATE tag SET tag = :updateValue WHERE tag = :value";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':updateValue', $updateValue);
            $stmt->bindParam(':value', $value);
            $stmt->execute();

            return true;
        }

        $jokeId = $params['joke']['id'];
        $updateJoke = $params['joke']['joke'];
        $updateUser = $params['joke']['user'];
        $updateTags = $params['joke']['tags'];

        // Создаем пустой массив для хранения найденных идентификаторов "tags"
        $tagIds = [];

        // Проходим по массиву $updateTags и ищем соответствующие значения "id" в таблице "tags"
        foreach ($updateTags as $tag) {
            $sql = "SELECT id FROM tag WHERE tag = :tag";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':tag', $tag);
            $stmt->execute();

            // Проверяем, найдено ли значение "id" для текущего тега
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tagIds[] = $row['id'];
            }
        }

        // Обновляем строку в таблице "joke"
        $sql = "UPDATE joke SET joke = :joke, user = :user WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':joke', $updateJoke);
        $stmt->bindParam(':user', $updateUser);
        $stmt->bindParam(':id', $jokeId);
        $stmt->execute();

        // Удаляем существующие связи в таблице "joke_tag" для данной шутки
        $sql = "DELETE FROM joke_tag WHERE id_joke = :id_joke";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_joke', $jokeId);
        $stmt->execute();

        // Добавляем новые связи в таблицу "joke_tag" на основе значений в массиве $tagIds
        foreach ($tagIds as $tagId) {
            $sql = "INSERT INTO joke_tag (id_joke, id_tag) VALUES (:id_joke, :id_tag)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_joke', $jokeId);
            $stmt->bindParam(':id_tag', $tagId);
            $stmt->execute();
        }

        $crud = array();

        if (isset($params['currentElement']) && $params['currentElement'] !== 0) {
            $crud['offset'] = $params['currentElement'];
        }

        if (isset($params['chapter']) && $params['chapter'] !== '') {
            $crud['jsTag'] = $params['chapter'];

        }

        return self::readDatabase($crud);
    }


}