<?php

// filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
function v_string($string) {
    $strings = trim($string);
    $string = stripslashes($strings);
    $string = stripcslashes($strings);
    $data = htmlspecialchars(addslashes($string));
    if($data == TRUE) {
        return $data;
    } else {
        return FALSE;
    }
}

function v_number($id) {
    if($id == 0 || $id == 1) {
        return $id;
    } else {
        if(filter_var(trim($id), FILTER_SANITIZE_NUMBER_INT) == TRUE) {
            return $id;
        } else {
            return FALSE;
        }
    }
}

function v_email($ema) {
    $eml = htmlspecialchars(addslashes(trim($ema)));
    $emai = filter_var($ema, FILTER_SANITIZE_EMAIL);
    $email = filter_var($emai, FILTER_VALIDATE_EMAIL);
    if($email == TRUE) {
        return $email;
    } else {
        return FALSE;
    }

}

function dateTime() {
    $createdOn = date('d-M-Y h:i:s a', time());
    return $createdOn;
}

function id() {
    $id = (int)$_GET['id'];
    if(isset($id)) {
        if($id !== 0) {
            return $id;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function pin() {
    $id = (int)$_GET['pin'];
    if(isset($id)) {
        if($id !== 0) {
            return $id;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function getEmail() {
    if(isset($_GET['email'])) {
        $email = urldecode($_GET['email']);
        return $email;
    } else {
        return FALSE;
    }
}

function getActNum() {
    if(isset($_GET['act_num'])) {
        $email = urldecode($_GET['act_num']);
        return $email;
    } else {
        return FALSE;
    }
}

function currency($currency) {
    if($currency == "USD") {
        return "$";
    } elseif($currency == "EUR") {
        return "€";
    } elseif($currency == "GBP") {
        return '£';
    } elseif($currency == "YEN") {
        return '¥';
    } else {
        return $currency;
    }
}

function userName($name) {
    $pattern = '/^[A-Za-z_-][A-Za-z0-9_-]{5,31}$/';
    if(preg_match($pattern, $name)) {
        return $name;
    } else {
        return FALSE;
    }
}

function validatePassword($password) {
    if(preg_match('/^(?=.*\d)(?=.*[@#\-,_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-,_$%^&+=§!\?]{8,20}$/', $password)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function v_passess($password) {
    if(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
        return "Your password is strong.";
    } else {
        return "Your password is not safe.";
    }
}

function v_passd($password) {
    if(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password)) {
        return "Your password is good.";
    } else {
        return "Your password is bad.";
    }
}

function truncate($string, $length, $stopanywhere = false) {
    //truncates a string to a certain char length, stopping on a word if not specified otherwise.
    if(strlen($string) > $length) {
        //limit hit!
        $string = substr($string, 0, ($length - 3));
        if($stopanywhere) {
            //stop anywhere
            $string .= '...';
        } else {
            //stop on a word.
            $string = substr($string, 0, strrpos($string, ' ')).'...';
        }
    }
    return $string;
}

function truncate2($string, $length, $stopanywhere = false) {
    //truncates a string to a certain char length, stopping on a word if not specified otherwise.
    if(strlen($string) > $length) {
        //limit hit!
        $string = substr($string, 0, ($length - 3));
        if($stopanywhere) {
            //stop anywhere
            $string .= '**';
        } else {
            //stop on a word.
            $string = substr($string, 0, strrpos($string, ' ')).'***';
        }
    }
    return $string;
}

function getIPAddress() {
    //whether ip is from the share internet  
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from the proxy  
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from the remote address  
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;

}

function escape($full) {
    $fullname = '';
    for($i = 0; $i < strlen($full); ++$i) {
        $char = $full[$i];
        $ord = ord($char);
        if($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
            $fullname .= $char;
        else
            $fullname .= '\\x'.dechex($ord);
    }
    return $fullname;
}

function currencySym($currency) {
    if($currency == "USD") {
        return "$";
    } elseif($currency == "EUR") {
        return "€";
    } elseif($currency == "GBP") {
        return '£';
    } elseif($currency == "YEN") {
        return '¥';
    } else {
        return $currency;
    }
}

function sumAmount($allData) {
    $sum = 0;

    foreach($allData as $data) {
        $sum = $sum + v_price($data['amount']);
    }
    return $sum;
}

function sumAllData($allData) {
    $sum = 0;

    foreach($allData as $data) {
        $sum = $sum + v_price($data['amount']);
    }
    return $sum;
}

function sumAllU($allData) {
    $sum = 0;

    foreach($allData as $data) {
        $sum = $sum + v_price($data['deposit']);
    }
    return $sum;
}

function dayCount($edit) {
    $date1 = date_create($edit);
    $date2 = date_create(date('Y-m-d'));
    $diff = date_diff($date1, $date2);
    return $diff->format("%r%a");
}

function newDayCount($edit) {
    $origin = new DateTime($edit);
    $target = new DateTime(date('d-m-Y'));

    $interval = $origin->diff($target);
    return $interval->format('%r%a');
}

function BTCprice($amount) {
    $url = 'https://bitpay.com/api/rates';
    $json = json_decode(file_get_contents($url));
    $dollar = $btc = 0;

    foreach($json as $obj) {
        if($obj->code == 'USD')
            $btc = $obj->rate;
    }

    // echo "1 bitcoin=\$" . $btc . "USD<br />";
    $dollar = 1 / $btc;
    return round($dollar * $amount, 8);
}

function BTCpriceCurrency($amount, $currency) {
    $url = 'https://bitpay.com/api/rates';
    $json = json_decode(file_get_contents($url));
    $dollar = $btc = 0;

    foreach($json as $obj) {
        if($obj->code == (string)$currency)
            $btc = $obj->rate;
    }

    // echo "1 bitcoin=\$" . $btc . "USD<br />";
    $dollar = 1 / $btc;
    return round($dollar * $amount, 8);
}

function getCoinPrice($currency, $coin) {
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        [
            CURLOPT_URL => 'https://bitpay.com/api/rates/'.$coin.'/'.$currency,
            CURLOPT_RETURNTRANSFER => true,
        ]
    );
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, JSON_OBJECT_AS_ARRAY);
    return $data['rate'];
}

function getCode($len) {
    if($len) {

        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = '23456789';

        $wallet = '';

        //append a character from each set - gets first 4 characters
        foreach($sets as $set) {
            $wallet .= $set[array_rand(str_split($set))];
        }

        //use all characters to fill up to $len
        while(strlen($wallet) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];

            //add a random char from the random set
            $wallet .= $randomSet[array_rand(str_split($randomSet))];
        }

        //shuffle the wallet string before returning!
        return str_shuffle($wallet);

    }
}

function getOS() {

    global $user_agent;

    $os_platform = "Unknown OS Platform";

    $os_array = array('/windows nt 10/i' => 'Windows 10', '/windows nt 6.3/i' => 'Windows 8.1', '/windows nt 6.2/i' => 'Windows 8', '/windows nt 6.1/i' => 'Windows 7', '/windows nt 6.0/i' => 'Windows Vista', '/windows nt 5.2/i' => 'Windows Server 2003/XP x64', '/windows nt 5.1/i' => 'Windows XP', '/windows xp/i' => 'Windows XP', '/windows nt 5.0/i' => 'Windows 2000', '/windows me/i' => 'Windows ME', '/win98/i' => 'Windows 98', '/win95/i' => 'Windows 95', '/win16/i' => 'Windows 3.11', '/macintosh|mac os x/i' => 'Mac OS X', '/mac_powerpc/i' => 'Mac OS 9', '/linux/i' => 'Linux', '/ubuntu/i' => 'Ubuntu', '/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad', '/android/i' => 'Android', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile');

    foreach($os_array as $regex => $value)
        if(preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function getBrowser() {

    global $user_agent;

    $browser = "Unknown Browser";

    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );

    foreach($browser_array as $regex => $value)
        if(preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}