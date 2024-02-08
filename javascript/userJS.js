var button = document.querySelector('.btn-danger');
button.style.display = 'none';

const buttonsMenu = document.querySelectorAll('.menu a');

const currentTag = decodeURIComponent(window.location.pathname.split('/').pop());

buttonsMenu.forEach(button => {
    if (decodeURIComponent(button.getAttribute('href')) === currentTag) {
        button.classList.add('active');
    }
});

if (window.location.pathname === '/') {
    localStorage.removeItem('activeButton'); // Удаляем сохраненную активную кнопку из локального хранилища
    buttonsMenu.forEach(button => {
        if (button.dataset.id === 'home') {
            button.classList.remove('active');
        }
    });
}

var okButton = document.querySelector('.modal-footer .btn-primary');
okButton.addEventListener('click', function () {
    var usernameInput = document.querySelector('#exampleModal input[type="text"]');
    var passwordInput = document.querySelector('#exampleModal input[type="password"]');

    var username = usernameInput.value;
    var password = passwordInput.value;

    if (username === '') {
        alert('Вы не ввели логин');
        return;
    } else if (password === '') {
        alert('Вы не ввели пароль');
        return;
    }

    var data = {
        validation: {
            username: username,
            password: password
        }
    };

    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data === true) {
                document.cookie = 'username=' + username;


                var currentUrl = window.location.href;
                var newUrl = currentUrl.split('/')[0] + '/';

                console.log(newUrl);


                window.location.href = newUrl;
            } else {
                alert('Вы ввели неправильный логин и/или пароль');
            }
        })
        .catch(error => console.error(error));
});