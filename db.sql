CREATE TYPE buildings_ready_quarter AS ENUM ('1', '2', '3', '4');
CREATE TYPE buildings_lift AS ENUM ('1', '0', 'true', 'false', '+', '-', 'да', 'нет');
CREATE TYPE buildings_ribbish AS ENUM ('1', '0', 'true', 'false', '+', '-', 'да', 'нет');
CREATE TYPE buildings_guarded AS ENUM ('1', '0', 'true', 'false', '+', '-', 'да', 'нет');
CREATE TYPE buildings_parking AS ENUM ('1', '0', 'true', 'false', '+', '-', 'да', 'нет');
CREATE TYPE buildings_elite AS ENUM ('1', '0', 'true', 'false', '+', '-', 'да', 'нет');
CREATE TYPE buildings_state AS ENUM ('built', 'hand-over', 'unfinished');
CREATE TABLE buildings (
  id                 SERIAL PRIMARY KEY,
  floors_total       INT                     NOT NULL,
  building_name      VARCHAR(255)            NOT NULL,
  yandex_building_id INT               DEFAULT NULL,
  yandex_house_id    INT               DEFAULT NULL,
  building_state     buildings_state   DEFAULT NULL,
  built_year         INT                     NOT NULL,
  ready_quarter      buildings_ready_quarter NOT NULL,
  building_phase     VARCHAR(100)      DEFAULT NULL,
  building_type      VARCHAR(100)      DEFAULT NULL,
  building_series    VARCHAR(10)       DEFAULT NULL,
  building_section   VARCHAR(25)       DEFAULT NUlL,
  celling_height     INT               DEFAULT NULL,
  lift               buildings_lift    DEFAULT NULL,
  ribbish_chute      buildings_ribbish DEFAULT NULL,
  guarded_building   buildings_guarded DEFAULT NULL,
  parking            buildings_parking DEFAULT NULL,
  is_elite           buildings_elite   DEFAULT NULL
);
--DROP TABLE buildings CASCADE;


CREATE TYPE offer_type AS ENUM ('продажа');
CREATE TYPE offer_property_type AS ENUM ('жилая');
CREATE TYPE offer_category AS ENUM ('дом', 'квартира', 'таунхаус', 'house', 'flat', 'townhouse');
CREATE TYPE offer_vas AS ENUM ('premium', 'raise', 'promotion');
CREATE TABLE offer (
  id               SERIAL PRIMARY KEY,
  internal_id      INT                 NOT NULL,
  building_id      SERIAL              NOT NULL,
  type             offer_type          NOT NULL,
  property_type    offer_property_type NOT NULL,
  category         offer_category      NOT NULL,
  url              VARCHAR(255) DEFAULT NULL,
  creation_date    timestamp           NOT NULL,
  last_update_date timestamp           NOT NULL,
  vas              offer_vas    DEFAULT NULL,
  vas_start_time   timestamp    DEFAULT NULL,
  FOREIGN KEY (building_id) REFERENCES buildings (id) ON DELETE CASCADE
);
--DROP TABLE offer CASCADE;

CREATE TABLE offer_location (
  id                SERIAL PRIMARY KEY,
  offer_id          SERIAL       NOT NULL,
  country           VARCHAR(100) NOT NULL,
  region            VARCHAR(100) DEFAULT NULL,
  district          VARCHAR(100) DEFAULT NULL,
  locality_name     VARCHAR(100) DEFAULT NULL,
  sub_locality_name VARCHAR(100) DEFAULT NULL,
  address           VARCHAR(255) DEFAULT NULL,
  direction         VARCHAR(255) DEFAULT NULL,
  latitude          VARCHAR(10)  DEFAULT NULL,
  longitude         VARCHAR(10)  DEFAULT NULL,
  FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE
);
--DROP TABLE offer_location;

CREATE TABLE offer_location_metro (
  id                SERIAL PRIMARY KEY,
  offer_location_id SERIAL       NOT NULL,
  name              VARCHAR(100) NULL,
  time_on_foot      INT          DEFAULT NULL,
  time_on_transport INT          DEFAULT NULL,
  railway_station   VARCHAR(100) DEFAULT NULL,
  FOREIGN KEY (offer_location_id) REFERENCES offer_location (id) ON DELETE CASCADE
);
--DROP TABLE offer_location_metro;

CREATE TYPE sales_agent_category AS ENUM ('агенство', 'застройщик', 'agency', 'developer');
CREATE TABLE sales_agent (
  id           SERIAL PRIMARY KEY,
  name         VARCHAR(100) DEFAULT NULL,
  category     sales_agent_category NOT NULL,
  phone        VARCHAR(100)         NOT NULL,
  organization VARCHAR(255) DEFAULT NULL,
  email        VARCHAR(100) DEFAULT NULL,
  photo        VARCHAR(255) DEFAULT NULL,
  url          VARCHAR(255) DEFAULT NULL
);
--DROP TABLE sales_agent;


CREATE TABLE offer_sales (
  id             SERIAL PRIMARY KEY,
  sales_agent_id SERIAL NOT NULL,
  offer_id       SERIAL NOT NULL,
  FOREIGN KEY (offer_id) REFERENCES offer (id),
  FOREIGN KEY (sales_agent_id) REFERENCES sales_agent (id)
);
--DROP TABLE offer_sales;


CREATE TYPE deal_status AS ENUM ('первичная продажа', 'продажа от застройщика', 'переуступка', 'reassignment', '214 ФЗ');
CREATE TYPE deal_currency AS ENUM ('RUB', 'RUR', 'EUR', 'USD');
CREATE TYPE deal_unit AS ENUM ('кв. м', 'sq. m');
CREATE TABLE deal (
  id          SERIAL PRIMARY KEY,
  offer_id    SERIAL      NOT NULL,
  deal_status deal_status NOT NULL,
  value       INT           DEFAULT NULL,
  currency    deal_currency DEFAULT NULL,
  unit        deal_unit     DEFAULT NULL,
  FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE
);
--DROP TABLE deal;

CREATE TYPE offer_image_tag AS ENUM ('plan', 'plan 3d');
CREATE TABLE offer_images (
  id       SERIAL PRIMARY KEY,
  offer_id SERIAL       NOT NULL,
  image    VARCHAR(255) NOT NULL,
  tag      offer_image_tag DEFAULT 'plan',
  FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE
);
--DROP TABLE offer_images;

CREATE TYPE offer_space_unit AS ENUM ('кв.м', 'sq.m');
--DROP TYPE  offer_space_unit;
CREATE TYPE offer_space_type AS ENUM ('living-space', 'room-space', 'kitchen-space');
CREATE TYPE offer_space_renovation AS ENUM ('чистовая отделка', 'под ключ', 'черновая отделка');
CREATE TABLE offer_space (
  id          SERIAL PRIMARY KEY,
  offer_id    SERIAL           NOT NULL,
  type        offer_space_type NOT NULL,
  value       FLOAT            DEFAULT NULL,
  unit        offer_space_unit DEFAULT NULL,
  description VARCHAR(255)     DEFAULT NULL,
  FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE
);
--DROP TABLE offer_space;

CREATE TYPE flat_new AS ENUM ('да', 'true', '1', '+');
CREATE TYPE flat_studio AS ENUM ('да', 'true', '1', '+');
CREATE TYPE flat_open_plan AS ENUM ('да', 'true', '1', '+');
CREATE TYPE flat_rooms_type AS ENUM ('смежные', 'раздельные');
CREATE TYPE flat_windows_view AS ENUM ('во двор', 'на улицу');
CREATE TYPE flat_floor_covering AS ENUM ('ковролин', 'ламинат', 'линолеум', 'паркет');
CREATE TYPE flat_apartments AS ENUM ('1', '0', 'true', 'false', '+', '-', 'да', 'нет');
CREATE TABLE flat (
  id             SERIAL PRIMARY KEY,
  offer_id       SERIAL   NOT NULL,
  new_flat       flat_new NOT NULL,
  floor          INT                 DEFAULT NULL,
  rooms          INT      NOT NULL,
  rooms_type     flat_rooms_type     DEFAULT NULL,
  apartments     flat_apartments     DEFAULT NULL,
  studio         flat_studio         DEFAULT NULL,
  open_plan      flat_open_plan      DEFAULT NULL,
  balcony        VARCHAR(100)        DEFAULT NULL,
  windows_view   flat_windows_view   DEFAULT NULL,
  floor_covering flat_floor_covering DEFAULT NULL,
  bathroom_unit  VARCHAR(100)        DEFAULT NULL,
  FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE
);
--DROP TABLE flat;

CREATE OR REPLACE FUNCTION getBuildingPrice(IN P_BUILDING_ID INT, IN P_TYPE BOOLEAN)
  RETURNS INT
AS $$
DECLARE
  P_RESULT INT := 0;
BEGIN
  SELECT CASE P_TYPE
           WHEN TRUE THEN MAX(d.value)
           ELSE MIN(d.value)
             END
      INTO P_RESULT
  FROM buildings b
         JOIN offer o ON o.building_id = b.id
         JOIN deal d ON d.offer_id = o.id;
  RETURN P_RESULT;
END;
$$
LANGUAGE plpgsql;

SELECT getBuildingPrice(1, false) AS MAX, getBuildingPrice(1, true) AS MIN;

CREATE OR REPLACE FUNCTION getRoomImages(IN P_OFFER_ID INT)
  RETURNS VARCHAR(1000)
AS $$
DECLARE
  P_RESULT VARCHAR(1000) := '';
  item     RECORD;
BEGIN
  FOR item IN (SELECT i.image AS url FROM offer_images i WHERE i.offer_id = P_OFFER_ID ORDER BY i.tag)
  LOOP
    P_RESULT := P_RESULT || item.url || ', ';
  END LOOP;
  RETURN SUBSTR(P_RESULT, 0, LENGTH(P_RESULT) - 1);
END;
$$
LANGUAGE plpgsql;

SELECT getRoomImages(3) AS Images;

CREATE OR REPLACE FUNCTION getCountSection(IN P_BUILDING_ID INT)
  RETURNS INT
AS $$
DECLARE
  P_RESULT INT := 0;
BEGIN
  SELECT COUNT(*)
  FROM (SELECT DISTINCT b.yandex_house_id
      INTO P_RESULT
        FROM buildings b
        WHERE b.yandex_building_id = (SELECT yandex_building_id FROM buildings WHERE id = P_BUILDING_ID)) p;
  RETURN P_RESULT;
END;
$$
LANGUAGE plpgsql;

SELECT getCountSection(1);

CREATE OR REPLACE FUNCTION getCountFlats(IN P_BUILDING_ID INT)
  RETURNS INT
AS $$
DECLARE P_RESULT INT := 0;
BEGIN
  SELECT COUNT(f.id)
      INTO P_RESULT
  FROM buildings b
         JOIN offer o on b.id = o.building_id
         JOIN flat f on o.id = f.offer_id
  WHERE b.id = P_BUILDING_ID;
  RETURN P_RESULT;
END;
$$
LANGUAGE plpgsql;

SELECT getCountFlats(1);


CREATE OR REPLACE VIEW building_info AS
SELECT row_number() OVER (ORDER BY b.id) AS i,
b.id AS Building_id,
       b.building_name AS Title,
       getBuildingPrice(b.id, false) AS Max_price,
       getBuildingPrice(b.id, true)  AS Min_price,
       getCountSection(b.id) AS Count_section,
       getCountFlats(b.id) AS Count_flats
FROM buildings b;
DROP VIEW building_info;



--- TESTS ---
--
-- SELECT * FROM buildings
-- SELECT * FROM offer
-- SELECT * FROM offer_location
-- SELECT * FROM offer_location_metro
-- SELECT * FROM offer_images
-- SELECT * FROM sales_agent
-- SELECT * FROM offer_sales
-- SELECT * FROM offer_space
-- SELECT * FROM deal
-- SELECT * FROM flat

--SELECT ol.country, ol.region, ol.district, ol.locality_name, ol.sub_locality_name, ol.address, ol.direction, ol.latitude, ol.longitude,
--       olm.name, olm.railway_station, olm.time_on_foot, olm.time_on_transport
--FROM offer_location ol
--JOIN offer_location_metro olm
--    ON olm.offer_location_id = ol.id
--WHERE ol.offer_id = 1

--SELECT sa.name, sa.category, sa.phone, sa.organization, sa.email, sa.photo, sa.url
--FROM sales_agent sa
--       LEFT OUTER JOIN offer_sales o on sa.id = o.sales_agent_id
--WHERE o.offer_id = 1

--SELECT
--  CASE os.type WHEN 'room-space' THEN 'Площадь комнат' WHEN 'living-space' THEN 'Жилая площадь' WHEN 'kitchen-space' THEN 'Кухня' END AS type,
--       os.value, os.unit, COALESCE(os.description,'-')
--FROM offer_space os
--WHERE os.offer_id = 1

--SELECT d.deal_status, d.value, COALESCE(d.currency,'RUR') AS currency, COALESCE(d.unit,'кв. м') AS unit
--FROM deal d
--WHERE d.offer_id = 1

--SELECT f.new_flat, f.floor, f.rooms, COALESCE(f.rooms_type,'раздельные') as rooms_type,coalesce(f.apartments,'нет') AS apartments, f.studio,
--       coalesce(f.open_plan,'да') AS open_paln,coalesce(f.balcony,'-') AS balcony, f.windows_view, f.floor_covering, f.bathroom_unit
--FROM flat f
--WHERE f.offer_id = 1