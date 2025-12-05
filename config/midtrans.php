<?php   
require_once __DIR__ . '/../vendor/autoload.php';

\Midtrans\Config::$serverKey = "MIDTRANS_SERVER_KEY";
\Midtrans\Config::$clientKey = "MIDTRANS_CLIENT_KEY";

\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized  = true;
\Midtrans\Config::$is3ds        = true;
