# test_postgres

  - index.php
  Стартовая страница. Доступна мини-форма для ввода ссылки на XML-фид. После ввода обрабатывает данные по ссылке (используя AJAX) и редиректит на форму просмотра данных из БД.
  - parse_xml.php 
  Раздел обработки XML-фида и занесения данных в БД.
  - feed.php
  Форма просмотра данных из БД (используются Datatables).
