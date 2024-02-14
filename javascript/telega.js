document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('form');
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // предотвращаем отправку формы

        var username = document.getElementById('username').value;
        console.log(username); // выводим введенное значение в консоль логе

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/last/4', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = xhr.responseText;
                // Обработка ответа
            }
        };
        xhr.send();

    });
});