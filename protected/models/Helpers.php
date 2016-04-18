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
      return gmdate($format, $time);
    }
    if($time<self::SECONDS_IN_MINUTE) {
      return gmdate('s сек.', $time);
    }
    if($time<self::SECONDS_IN_DAY) {
      return gmdate('H ч. i мин.', $time);
    }
    if($time<self::SECONDS_IN_WEEK) {
      return gmdate('d дн. H ч.', $time);
    }
    return gmdate('w нед. d дн. H ч.', $time);
  }


}
