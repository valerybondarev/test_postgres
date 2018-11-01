
var params = {
    'building': [
        {
            'orderable': false,
            'data': 'id',
            'className': 'details-control building-info span-hidden',
            'defaultContent': ''
        },
        {
            'orderable': true,
            'data': 'title'
        },
        {
            "orderable": true,
            "data": 'min_price'
        },
        {
            'orderable': true,
            'data': 'max_price'
        },
        {
            'orderable': true,
            'data': 'count_section'
        },
        {
            'orderable': true,
            'data': 'count_flats'
        }
    ],
    'offer': [
        {
            "className": 'details-control offer-location span-hidden',
            "orderable": false,
            "data": 'get_info',
            "defaultContent": ''
        },
        {
            "className": 'details-control offer-location span-hidden',
            "orderable": false,
            "data": 'link',
            "defaultContent": ''
        },
        {
            "data": "type",
            "orderable": true
        },
        {
            "data": "property_type",
            "orderable": true
        },
        {
            "data": "category",
            "orderable": true
        }
    ],
    'offer_location': [
        {
            "data": "country",
            "orderable": true
        },
        {
            "data": "region",
            "orderable": true
        },
        {
            "data": "district",
            "orderable": true
        },
        {
            "data": "locality_name",
            "orderable": true
        },
        {
            "data": "sub_locality_name",
            "orderable": true
        },
        {
            "data": "address",
            "orderable": true
        },
        {
            "data": "direction",
            "orderable": true
        },
        {
            "data": "coordinates",
            "orderable": true
        },
        {
            "data": "name",
            "orderable": true
        },
        {
            "data": "railway_station",
            "orderable": true
        },
        {
            "data": "time_on_transport",
            "orderable": true
        },
        {
            "data": "time_on_foot",
            "orderable": true
        }
    ],
    'sales_agent': [
        {
            'orderable': true,
            'data': 'name',
        },
        {
            'orderable': true,
            'data': 'category'
        },
        {
            'orderable': true,
            'data': 'organization'
        },
        {
            "orderable": true,
            "data": 'phone'
        },
        {
            'orderable': true,
            'data': 'email'
        },
        {
            'orderable': true,
            'data': 'link'
        }
    ],
    'offer_space': [
        {
            'orderable': true,
            'data': 'type',
        },
        {
            'orderable': true,
            'data': 'value'
        },
        {
            'orderable': true,
            'data': 'description'
        }
    ],
    'deals': [
        {
            'orderable': true,
            'data': 'deal_status',
        },
        {
            'orderable': true,
            'data': 'value'
        },
        {
            'orderable': true,
            'data': 'currency'
        },
        {
            'orderable': true,
            'data': 'unit'
        }
    ],
    'flat': [
        {
            'orderable': true,
            'data': 'new_flat',
        },
        {
            'orderable': true,
            'data': 'floor'
        },
        {
            'orderable': true,
            'data': 'rooms'
        },
        {
            'orderable': true,
            'data': 'rooms_type'
        },
        {
            'orderable': true,
            'data': 'apartments'
        },
        {
            'orderable': true,
            'data': 'studio'
        },
        {
            'orderable': true,
            'data': 'open_plan'
        },
        {
            'orderable': true,
            'data': 'balcony'
        },
        {
            'orderable': true,
            'data': 'windows_view'
        },
        {
            'orderable': true,
            'data': 'floor_covering'
        },
        {
            'orderable': true,
            'data': 'bathroom_unit'
        }
    ],
};

var table_fields = {
    'building_info': [
        '',
        'Название',
        'Минимальная цена',
        'Максимальная цена',
        'Количество корпусов',
        'Количество квартир'
    ],
    'offers': [
        'ИНФО',
        'Объявление',
        'Тип объявления',
        'Тип недвижимости',
        'Тип помещения'
    ],
    'flat': [
        '!(Рухлядь)',
        'Этаж',
        'Комнат',
        'Тип комнат',
        'Апартаменты',
        'Студия',
        'Планировка',
        'Балкон',
        'Окна',
        'Лифт',
        'Санузел',
    ],
    'offer_space': [
        'Тип',
        'Размер',
        'Описание'
    ],
    'offer_location': [
        'Страна',
        'Регион',
        'Район',
        'Пригород',
        'Пригород',
        'Адрес',
        'Корпус',
        'Координаты',
        'Станция',
        'Название',
        'Транспорт',
        'Пешком'
    ],
    'deal': [
        'Условие',
        'Сумма',
        'Валюта',
        'Ед. изм.'
    ],
    'sales_agent': [
        'Имя',
        'Категория',
        'Организация',
        'Телефон',
        'Email',
        'Ссылки'
    ]
};

function getBuildingInfo(building_id) {
    $('#building_info tbody tr').removeClass('selected_row');
    $('span[onClick="getBuildingInfo(' + building_id + ')"]').parent('td').parent('tr').addClass('selected_row');

    if (typeof table_offers !== 'undefined') {
        table_offers.destroy();
    }
    table_offers = $('#offers').DataTable({
        "ajax": "processing.php?act=offers&building_id=" + building_id,
        "columns": params['offer'],
        "order": [[2, 'asc']],
        "language": {
            "lengthMenu": "Показано _MENU_ записей на странице",
            "zeroRecords": "Ничего не найдено",
            "info": "Страниц _PAGE_ из _PAGES_",
            "infoEmpty": "Нет данных",
            "infoFiltered": "(Поазано _MAX_ записей)"
        }
    });
}

function getOfferInfo(offer_id) {
    $('#offers tbody tr').removeClass('selected_row');
    $('span[onClick="getOfferInfo(' + offer_id + ')"]').parent('td').parent('tr').addClass('selected_row');

    if (typeof table_offer_location !== 'undefined') {
        table_offer_location.destroy();
    }
    table_offer_location = $('#offer_location').DataTable({
        "ajax": "processing.php?act=offer_location&offer_id=" + offer_id,
        "columns": params['offer_location'],
        "order": [[1, 'asc']],
        "language": {
            "lengthMenu": "Показано _MENU_ записей на странице",
            "zeroRecords": "Ничего не найдено",
            "info": "Страниц _PAGE_ из _PAGES_",
            "infoEmpty": "Нет данных",
            "infoFiltered": "(Поазано _MAX_ записей)"
        }
    });

    if (typeof table_sales_agent !== 'undefined') {
        table_sales_agent.destroy();
    }
    table_sales_agent = $('#sales_agent').DataTable({
        "ajax": "processing.php?act=sales_agent&offer_id=" + offer_id,
        "columns": params['sales_agent'],
        "order": [[1, 'asc']],
        "language": {
            "lengthMenu": "Показано _MENU_ записей на странице",
            "zeroRecords": "Ничего не найдено",
            "info": "Страниц _PAGE_ из _PAGES_",
            "infoEmpty": "Нет данных",
            "infoFiltered": "(Поазано _MAX_ записей)"
        }
    });

    if (typeof table_offer_space !== 'undefined') {
        table_offer_space.destroy();
    }
    table_offer_space = $('#offer_space').DataTable({
        "ajax": "processing.php?act=offer_space&offer_id=" + offer_id,
        "columns": params['offer_space'],
        "order": [[1, 'asc']],
        "language": {
            "lengthMenu": "Показано _MENU_ записей на странице",
            "zeroRecords": "Ничего не найдено",
            "info": "Страниц _PAGE_ из _PAGES_",
            "infoEmpty": "Нет данных",
            "infoFiltered": "(Поазано _MAX_ записей)"
        }
    });

    if (typeof table_offer_deals !== 'undefined') {
        table_offer_deals.destroy();
    }
    table_offer_deals = $('#deal').DataTable({
        "ajax": "processing.php?act=deals&offer_id=" + offer_id,
        "columns": params['deals'],
        "order": [[1, 'asc']],
        "language": {
            "lengthMenu": "Показано _MENU_ записей на странице",
            "zeroRecords": "Ничего не найдено",
            "info": "Страниц _PAGE_ из _PAGES_",
            "infoEmpty": "Нет данных",
            "infoFiltered": "(Поазано _MAX_ записей)"
        }
    });

    if (typeof table_offer_flat !== 'undefined') {
        table_offer_flat.destroy();
    }
    table_offer_flat = $('#flat').DataTable({
        "ajax": "processing.php?act=flat&offer_id=" + offer_id,
        "columns": params['flat'],
        "order": [[1, 'asc']],
        "language": {
            "lengthMenu": "Показано _MENU_ записей на странице",
            "zeroRecords": "Ничего не найдено",
            "info": "Страниц _PAGE_ из _PAGES_",
            "infoEmpty": "Нет данных",
            "infoFiltered": "(Поазано _MAX_ записей)"
        }
    });

}