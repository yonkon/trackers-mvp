<?php


class Helpers
{

  const MYSQL_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';
  const SECONDS_IN_MINUTE = 60;
  const SECONDS_IN_HOUR = 3600;
  const SECONDS_IN_DAY = 86400;
  const SECONDS_IN_WEEK = 604800;
  const SECONDS_IN_MONTH = 2592000;
  const SECONDS_IN_YEAR = 31557600;

  public static function time2mysql_ts($time = null) {
    if(empty($time)) {
      $time = time();
    }
    return is_numeric($time) ? date( self::MYSQL_TIMESTAMP_FORMAT, $time) : $time;
  }

  public static function getToday()
  {
    return self::getMidnight(time());
  }

  public static function getTomorrow()
  {
    return self::getToday() + self::SECONDS_IN_DAY;
  }

  public static function getYesterday()
  {
    return self::getToday() - self::SECONDS_IN_DAY;
  }

  public static function getMidnight($time){
    return strtotime(date('Y-m-d', $time));
  }

  public static function formatTime($time, $format = null) {

    if(!empty($format)) {
      return date($format, $time);
    }
    if($time<self::SECONDS_IN_MINUTE) {
      return gmdate('s сек.', $time);
    }
    if($time<self::SECONDS_IN_DAY) {
      return gmdate('G ч. i мин.', $time);
    }
    if($time<self::SECONDS_IN_WEEK) {
      return gmdate('z дн. G ч.', $time);
    }
    return gmdate('z дн. G ч.', $time);
//    return gmdate('W нед. w дн. H ч.', $time);
//    return gmdate('W нед. d дн. H ч.', $time);
  }

  public static function dayDifference($before, $after, $getPositiveValue = true) {
    $delta = $getPositiveValue ? abs($after - $before) : ($after - $before);
    $delta = ceil($delta/self::SECONDS_IN_DAY);
    return $delta;
  }

  public static function getTimestamp($date) {
    if(empty($date)) {
      return time();
    }
    if(is_numeric($date)) {
      return $date;
    }
    return strtotime($date);
  }

  public static function getLocaleDate($date = null, $lang = 'ru') {
    //$format = 'd n';
    $ts = self::getTimestamp($date);
    $day = date('d', $ts);
    $month = intval(date('n', $ts));
    $monthTr = self::getMonthName($month-1, $lang);
    if(empty($monthTr)) {
      $monthTr = $month;
    }
    $res = "{$day} {$monthTr}";
    return $res;
  }

  public static function getMonthName($monthNum, $lang = 'ru') {
    $monthNames = array(
      'ru' => array(
        'Января',
        'Февраля',
        'Марта',
        'Апреля',
        'Мая',
        'Июня',
        'Июля',
        'Авгуса',
        'Сентября',
        'Октября',
        'Ноября',
        'Декабря'
      )
    );
    if(empty($lang)) {
      return $monthNames;
    }
    if(!is_numeric($monthNum)) {
      return $monthNames[$lang];
    }
    return $monthNames[$lang][$monthNum];
  }

  public static function getDaysInMonth($month = null, $year = null) {
    if(empty($year)) {
      $year = intval(date('Y'));
    }
    if(empty($month)) {
      $month = intval(date('m'));
    }
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
  }

}
