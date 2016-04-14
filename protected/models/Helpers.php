<?php


class Helpers
{

  const MYSQL_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

  public static function time2mysql_ts($time = null) {
    if(empty($time)) {
      $time = time();
    }
    return is_numeric($time) ? date( self::MYSQL_TIMESTAMP_FORMAT, $time) : $time;
  }


}
