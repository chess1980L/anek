let pseudoUrl = []

var currentElement = 0
let oldCurrentElement = 0;
let resetP = false

let idJoke = [];
var users;
const button = document.querySelector('.btn-secondary');
button.style.display = 'none';
const user = document.getElementById("exit").dataset.username;

if ((user === 'admin') || (user === 'admin2')) { // для админа показ дополнительных опций в виде статистике


    const button = document.querySelector('.btn-success');
    button.removeAttribute('hidden');
    var crud = {
        action: 'r',
        user: '*'
    }
    DataFromServer(crud, function (response) {
        users = response
    });
}

//показ админ панели

function showAdminPanel() {

    var element = document.getElementById('myElement');
    element.parentNode.removeChild(element);

    document.querySelector('.admin').hidden = false;
    document.querySelectorAll('.btn-link').forEach(btn => btn.hidden = false);
}

//функция для отображения формы с флажками
function showFormCheck() {

    var tag = document.getElementById('tag');
    var radio = document.querySelector('.form-check');
    var crud = {
        action: 'r'
    };
    DataFromServer(crud, function (response) {
        var data = response;
        for (let i = 0; i < data.tags.length; i++) {
            let radioShow = radio.cloneNode(true);
            radioShow.hidden = false;
            radioShow.querySelector('label').innerHTML = data.tags[i].tag;
            radioShow.querySelector('input').value = data.tags[i].tag;
            tag.appendChild(radioShow);
        }
    });
}

// функция для установки чекбоксов
function setValueCheckboxes(tags) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    checkboxes.forEach(function (checkbox) {
        var tag = checkbox.value;
        var foundTag = tags.find(function (t) {
            return t.tag === tag;
        });

        if (foundTag) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    });
}

//  заполняет значениями поля ввода
function populateInputs(joke) {

    const idInput = document.getElementById('my-input');
    const jokeTextarea = document.getElementById('joke');

    idInput.value = joke.id;
    jokeTextarea.value = joke.joke;
}

// показ анекдота
function showJoke(data = null) {

    console.log("showJoke start");
    var crud = {
        action: 'r',
        //      currentUrl: currentUrl
    };
    if (data === null) {
        DataFromServer(crud, function (response) {
            data = response;

            console.log("before renderJokes");
            renderJokes(data);
        });
    } else {

        renderJokes(data);
    }
}

function renderJokes(data) {

    var jokeContainer = document.getElementById('dataJoke');
    jokeContainer.innerHTML = '';

    var tagsArray = [];

    console.log("RenderJokes start. length = " + data.jokes.length);
    for (let i = 0; i < data.jokes.length; i++) {
        var joke = data.jokes[i];

        var jokeElement = document.createElement('div');
        jokeElement.className = 'p-3 mb-2 bg-light text-dark';

        var rowElement = document.createElement('div');
        rowElement.className = 'row';

        var col1Element = document.createElement('div');
        col1Element.className = 'col-sm';
        col1Element.id = joke.id;

        var jokeTextElement = document.createElement('div');
        jokeTextElement.innerHTML = joke.joke + '<br>';

        var editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'btn btn-link';
        editButton.id = joke.id;
        editButton.innerText = 'редактировать (' + data.jokes[i]['id'] + ')';

        editButton.addEventListener('click', (function (jokeId) {
            return function () {
                var jokeDiv = document.getElementById(jokeId).parentNode;
                if (jokeDiv !== null && jokeDiv.tagName.toLowerCase() === 'div') {
                    var jokeText = jokeDiv.querySelector('div');
                    if (jokeText !== null) {
                        data.jokes[i]['joke'] = data.jokes[i]['joke'].replace(/<br\s*[\/]?>/gi, "\n");

                        console.log(data.jokes[i]['joke']);
                        idJoke.tags = data.jokes[i]['tags'];
                        idJoke.jokes = {};
                        idJoke.jokes[0] = data.jokes[i]
                        document.getElementById('joke').value = data.jokes[i]['joke'];
                        document.getElementById('my-input').value = jokeId;
                    }
                }
                // Устанавливаем значения чекбоксов в соответствии с данными
                var tags = data.jokes[i]['tags'];

                setValueCheckboxes(tags);
            };
        })(joke.id));
        var jokeDateElement = document.createElement('div');
        jokeDateElement.innerHTML = joke.joke_date;
        col1Element.appendChild(jokeTextElement);
        col1Element.appendChild(editButton);
        col1Element.appendChild(jokeDateElement);
        var col2Element = document.createElement('div');
        col2Element.className = 'col-sm';
        col2Element.id = joke.id;

        for (let j = 0; j < joke.tags.length; j++) {
            var tag = joke.tags[j];
            var tagButton = document.createElement('button');
            tagButton.type = 'button';
            tagButton.className = 'btn btn-outline-info btn-sm';
            tagButton.innerText = tag.tag;
            tagButton.setAttribute('id', tag.id);
            tagButton.addEventListener('click', (function (tagValue) {
                // currentElement = 0
                return function () {
                    currentElement = 0
                    showTagJoke(tagValue);
                };
            })(tag.tag));
            col2Element.appendChild(tagButton);

            rowElement.appendChild(col1Element);
            rowElement.appendChild(col2Element);
            jokeElement.appendChild(rowElement);
            jokeContainer.appendChild(jokeElement);
        }
    }
    createPagination(pseudoUrl.tag)
}

function showTagJoke(tag) {
    var crud = {
        action: 'r',
        jsTag: tag
    };

    pseudoUrl = {
        tag: tag
    };

    DataFromServer(crud, function (response) {
        data = response;

        renderJokes(data);
        //       createPagination(pseudoUrl.tag); // Передача актуального значения tag в функцию createPagination()
    });
}


function eventHandler() {
    let selectedTags

//проверка id
    var idInput = document.querySelector('#my-input');
    const jokeTextarea = document.querySelector('#joke');
    idInput.addEventListener('input', function () {
        var idValue = idInput.value.trim();

        if (idValue.length > 0 && !/^\d+$/.test(idValue)) {
            alert('Можно вводить только цифры!');
        }

        // Очистить поле ввода анекдота
        jokeTextarea.value = '';
    });

    // получаем все теги и добавляем обработчик клика на каждый из них
    const tags = document.querySelectorAll('.menu a');
    tags.forEach(tag => {
        tag.addEventListener('click', (event) => {
            event.preventDefault(); // отменяем переход по ссылке
            const tagName = tag.textContent;

            currentElement = 0
            showTagJoke(tagName); // вызываем функцию для получения анекдотов по тегу
        });
    });

    // нажатие кнопки изменить тег
    document.getElementById('updateTagOK').addEventListener("click", function () {
        selectedTags = document.querySelectorAll('.form-check input:checked');

        if (selectedTags.length === 0) {
            alert("Не выбран тег");
        } else {
            const value = selectedTags[0].value;
            const updateValue = document.getElementById('newTag').value.trim();

            if (updateValue === "") {
                alert("Тег пустой.");
            } else if (value === updateValue) {
                alert("Вы не поменяли тег");
            } else if (selectedTags.length > 1) {
                alert("Нельзя изменять несколько тегов одновременно");
            } else {
                const crud = {
                    action: 'u',
                    tag: {
                        value: value,
                        updateValue: updateValue
                    }
                };
                DataFromServer(crud, function (response) {
                    if (response === true) {

                        alert('Тег изменен');

                        // Добавляем код для удаления старых значений
                        var tag = document.getElementById('tag');

                        showFormCheck();

                        while (tag.firstChild) {
                            tag.removeChild(tag.firstChild);
                        }
                    }
                })
            }
        }
    });

    //клик посмотреть статистику
    const button = document.querySelector('.btn-success');
    button.addEventListener('click', showModal);

    // удаление тега
    const deleteTagBtn = document.getElementById('deleteTagBtn');
    deleteTagBtn.addEventListener('click', () => {
        selectedTags = document.querySelectorAll('.form-check input:checked');
        if (selectedTags.length === 0) {
            alert('Не выбран тег');
        } else if (selectedTags.length > 1) {
            alert('Нельзя удалить сразу несколько тегов');
        } else {
            const confirmed = confirm('Вы действительно хотите удалить тег?');
            if (confirmed) {
                const value = selectedTags[0].value;
                const crud = {
                    action: 'd',
                    tag: {
                        value: value
                    }
                };
                DataFromServer(crud, function (response) {
                    if (response === false) {
                        alert('Тег является единственным для анекдотов');
                    } else {
                        alert('Тег удален');

                        // Добавляем код для удаления старых значений
                        var tag = document.getElementById('tag');

                        showFormCheck();

                        while (tag.firstChild) {
                            tag.removeChild(tag.firstChild);
                        }
                    }
                });
            } else {// выполнить действия при отмене удаления тега
            }
        }
    });
    // обработчик события для кнопки "выход"
    const dangerButton = document.querySelector('.btn-danger');
    dangerButton.addEventListener('click', function () {
        document.cookie = "username=admin; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        location.reload();
    });
    // Проверяем наличие элемента dateRangePicker на странице
    const dateRangePicker = document.querySelector('#date-range-picker');
    if (dateRangePicker !== null) {
        // Инициализация flatpickr с опциями по умолчанию
        flatpickr(dateRangePicker, {
            mode: 'range',
            dateFormat: 'Y-m-d'
        });
    }
//обработчик событий для кнопки "Добавить тег"
    const okTag = document.getElementById('okNewTag');
    okTag.addEventListener("click", function () {
        // проверка выбранных тегов
        const selectedTags = document.querySelectorAll('.form-check input:checked');
        if (selectedTags.length === 0) {
            newTag(function () {
                //обновляем список тегов
                tag.innerHTML = '';
                showFormCheck();
            });
        } else {

            if (selectedTags.length > 1) {
                alert('Нельзя удалить сразу несколько тегов');
            } else {

                const value = selectedTags[0].value;
                document.getElementById('newTag').value = value;
            }
        }
    });

    //обработчик событий для кнопки "Добавить шутку"
    const okJoke = document.getElementById('okJoke');
    okJoke.addEventListener("click", function () {
        console.log("idInputValue = " + idInput.value);

        const jokeValue = document.getElementById("joke").value;
        const selectedTags = document.querySelectorAll('.form-check-input:checked'); // get selected tags
        const tagsLength = selectedTags.length;
        if (jokeValue === "") {
            alert("Вы не ввели анекдот");
        } else if (tagsLength === 0) {
            alert("Не выбран тег");
        } else if (idInput.value === '') {
            newJoke(jokeValue, selectedTags, tagsLength);

        } else {

            var currentId = idInput.value

            var tags = getCheckedCheckboxes()
            updateJoke(jokeValue, tags, currentId)
        }
    });

// обработчик событий для кнопки удалить анекдот
    //  const jokeTextarea = document.getElementById('joke');
    var deleteBtn = document.querySelector('#deleteJokeBtn');
    deleteBtn.addEventListener('click', function () {
        var idInput = document.querySelector('#my-input');
        var idValue = idInput.value.trim();

        if (idValue === '') {
            alert('Вы не ввели ID анекдота для удаления.');
            return;
        }
        if (isNaN(idValue)) {
            alert('Вы ввели неправильный ID анекдота. ID должен быть числом.');
            return;
        }
        if (jokeTextarea.value.trim() === '') {
            alert('Поле анекдота пустое! Проверьте id которое вы хотите удалить');
            return;
        }
        var confirmDelete = confirm('Вы действительно хотите удалить анекдот?');
        if (confirmDelete) {
            deleteJoke(idValue)
        }
    });

    // нажатие ок для вывода анекдота по id
    document.querySelector('#my-btn').addEventListener('click', function () {
        var input = document.querySelector('#my-input');
        var id = input.value.trim();

        if (!id) {
            alert('Ничего не введено!');
            return;
        }

        if (!/^\d+$/.test(id)) {
            alert('Можно вводить только цифры!');
            return;
        }

        var crudArray = {
            action: 'r',
            jokes: {
                id: id
            }
        };
        DataFromServer(crudArray, function (response) {
            if (response === true) {
                alert('Такого ID нет!');
                return;
            }

            idJoke = response
            jokes = response;
            var joke = Object.values(jokes); // Преобразуем объект joke в массив
            var tags = joke[1]
            var joke = joke[0][0]

            setValueCheckboxes(tags)
            populateInputs(joke);
        });
    });
}

function getCheckedCheckboxes() {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    var checkedCheckboxes = [];

    checkboxes.forEach(function (checkbox) {
        if (checkbox.checked) {
            checkedCheckboxes.push(checkbox.value);
        }
    });
    return checkedCheckboxes;
}

function DataFromServer(crudArray, callback) {

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            callback(response);
        }
    };
    xhr.send(JSON.stringify(crudArray));
}

function updateJoke(jokeValue, tags, currentId) {

    console.log("updateJoke START");

    const idJokeTags = idJoke.tags.map(tag => tag.tag);

    // Check if the tags match
    const tagsMatch = JSON.stringify(tags) === JSON.stringify(idJokeTags);

    // Check if the jokeValue matches
    const jokeMatch = jokeValue === idJoke.jokes[0].joke;

    // Output a messages indicating the match or mismatch
    if (tagsMatch && jokeMatch) {
        alert("Вы ни чего не поменяли");
    } else {

        var crudArray = {
            action: 'u',
            joke: {
                id: currentId,
                joke: jokeValue,
                user: user,
                tags: tags // сюда должен попасть массив тегов
            },
            currentElement: currentElement,

            chapter: pseudoUrl.tag

        };

        DataFromServer(crudArray, function (response) {
            data = response;
            renderJokes(data)
        })
    }
}

function newJoke(jokeValue, selectedTags, tagsLength) {
    const crudArray = {action: 'c', user: user, joke: {id: '', joke: jokeValue, tags: []}};

    for (let i = 0; i < tagsLength; i++) {
        crudArray.joke.tags.push(selectedTags[i].value);
    }
    DataFromServer(crudArray, function (response) {
        data = response;

        if (typeof data === 'string') {
            alert('Такой анекдот есть, проверьте id ' + data);
        } else if (Array.isArray(data) || typeof data === 'object') {

            document.getElementById('joke').value = '';

            currentElement = 0
            oldCurrentElement = 0;
            pseudoUrl.tag = ''
            showJoke(data); // Вызываем функцию showJoke() с полученными данными
        }
    });
}

function deleteJoke(idValue) {


    var crudArray = {
        action: 'd',
        joke: {
            id: idValue
        }
    };

    DataFromServer(crudArray, function (response) {
        // Обработка ответа от сервера
        if (response === true) {
            alert('Такого ID нет!');
            return;
        }
        var idInput = document.querySelector('#my-input');
        const jokeTextarea = document.querySelector('#joke');
        jokeTextarea.value = '';
        idInput.value = ''
        data = response

        currentElement = 0
        oldCurrentElement = 0;
        pseudoUrl.tag = ''

        showJoke(data)

    });
}

function newTag() {
    // функция для добавления нового тега
    var input = document.getElementById('newTag');
    // Получаем значение, введенное в поле input
    var value = input.value;
    // Проверяем, не пусто ли значение

    if (value.trim() === '') {
        // Если значение пусто, выводим сообщение
        alert('Вы не ввели тег');
        return;
    }

    // Создаем массив с ключами для передачи в функцию DataFromServer
    var crudArray = {
        action: 'r',
        tags: '*'
    };

    // Вызываем функцию DataFromServer для получения актуальных данных
    DataFromServer(crudArray, function (response) {
        var tags = response;
        console.log(tags);

        // Проверяем, есть ли уже такой тег
        var tagExists = false;
        for (let i = 0; i < tags.length; i++) {
            if (value.trim() === tags[i]) {
                tagExists = true;
                break;
            }
        }

        if (tagExists) {
            alert('Такой тег уже есть');
            return;
        }

        // Создаем объект crud для добавления нового тега
        var crud = {
            action: 'c',
            joke: {
                value: '',
                tags: []
            },
            tag: {
                value: value
            }
        };

        // Вызываем функцию DataFromServer для добавления нового тега
        DataFromServer(crud, function (response) {
            if (response.status === 'success') {
                // Добавляем новый тег на страницу
                var tag = document.getElementById('tag');
                var radio = document.querySelector('.form-check');
                var radioShow = radio.cloneNode(true);
                radioShow.hidden = false;
                radioShow.querySelector('label').innerHTML = value;
                radioShow.querySelector('input').value = value;
                tag.appendChild(radioShow);

                // Очищаем поле ввода
                input.value = '';
            } else {
                alert('Ошибка при добавлении тега: ' + response.message);
            }
        });
    });
}

function sendDates() {
    // Получение значений начальной и конечной даты
    const startDate = $("#start-date").datepicker("getDate");
    const endDate = $("#end-date").datepicker("getDate");

    // Получение выбранного пользователя
    const selectedUser = document.getElementById("users").value;


    // Вызов функции countJoke() и передача значений
    countJoke(startDate, endDate, selectedUser);
}

function countJoke(startDate, endDate, selectedUser) {

    if (!startDate) {
        alert("Введите начальную дату");
        return;
    }

    if (!endDate) {
        endDate = new Date(); // Текущая дата
    }

    if (startDate >= endDate) {
        alert("Некорректные даты");
        return;
    }

    var crudArray = {
        action: 'r',
        user: selectedUser,
        startDate: startDate,
        endDate: endDate
    };

    // Вызываем функцию DataFromServer для получения актуальных данных
    DataFromServer(crudArray, function (response) {
        var countJokeDate = response;

        // Формируем сообщение
        var message = "С даты " + startDate + " по дату " + endDate + ", пользователь " + selectedUser + ": " + countJokeDate;

        // Выводим сообщение в виде алерта
        alert(message);

        // Дальнейшая обработка значений...
    });
}

function showModal() {
    const modal = new bootstrap.Modal(document.getElementById('simple-modal'));
    modal.show();

    // Инициализация "bootstrap-datepicker" для элементов с классом "datepicker"
    $(".datepicker").datepicker();

    // Очистка выпадающего списка
    const usersSelect = document.getElementById('users');
    usersSelect.innerHTML = '';

    // Добавление пользователей в выпадающий список
    for (let i = 0; i < users.length; i++) {
        const userOption = document.createElement('option');
        userOption.value = users[i];
        userOption.textContent = users[i];
        usersSelect.appendChild(userOption);
    }
}

//показ псевдо url
function showPseudoUrl() {
    const link = document.querySelector('.link-warning');
    link.removeAttribute('hidden');
    if (currentElement !== 0) {

        link.textContent = (pseudoUrl.tag !== undefined && pseudoUrl.tag !== '') ? '/' + pseudoUrl.tag + '/' + currentElement : '/' + currentElement;
    } else {
        link.textContent = (pseudoUrl.tag !== undefined && pseudoUrl.tag !== '') ? '/' + pseudoUrl.tag : '/';
    }
}


window.addEventListener("hashchange", function () {
    createPagination(pseudoUrl.tag); // Передача актуального значения pseudoUrl.tag в функцию createPagination()
});

function createPagination(tag) {
    showPseudoUrl();

    var crud = {
        action: 'r',
        paginationJs: 'paginationJs',
        currentElement: currentElement,
        tag: tag
    };

    DataFromServer(crud, function (response) {
        var jsP = document.querySelector('.jsP');
        jsP.innerHTML = response;

        var paginationButtons = document.querySelectorAll('.jsP .pagination .page-link');

        paginationButtons.forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault(); // Отменяем действие по умолчанию при нажатии на кнопку
                event.stopPropagation(); // Предотвращаем дальнейшую обработку события
                someFunction(button.innerText); // Передаем значение кнопки в параметр функции
                return false; // Предотвращаем переход по ссылке
            });
        });
    });
}

function someFunction(buttonValue) {

    if (buttonValue === "next") {

        currentElement++;

    } else if (buttonValue === "Previous") {
        currentElement--;
    } else {
        currentElement = parseInt(buttonValue);
    }

    if (oldCurrentElement !== currentElement) {

        oldCurrentElement = currentElement;

        dataPag()
    }
}

function dataPag() {

    var crudArray = {
        action: 'r',
        jsTag: pseudoUrl.tag,
        offset: currentElement
    };


    DataFromServer(crudArray, function (response) {

        data = response;
        renderJokes(data);

    });
}

document.addEventListener('DOMContentLoaded', function () {
    console.log("DOMContentLoaded");

    showAdminPanel();
    // filCountTagJoke()
    console.log("DOMContentLoaded before showJoke");
    showJoke();
    showFormCheck();
    eventHandler();
    createPagination(pseudoUrl.tag)

});