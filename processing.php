<?php
require_once 'Database.php';
$worker = new Database();

$worker->db_connect();
$array = ['data' => [],
    "draw" => 1,
    "recordsTotal" => 57,
    "recordsFiltered" => 57
];
$tmp = [];


if (!isset($_GET['act'])) {

    $result = pg_query($worker->db_config, "SELECT * FROM building_info");
    while ($row = pg_fetch_assoc($result)) {
        $tmp['id'] = '<span class="fa fa-search fa-lg" onClick="getBuildingInfo('.$row['building_id'].')"></span>';;
        $tmp['title'] = $row['title'];
        $tmp['min_price'] = $row['min_price'];
        $tmp['max_price'] = $row['max_price'];
        $tmp['count_section'] = $row['count_section'];
        $tmp['count_flats'] = $row['count_flats'];
        array_push($array['data'], $tmp);
        $tmp = [];
    }
    echo json_encode($array);

} else {
    switch($_GET['act']){
        case 'offers':
            {
                $result = pg_query($worker->db_config, "SELECT id AS id, type, property_type, category, url, creation_date, last_update_date FROM offer WHERE building_id = " . $_GET['building_id']);
                while ($row = pg_fetch_assoc($result)) {
                    $tmp['get_info'] = '<span class="fa fa-search fa-lg" onClick="getOfferInfo('.$row['id'].')"></span>';
                    $tmp['type'] = $row['type'];
                    $tmp['property_type'] = $row['property_type'];
                    $tmp['category'] = $row['category'];
                    $tmp['link'] = '<a class="fa fa-link fa-lg" target="_blank" href="' . $row['url'] . '"></a>';
                    array_push($array['data'], $tmp);
                    $tmp = [];
                }
                echo json_encode($array);
                break;
            }
        case 'offer_location':
            {
                $result = pg_query($worker->db_config, "SELECT ol.country, ol.region, ol.district, ol.locality_name, ol.sub_locality_name, ol.address, ol.direction, ol.latitude, ol.longitude,
                       olm.name, olm.railway_station, olm.time_on_foot, olm.time_on_transport
                FROM offer_location ol
                JOIN offer_location_metro olm
                    ON olm.offer_location_id = ol.id
                WHERE ol.offer_id = " . $_GET['offer_id']);
                while ($row = pg_fetch_assoc($result)) {
                    $tmp['country'] = $row['country'];
                    $tmp['region'] = $row['region'];
                    $tmp['district'] = $row['district'];
                    $tmp['locality_name'] = $row['locality_name'];
                    $tmp['sub_locality_name'] = $row['sub_locality_name'];
                    $tmp['address'] = $row['address'];
                    $tmp['direction'] = $row['direction'];
                    $tmp['coordinates'] = '(' . $row['latitude'] . ';' . $row['longitude'] . ')';
                    $tmp['name'] = $row['name'];
                    $tmp['railway_station'] = $row['railway_station'];
                    $tmp['time_on_transport'] = $row['time_on_transport'];
                    $tmp['time_on_foot'] = $row['time_on_foot'];
                    array_push($array['data'], $tmp);
                    $tmp = [];
                }
                echo json_encode($array);
                break;
            }
        case 'sales_agent':
            {
                $result = pg_query($worker->db_config, "SELECT sa.name, sa.category, sa.phone, sa.organization, sa.email, sa.photo, sa.url
FROM sales_agent sa
       LEFT OUTER JOIN offer_sales o on sa.id = o.sales_agent_id
WHERE o.offer_id = " . $_GET['offer_id']);
                while ($row = pg_fetch_assoc($result)) {
                    $tmp['name'] = $row['name'];
                    $tmp['category'] = $row['category'];
                    $tmp['phone'] = '<a target="_blank" href="tel:' . $row['phone'] . '">'.$row['phone'].'</a>';
                    $tmp['organization'] = $row['organization'];
                    $tmp['email'] = '<a target="_blank" href="mailto:' . $row['email'] . '">'.$row['email'].'</a>';
                    $tmp['link'] = '<a class="fa fa-image fa-lg" target="_blank" href="' . $row['photo'] . '"></a>';
                    $tmp['link'] .= '<a class="fa fa-link fa-lg" target="_blank" href="' . $row['url'] . '"></a>';
                    array_push($array['data'], $tmp);
                    $tmp = [];
                }
                echo json_encode($array);
                break;
            }
        case 'offer_space':
            {
                $result = pg_query($worker->db_config, "SELECT
  CASE os.type WHEN 'room-space' THEN 'Площадь комнат' WHEN 'living-space' THEN 'Жилая площадь' WHEN 'kitchen-space' THEN 'Кухня' END AS type,
       os.value, os.unit, COALESCE(os.description,'-') AS description
FROM offer_space os
WHERE os.offer_id = " . $_GET['offer_id']);
                while ($row = pg_fetch_assoc($result)) {
                    $tmp['type'] = $row['type'];
                    $tmp['value'] = $row['value'] . ' ' . $row['unit'];
                    $tmp['description'] = $row['description'];
                    array_push($array['data'], $tmp);
                    $tmp = [];
                }
                echo json_encode($array);
                break;
            }
        case 'deals':
            {
                $result = pg_query($worker->db_config, "SELECT d.deal_status, d.value, COALESCE(d.currency,'RUR') AS currency, COALESCE(d.unit,'кв. м') AS unit
FROM deal d
WHERE d.offer_id = " . $_GET['offer_id']);
                while ($row = pg_fetch_assoc($result)) {
                    $tmp['deal_status'] = $row['deal_status'];
                    $tmp['value'] = $row['value'];
                    $tmp['currency'] = $row['currency'];
                    $tmp['unit'] = $row['unit'];
                    array_push($array['data'], $tmp);
                    $tmp = [];
                }
                echo json_encode($array);
                break;
            }
        case 'flat':
            {
                $result = pg_query($worker->db_config, "SELECT f.new_flat, f.floor, f.rooms, COALESCE(f.rooms_type,'раздельные') as rooms_type,coalesce(f.apartments,'нет') AS apartments, f.studio,
       coalesce(f.open_plan,'да') AS open_plan,coalesce(f.balcony,'-') AS balcony, f.windows_view, f.floor_covering, f.bathroom_unit
FROM flat f
WHERE f.offer_id = " . $_GET['offer_id']);
                while ($row = pg_fetch_assoc($result)) {
                    $tmp['new_flat'] = $row['new_flat'];
                    $tmp['floor'] = $row['floor'];
                    $tmp['rooms'] = $row['rooms'];
                    $tmp['rooms_type'] = $row['rooms_type'];
                    $tmp['apartments'] = $row['apartments'];
                    $tmp['studio'] = $row['studio'];
                    $tmp['open_plan'] = $row['open_plan'];
                    $tmp['balcony'] = $row['balcony'];
                    $tmp['windows_view'] = $row['windows_view'];
                    $tmp['floor_covering'] = $row['floor_covering'];
                    $tmp['bathroom_unit'] = $row['bathroom_unit'];
                    array_push($array['data'], $tmp);
                    $tmp = [];
                }
                echo json_encode($array);
                break;
            }
    }

}

$worker->db_close();
