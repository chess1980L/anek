<div id="simple-modal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    <label for="start-date">Начальная дата:</label>
                    <input type="text" class="form-control datepicker" id="start-date">
                </div>
                <div class="form-group">
                    <label for="end-date">Конечная дата:</label>
                    <input type="text" class="form-control datepicker" id="end-date">
                </div>
                <div class="form-group">
                    <label for="users">Пользователи:</label>
                    <select class="form-control" id="users">
                        <!-- Вставьте пользователей из массива users здесь -->
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" onclick="sendDates()">Отправить</button>
            </div>
        </div>
    </div>
</div>
