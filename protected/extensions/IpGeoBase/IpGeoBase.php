<?php
/**
 * @link https://github.com/himiklab/yii2-ipgeobase-component
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

//namespace himiklab\ipgeobase;

//use Yii;
//use yii\base\Component;
//use yii\base\Exception;

/**
 * Компонент для работы с базой IP-адресов сайта IpGeoBase.ru,
 * он Реализует поиск географического местонахождения IP-адреса,
 * выделенного RIPE локальным интернет-реестрам (LIR-ам).
 * Для Российской Федерации и Украины с точностью до города.
 *
 * @author HimikLab
 * @package himiklab\ipgeobase
 */
class IpGeoBase extends CApplicationComponent
{
    const XML_URL = 'http://ipgeobase.ru:7020/geo?ip=';
    const ARCHIVE_URL = 'http://ipgeobase.ru/files/db/Main/geo_files.zip';
    const ARCHIVE_IPS_FILE = 'cidr_optim.txt';
    const ARCHIVE_CITIES_FILE = 'cities.txt';

    const DB_IP_INSERTING_ROWS = 20000; // максимальный размер (строки) пакета для INSERT запроса
    const DB_IP_TABLE_NAME = 'geobase_ip';
    const DB_CITY_TABLE_NAME = 'geobase_city';
    const DB_REGION_TABLE_NAME = 'eobase_region';

    /** @var bool $useLocalDB Использовать ли локальную базу данных */
    public $useLocalDB = false;

    /**
     * Определение географического положеня по IP-адресу.
     * @param string $ip
     * @param bool $asArray
     * @return array|IpData ('ip', 'country', 'city', 'region', 'lat', 'lng') или false если ничего не найдено.
     */
    public function getLocation($ip=null, $asArray = true)
    {
      if(empty($ip)) {
        $ip = self::get_client_ip();
//        $ip = '46.118.51.83';
      }
        if ($this->useLocalDB) {
          $ipDataArray = $this->fromDB($ip);
          $ipDataArray['ip'] = $ip;
        } else {
          $ipDataArray = $this->fromSite($ip);
          $ipDataArray['ip'] = $ip;

        }

        if ($asArray) {
            return $ipDataArray;
        } else {
            return new IpData($ipDataArray);
        }
    }

    /**
     * Тест скорости получения данных из БД.
     * @param int $iterations
     * @return float IP/second
     */
    public function speedTest($iterations)
    {
        $ips = array();
        for ($i = 0; $i < $iterations; ++$i) {
            $ips[] = mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
        }

        $begin = microtime(true);
        foreach ($ips as $ip) {
            $this->getLocation($ip);
        }
        $time = microtime(true) - $begin;

        if ($time != 0 && $iterations != 0) {
            return $iterations / $time;
        } else {
            return 0.0;
        }
    }

    /**
     * Метод создаёт или обновляет локальную базу IP-адресов.
     * @throws Exception
     */
    public function updateDB($no_cache = false)
    {
      $createTables = "CREATE TABLE IF NOT EXISTS `" .self::DB_CITY_TABLE_NAME . "` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `region_id` int(6) unsigned NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `" .self::DB_IP_TABLE_NAME. "` (
  `ip_begin` int(10) unsigned NOT NULL,
  `ip_end` int(10) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `city_id` int(6) unsigned NOT NULL,
  KEY `ip_begin_ind` (`ip_begin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `" .self::DB_REGION_TABLE_NAME. "` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `geobase_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `value` text NOT NULL,
  `int_value` int(11) DEFAULT NULL,
  `type` enum('string','int','array','bool') NOT NULL DEFAULT 'string',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
      Yii::app()->db->createCommand($createTables)->execute();
      $last_update =  Yii::app()->db->createCommand("SELECT `int_value` FROM geobase_params where `name` = 'last_update' ")->queryRow();
      $now = time();
      if($no_cache ||
        empty($last_update) ||
        $last_update['int_value'] + 7*24*60*60 < $now) {
        if (($fileName = $this->getArchive()) == false) {
            throw new Exception('Ошибка загрузки архива.');
        }
        $zip = new ZipArchive;
        if ($zip->open($fileName) !== true) {
            @unlink($fileName);
            throw new Exception('Ошибка распаковки.');
        }

        $this->generateIpTable($zip);
        $this->generateCityTables($zip);
        $zip->close();
        @unlink($fileName);
        Yii::app()->db->createCommand("INSERT INTO geobase_params SET `name` = 'last_update',  `value` = '{$now}', `int_value` = {$now}, `type` = 'string' ON DUPLICATE KEY UPDATE `value` = '{$now}', `int_value` = {$now}")->execute();

      }
    }

    /**
     * @param string $ip
     * @return array
     */
    protected function fromSite($ip)
    {
        $xmlData = $this->getRemoteContent(self::XML_URL . urlencode($ip));
        $xmlElement = (new SimpleXMLElement($xmlData));
        $ipData = $xmlElement->ip;
        if (isset($ip->message)) {
            return array();
        }

        return array(
            'country' => (string)$ipData->country,
            'city' => isset($ipData->city) ? (string)$ipData->city : null,
            'region' => isset($ipData->region) ? (string)$ipData->region : null,
            'lat' => isset($ipData->lat) ? (string)$ipData->lat : null,
            'lng' => isset($ipData->lng) ? (string)$ipData->lng : null
    );
    }

    /**
     * @param string $ip
     * @return array
     */
    protected function fromDB($ip)
    {
      /**
       * @var CWebApplication $app
       */
      $app = Yii::app();
        $dbIpTableName = self::DB_IP_TABLE_NAME;
        $dbCityTableName = self::DB_CITY_TABLE_NAME;
        $dbRegionTableName = self::DB_REGION_TABLE_NAME;

        $result = $app->db->createCommand(
            "SELECT tIp.country_code AS country, tCity.name AS city,
                    tRegion.name AS region, tCity.latitude AS lat,
                    tCity.longitude AS lng
            FROM (SELECT * FROM {$dbIpTableName} WHERE ip_begin <= INET_ATON(:ip) ORDER BY ip_begin DESC LIMIT 1) AS tIp
            LEFT JOIN {$dbCityTableName} AS tCity ON tCity.id = tIp.city_id
            LEFT JOIN {$dbRegionTableName} AS tRegion ON tRegion.id = tCity.region_id
            WHERE INET_ATON(:ip) <= tIp.ip_end"
        )->bindValue(':ip', $ip)->queryRow();

        if ($result != false) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * Метод производит заполнение таблиц городов и регионов используя
     * данные из файла self::ARCHIVE_CITIES.
     * @param $zip ZipArchive
     * @throws Exception
     */
    protected function generateCityTables($zip)
    {
      /**
       * @var CWebApplication $app
       */
      $app = Yii::app();
        $citiesArray = explode("\n", $zip->getFromName(self::ARCHIVE_CITIES_FILE));
        array_pop($citiesArray); // пустая строка

        $cities = array();
        $uniqueRegions = array();
        $regionId = 1;
        foreach ($citiesArray as $city) {
            $row = explode("\t", $city);

            $regionName = iconv('WINDOWS-1251', 'UTF-8', $row[2]);
            if (!isset($uniqueRegions[$regionName])) {
                // новый регион
                $uniqueRegions[$regionName] = $regionId++;
            }

            $cities[$row[0]]['id'] = $row[0]; // id
            $cities[$row[0]]['name'] = iconv('WINDOWS-1251', 'UTF-8', $row[1]); // name
            $cities[$row[0]]['region_id'] = $uniqueRegions[$regionName]; // region_id
            $cities[$row[0]]['latitude'] = $row[4]; // latitude
            $cities[$row[0]]['longitude'] = $row[5]; // longitude
        }

        // города
        $app->db->createCommand()->truncateTable(self::DB_CITY_TABLE_NAME);
//        $app->db->createCommand()->truncateTable(self::DB_CITY_TABLE_NAME)->execute();
        $app->db->createCommand()->insertMultiple(
            self::DB_CITY_TABLE_NAME,
          array('id', 'name', 'region_id', 'latitude', 'longitude'),
            $cities
        );

        // регионы
        $regions = array();
        foreach ($uniqueRegions as $regionUniqName => $regionUniqId) {
            $regions[] = array('id' => $regionUniqId, 'name' => $regionUniqName);
        }
        $app->db->createCommand()->truncateTable(self::DB_REGION_TABLE_NAME);
//        $app->db->createCommand()->truncateTable(self::DB_REGION_TABLE_NAME)->execute();
        $app->db->createCommand()->insertMultiple(
            self::DB_REGION_TABLE_NAME,
          array('id', 'name'),
            $regions
        );
    }

    /**
     * Метод производит заполнение таблиц IP-адресов используя
     * данные из файла self::ARCHIVE_IPS.
     * @param $zip ZipArchive
     * @throws Exception
     */
    protected function generateIpTable($zip)
    {
      /**
       * @var CWebApplication $app
       */
      $app = Yii::app();
        $ipsArray = explode("\n", $zip->getFromName(self::ARCHIVE_IPS_FILE));
        array_pop($ipsArray); // пустая строка

        $i = 0;
        $values = '';
        $dbIpTableName = self::DB_IP_TABLE_NAME;
        $app->db->createCommand()->truncateTable($dbIpTableName);
//        $app->db->createCommand()->truncateTable($dbIpTableName)->execute();
        foreach ($ipsArray as $ip) {
            $row = explode("\t", $ip);
            $values .= '(' . (float)$row[0] .
                ',' . (float)$row[1] .
                ',' . $app->db->quoteValue($row[3]) .
                ',' . ($row[4] !== '-' ? (int)$row[4] : 0) .
                ')';
            ++$i;

            if ($i === self::DB_IP_INSERTING_ROWS) {
                $app->db->createCommand(
                    "INSERT INTO {$dbIpTableName} (ip_begin, ip_end, country_code, city_id)
                    VALUES {$values}"
                )->execute();
                $i = 0;
                $values = '';
                continue;
            }
            $values .= ',';
        }

        // оставшиеся строки не вошедшие в пакеты
        $app->db->createCommand(
            "INSERT INTO {$dbIpTableName} (ip_begin, ip_end, country_code, city_id)
            VALUES " . rtrim($values, ',')
        )->execute();
    }

    /**
     * Метод загружает архив с данными с адреса self::ARCHIVE_URL.
     * @return bool|string путь к загруженному файлу или false если файл загрузить не удалось.
     */
    protected function getArchive()
    {
      /**
       * @var CWebApplication $app
       */
      $app = Yii::app();
        $fileData = $this->getRemoteContent(self::ARCHIVE_URL);
        if ($fileData == false) {
            return false;
        }

        $fileName = $app->getRuntimePath() . DIRECTORY_SEPARATOR .
            substr(strrchr(self::ARCHIVE_URL, '/'), 1);
        if (file_put_contents($fileName, $fileData) != false) {
            return $fileName;
        }

        return false;
    }

  public static function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
      $ipaddress = '';
    return $ipaddress;
  }

    /**
     * Метод возвращает содержимое документа полученного по указанному url.
     * @param string $url
     * @return mixed|string
     */
    protected function getRemoteContent($url)
    {
        if (function_exists('curl_version')) {
            $curl = curl_init($url);
            curl_setopt_array($curl, array(
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true
            ));
            $data = curl_exec($curl);
            curl_close($curl);
            return $data;
        } else {
            return file_get_contents($url);
        }
    }
}

