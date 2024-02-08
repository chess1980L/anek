var buttonElement = document.getElementById('bottomButton');
var inputElement = document.getElementById('bottomInput');
var loginElement = document.getElementById('login');
var outputElement = document.getElementById('output');

buttonElement.addEventListener('click', function() {
    var inputValue = inputElement.value;

    if (document.getElementById('login').innerText !== '' || /^login\/.+/.test(inputValue)) {
        var login = document.getElementById('login').innerText;
        var url = 'http://anekd/api/' + inputValue;

        if (document.getElementById('login').innerText !== '') {
            var loginStr = String(login).replace(/^"(.*)"$/, '$1');
            url += '/' + loginStr;
        }

        fetch(url)
            .then(function(response) {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Произошла ошибка при выполнении запроса.');
                }
            })
            .then(function(data) {
                if (data === false) {
                    outputElement.innerText = 'Не существующий login';
                } else {
                    // Заменяем все вхождения \\r\\n на переводы строк
                    var formattedData = data.replace(/\\r\\n/g, '<br>');
                    // Разделяем шутки и абзацы
                    var jokes = formattedData.split('\n\n');
                    var formattedJokes = '';
                    // Обрабатываем каждую шутку или абзац
                    for (var i = 0; i < jokes.length; i++) {
                        formattedJokes += jokes[i] + '<br><br>'; // Добавляем перевод строки между шутками или абзацами
                    }
                    outputElement.innerHTML = 'Ответ сервера: <br>' + formattedJokes;
                    if (document.getElementById('login').innerText !== '') {
                        console.log(jokes);
                    } else {
                        loginElement.innerHTML = jokes;
                    }
                }
            })

            .catch(function(error) {
                outputElement.innerText = 'Ошибка: ' + error.message;
            });
    } else {
        outputElement.innerText = 'Не правильная команда';
    }
});