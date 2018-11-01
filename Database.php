<?php


class Database
{

    public $db_config;

    const DB_PARAMS = [
        'host' => 'localhost',
        'port' => 5432,
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => 'valera'
    ];

    function db_connect()
    {
        $connect_str = 'host=' . $this::DB_PARAMS['host'];
        $connect_str .= ' port=' . $this::DB_PARAMS['port'];
        $connect_str .= ' dbname=' . $this::DB_PARAMS['dbname'];
        $connect_str .= ' user=' . $this::DB_PARAMS['user'];
        $connect_str .= ' password=' . $this::DB_PARAMS['password'];
        try {
            $this->db_config = pg_connect($connect_str) or die('Не удалось соединиться: ' . pg_last_error());
        } catch (Exception $e) {
            print $e;
            die;
        }
    }

    function db_close()
    {
        pg_close($this->db_config);
    }

    function db_insert_data($table_name, $array = [], $returning = false, $params = [])
    {
        $query = 'INSERT INTO ' . $table_name;
        $str_keys = '(';
        $str_values = 'VALUES (';
        foreach ($array AS $key => $value) {
            if (is_null($params[str_replace('-', '_', $key)])) {
                $str_keys .= str_replace('-', '_', $key) . ', ';
                $str_values .= is_null($value[0]) ? 'null' . ', ' : '\'' . $value[0] . '\', ';
            } else {
                $str_keys .= str_replace('-', '_', $key) . ', ';
                $str_values .= '\'' . $params[str_replace('-', '_', $key)] . '\', ';
            }
        }
        $str_values = substr($str_values, 0, strlen($str_values) - 2);
        $str_keys = substr($str_keys, 0, strlen($str_keys) - 2);

        //echo '<br>';
        $query .= ' ' . $str_keys . ') ' . $str_values . ')' . (($returning) ? ' RETURNING id;' : ';');
        //echo '<br>';
        //echo $query;

        try {
            $result = pg_query($query) or die('Query failed: ' . pg_last_error());
            $row = pg_fetch_row($result);
            if ($returning) {
                return $row[0];
            } else {
                return true;
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    function setOfferDb($offer)
    {
        //$internal_id = $offer['internal-id'];
        //echo 'internal-id: ' . $offer['internal-id'] . PHP_EOL;
        //echo 'internal-id: ' . $internal_id . '<br>';


        $array_building = [];
        foreach (['building'] AS $category) {
            foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                $array_building[$p_value] = $offer->{$p_value};
            }
        }
        $rec = pg_select($this->db_config, 'buildings', ['yandex_building_id' => (string)$array_building['yandex-building-id']]);
        if (!$rec) {
            $new_building_id = $this->db_insert_data(
                'buildings',
                $array_building,
                true
            );
        } else {
            $new_building_id = $rec[0]['id'];
        }

        $array_offer = [];
        foreach (['offer'] AS $category) {
            foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                $array_offer[$p_value] = $offer->{$p_value};
            }
        }

        $rec = pg_select($this->db_config, 'offer', ['internal_id' => (string)$offer['internal-id']]);
        if (!$rec) {
            $new_offer_id = $this->db_insert_data(
                'offer',
                $array_offer,
                true,
                ['building_id' => $new_building_id, 'internal_id' => $offer['internal-id']]
            );
        } else {
            $new_offer_id = $rec[0]['id'];
        }

        $array_offer_location = [];
        foreach (['location'] AS $category) {
            foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                $array_offer_location[$p_value] = $offer->{'location'}->{$p_value};
            }
        }

        $rec = pg_select($this->db_config, 'offer_location', ['offer_id' => $new_offer_id]);
        if (!$rec) {
            $new_offer_location_id = $this->db_insert_data(
                'offer_location',
                $array_offer_location,
                true,
                ['offer_id' => $new_offer_id]
            );
        } else {
            $new_offer_location_id = $rec[0]['id'];
        }

        foreach (['metro'] AS $category) {
            foreach ($offer->{'location'}->{'metro'} AS $p_metro_name => $p_metro_value) {
                $array_offer_location_metro = [];
                foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                    $array_offer_location_metro[$p_value] = $offer->{'location'}->{'metro'}->{$p_value};
                }
                //echo $array_offer_location_metro['name'][0];
                $rec = pg_select($this->db_config, 'offer_location_metro', ['offer_location_id' => $new_offer_location_id, 'name' => (string)$array_offer_location_metro['name'][0]]);
                if (!$rec) {
                    $this->db_insert_data(
                        'offer_location_metro',
                        $array_offer_location_metro,
                        false,
                        ['offer_location_id' => $new_offer_location_id]
                    );
                }
            }
        }

        $array_sales_agent = [];
        foreach (['sales-agent'] AS $category) {
            foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                $array_sales_agent[$p_value] = $offer->{'sales-agent'}->{$p_value};
            }
        }

        $rec = pg_select($this->db_config, 'sales_agent', ['phone' => (string)$array_sales_agent['phone']]);
        if (!$rec) {
            $new_sales_agent_id = $this->db_insert_data(
                'sales_agent',
                $array_sales_agent,
                true
            );
        } else {
            $new_sales_agent_id = $rec[0]['id'];
        }

        $rec = pg_select($this->db_config, 'offer_sales', ['sales_agent_id' => (string)$new_sales_agent_id, 'offer_id' => (string)$new_offer_id]);
        if (!$rec) {
            $this->db_insert_data(
                'offer_sales',
                [
                    'offer_id' => $new_offer_id,
                    'sales_agent_id' => $new_sales_agent_id
                ],
                false
            );
        }


        $array_price = [
            'value' => '',
            'currency' => '',
            'unit' => ''
        ];

        foreach ($this::FIELDS['price'] AS $p_name => $p_value) {
            $array_price[$p_value] = $offer->{'price'}->{$p_value};
        }
        $array_price['offer-id'] = $new_offer_id;
        $array_price['deal-status'] = $offer->{'deal-status'}[0];

        $rec = pg_select($this->db_config, 'deal', ['offer_id' => (string)$new_offer_id, 'deal_status' => (string)$offer->{'deal-status'}[0]]);
        if (!$rec) {
            $query = 'INSERT INTO deal (offer_id, deal_status,value,currency,unit) 
            VALUES (\'' . $new_offer_id . '\', \'' . $offer->{'deal-status'}[0] . '\',
                \'' . $array_price['value'] . '\',
                \'' . $array_price['currency'] . '\',
                ' . (strlen($array_price['unit'] == 0) ? 'null' : '1') . ');';
            pg_query($query) or die('Query failed: ' . pg_last_error());
        }

        $array_offer_image = [];
        foreach (['image'] AS $category) {
            foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                $array_offer_image[$p_value] = $offer->{$p_value};
            }
        }
        $this->db_insert_data(
            'offer_images',
            $array_offer_image,
            false,
            ['offer_id' => $new_offer_id]
        );

        echo '<br>';

        foreach (['spaces'] AS $category) {
            foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                $array_offer_space = [
                    'type' => '',
                    'value' => '',
                    'unit' => '',
                    'description' => ''
                ];
                $array_offer_space['offer_id'] = $new_offer_id;
                $array_offer_space['type'] = [0 => $p_name];
                foreach ($p_value AS $key => $value) {
                    $array_offer_space[$value] = $offer->{$p_name}->{$value};
                }
                $rec = pg_select($this->db_config, 'offer_space', ['offer_id' => (string)$new_offer_id, 'type' => $p_name]);
                if (!$rec) {
                    $this->db_insert_data(
                        'offer_space',
                        $array_offer_space,
                        false,
                        ['offer_id' => $new_offer_id]
                    );
                }
            }
        }

        $array_offer_flat = [];
        foreach (['flat'] AS $category) {
            foreach ($this::FIELDS[$category] AS $p_name => $p_value) {
                $array_offer_flat[$p_value] = $offer->{$p_value};
            }
        }
        $rec = pg_select($this->db_config, 'flat', ['offer_id' => (string)$new_offer_id]);
        if (!$rec) {
            $this->db_insert_data(
                'flat',
                $array_offer_flat,
                false,
                ['offer_id' => $new_offer_id]
            );
        }
    }


    const FIELDS = [
        'offer' => [
            'internal-id',
            'building_id',
            'type',
            'property-type',
            'category',
            'url',
            'creation-date',
            'last-update-date',
            'vas',
            'vas-start-time'
        ],
        'building' => [
            'floors-total',
            'building-name',
            'yandex-building-id',
            'yandex-house-id',
            'building-state',
            'built-year',
            'ready-quarter',
            'building-phase',
            'building-type',
            'building-series',
            'building-section',
            'celling-height',
            'lift',
            'ribbish-chute',
            'guarded-building',
            'parking',
            'is_elite'
        ],
        'location' => [
            'offer_id',
            'country',
            'locality-name',
            'sub-locality-name',
            'address',
            'longitude',
            'latitude',
            'region',
            'district',
            'direction'
        ],
        'metro' => [
            'offer_location_id',
            'name',
            'time-on-foot',
            'time-on-transport'
        ],
        'sales-agent' => [
            'name',
            'email',
            'phone',
            'organization',
            'url',
            'category',
            'photo'
        ],
        'price' => [
            'offer-id',
            'deal-status',
            'value',
            'currency',
            'unit'
        ],
        'image' => [
            'offer_id',
            'image',
            'tag'
        ],
        'spaces' => [
            'living-space' =>
                [
                    'value',
                    'unit',
                    'description'
                ],
            'kitchen-space' =>
                [
                    'value',
                    'unit',
                    'description'
                ],
            'room-space' =>
                [
                    'value',
                    'unit',
                    'description'
                ]
        ],
        'flat' => [
            'offer_id',
            'new-flat',
            'floor',
            'rooms',
            'rooms-type',
            'apartments',
            'studio',
            'open-plan',
            'balcony',
            'windows-view',
            'floor-covering',
            'bathroom-unit'
        ]
    ];

}

