<?php

namespace Utils;

use DateTime;
use DateTimeZone;

class Helper
{
  public static function getNowWithTimezone(): string
  {
    return (new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d H:i:s');
  }
}
