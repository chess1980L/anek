<?php

define('VG_ACCESS', true);

header('Content-Type:text/html;charset=utf-8');
session_start();

require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';
require_once 'core/classes/qqpdo.php';


$dbconf = ['host' => 'localhost', 'dbname' => 'joke', 'user' => 'root', 'passw' => ''];

$db = QQPDO::setdb($dbconf);

$currDate = date("Y-m-d H:i:s");

// Добавляем запись через exec
$anekdata1 = ['joke' => 'Тестовый анекдот N1', 'joke_date' => $currDate, 'user' => 1];
$db->exec("INSERT INTO joke(`joke`, `joke_date`, `user`) VALUES (:joke, :joke_date, :user)", $anekdata1);
$id1 = $db->getLastInsertedId();
echo "Первый анекдот добавлен. id= {$id1}<br>\n";

// Добавляем запись через insert.
// Первый параметр - название таблицы,
// второй - ассоциативный массив в формате название_поля:значение
// третий параметр - возвращать ли id добавленной записи или нет
$anekdata2 = ['joke' => 'Тестовый анекдот N2', 'joke_date' => $currDate, 'user' => 1];
$id2 = $db->insert('joke', $anekdata2, true);
echo "Второй анекдот добавлен. Его id = {$id2}\n";

//Обновляем запись через update
// Первый параметр - название таблицы,
// второй - ассоциативный массив в формате название_поля:значение
// признак, по которому обновляются строки, в формате поле => значение. Может быть несколько полей, тогда условия объединяются через AND
$anekdata2 = ['joke' => 'Обновлённый тестовый анекдот N2'];
$db->update('joke', $anekdata2, array('id' => $id2));

// А теперь извлечём данные из joke
//Если мы извлекаем только одну строку, то используем getResult

$anek = $db->getResult("SELECT * FROM joke where id=:id", array('id' => $id2));
echo "Извлекаем второй анекдот: <br>\n";
echo var_export($anek, true) . "<br>\n";

// Если извлекаем несколько строк, используем getResults
$aneks = $db->getResults("SELECT * FROM joke where id in ($id1, $id2)");
echo "Список анекдотов\n";
foreach ($aneks as $an) {
    echo $an['joke_date'] . '  ' . $an['joke'] . "<br>\n";
}


//Удаляем добавленные анекдоты
$db->exec("DELETE FROM joke WHERE id in ($id1, $id2)");
echo "Все добавленные анекдоты удалены.\n";

// Получаем данные из бд


