<html>
<head>
    <script src="js/jquery-3.3.1.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/bootstrap.bundle.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
    <script src="js/script.js"></script>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap-grid.css">
    <link rel="stylesheet" href="css/bootstrap-reboot.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <link rel="stylesheet" href="css/style.css">
    <script>

        $(document).ready(function () {

            $.each(table_fields, function (key, value) {
                //alert(key);
                $('#tables').append('<table id="' + key + '" class="table table-bordered" style="width:100%">');
                $('#' + key).append('<thead>');
                $('#' + key).find('thead').append('<tr>');
                $.each(table_fields[key], function (k, val) {
                    $('#' + key).find('thead').find('tr').append('<th>' + val + '</th>');
                });
                $('#' + key).find('thead').append('</tr>');
                $('#' + key).append('</thead>');
                $('#' + key).append('</table>');

                //$('#' + key).hide();
                //$('#building_info').show();

            });

            var table_offers;
            var table_offer_location;
            var table_sales_agent;
            var table_offer_space;
            var table_offer_deals;
            var table_offer_flat;


            var table_building_info = $('#building_info').DataTable({
                // "processing": true,
                // "serverSide": true,
                "ajax": "processing.php",
                "columns": params['building'],
                "order": [[3, 'asc']],
                "language": {
                    "lengthMenu": "Показано _MENU_ записей на странице",
                    "zeroRecords": "Ничего не найдено",
                    "info": "Страниц _PAGE_ из _PAGES_",
                    "infoEmpty": "Нет данных",
                    "infoFiltered": "(Поазано _MAX_ записей)"
                }
            });
        });

    </script>
</head>
<body>
<div class="container" id="tables" style="margin: 10px;"></div>
</body>
</html>