<?php
namespace App\Http\Helpers;

use Symfony\Component\HttpFoundation\Response;
use View;
use Session;
use DateTime;
use App\Models\Admin;

use Illuminate\Support\Facades\Cache;


class Common {

    function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
    }

    function d($var,$a=false)
    {
          echo "<pre>";
          print_r($var);
          echo "</pre>";
          if($a)exit;
    }

    public function content_read($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function one_time_message($class, $message)
    {

        if ($class == 'error') $class = 'danger';
        Session::flash('alert-class', 'alert-'.$class);
        Session::flash('message', $message);
    }

    public static function key_value($key, $value, $ar)
    {
        $ret = [];
        foreach($ar as $k => $v) {
            $ret[$v[$key]] = $v[$value];
        }
        return $ret;
    }

    public function current_action($route)
    {
        $current = explode('@', $route);
        View::share('current_action',$current[1]);
    }

   

    public static function meta($url, $field)
    {
        $metas = Meta::where('url', $url);

        if($metas->count())
            return $metas->first()->$field;
        else if($field == 'title')
            return 'Page Not Found';
        else
            return '';
    }

    public function vrCacheForget($key)
    {
        Cache::forget($key);
    }

    function backup_tables($host,$user,$pass,$name,$tables = '*')
    {
        try {
            $con = mysqli_connect($host,$user,$pass,$name);
        } catch (Exception $e) {

        }

        if (mysqli_connect_errno()) {
            $this->one_time_message('danger', "Failed to connect to MySQL: ".mysqli_connect_error());
            return 0;
        }

        if ($tables == '*') {
             $tables = array();
             $result = mysqli_query($con, 'SHOW TABLES');
            while ($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        $return = '';
        foreach($tables as $table) {
            $result = mysqli_query($con, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);


            $row2 = mysqli_fetch_row(mysqli_query($con, 'SHOW CREATE TABLE '.$table));
            $return.= "\n\n".str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $row2[1]).";\n\n";

            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j < ($num_fields-1)) { $return.= ','; }
                    }
                    $return.= ");\n";
                }
            }

            $return.="\n\n\n";
        }

        $backup_name = date('Y-m-d-His').'.sql';

        $handle = fopen(storage_path("db-backups").'/'.$backup_name,'w+');
        fwrite($handle,$return);
        fclose($handle);

        return $backup_name;
    }

    // public function add_notification($user_id, $message)
    // {
    //     $notification = new Notification;
    //     $notification->user_id = $user_id;
    //     $notification->message = $message;
    //     $notification->status = 'unread';
    //     $notification->save();
    // }


    
    public static function thousandsCurrencyFormat($num)
    {
      if($num < 1000) return $num;
      $x = round($num);
      $x_number_format = number_format($x);
      $x_array = explode(',', $x_number_format);
      $x_parts = array('k', 'm', 'b', 't');
      $x_count_parts = count($x_array) - 1;
      $x_display = $x;
      $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
      $x_display .= $x_parts[$x_count_parts - 1];
      return $x_display;
    }

    public function senitize($val)
    {
        $inp = trim($val);
        $inp = strip_tags($inp);
        $inp = htmlspecialchars($inp);
        return $inp;
    }

    public function pretty_url($str)
    {
        $url = $this->convert_to_url_friendly($str);
        $turl = $url;
        $i = 0;
        while(1){
            $i++;
            $cnt = Properties::where('slug', $turl)->count();
            if($cnt != 0)
                $turl = $url.'-'.$i;
            else break;
        }
        return $turl;
    }

    public function convert_to_url_friendly($str, $replace=array(), $delimiter='-')
    {
        if( !empty($replace) ) {
            $str = str_replace((array)$replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public function currency_rate($from, $to)
    {
        $from_rate = Currency::getAll()->firstWhere('code',$from)->rate;
        $to_rate   = Currency::getAll()->firstWhere('code',$to)->rate;

        $rate = round($from_rate / $to_rate, 6);
        if((int) $rate)
            return $rate;
        return round($from_rate / $to_rate, 6);
    }

    public function convert_currency($from = '', $to = '', $price=0)
    {
        $from = currentCurrency($from);
        $to = currentCurrency($to);
        $price       = str_replace(']','',$price);//For Php Version 7.2
        $base_amount = (float) $price / $from->rate;

        return round($base_amount * $to->rate, $to->rate > 1000 ? 0 : 2);
    }

    public function getCurrentCurrencySymbol() {
        return Session::get('currency') ? Currency::code_to_symbol(Session::get('currency'))
            : Currency::getAll()->firstWhere('default', 1)->symbol;
    }
    public function getCurrentCurrencyCode() {
        return Session::get('currency') ?? Currency::getAll()->firstWhere('default', 1)->code;
    }

    public function getCurrentCurrency() {
        return Session::get('currency') ? Currency::firstWhere('code', Session::get('currency'))
            : Currency::getAll()->firstWhere('default', 1);
    }

    

    public function get_days($startDate, $endDate)
    {
        $days []     = $startDate;
        $startDate   = is_numeric($startDate) ? $startDate : strtotime($startDate);
        $endDate     = is_numeric($endDate) ? $endDate : strtotime($endDate);

        $startDate   = gmdate("Y-m-d", $startDate);
        $endDate     = gmdate("Y-m-d", $endDate);
        $currentDate = $startDate;
        while($currentDate < $endDate) {
            $currentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($currentDate)));
            $days[]      = $currentDate;
        }
        return $days;
    }

    public function y_m_d_convert($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function host_penalty_check($penalty, $booking_amount,$currency_code)
    {
        $penalty_id = '';
        $penalty_amnt = '';

        $penalty_sum = 0;
        if ($penalty->count() > 0 ) {

            $host_amount = $booking_amount;

            foreach ($penalty as $pen) {

                $host_amount = $this->convert_currency($currency_code,$pen->currency_code,$host_amount);

                $remaining_penalty = $pen->remaining_penalty;

                if ($host_amount > $remaining_penalty) {

                    $host_amount = $host_amount - $remaining_penalty ;

                    $penalty = Penalty::find($pen->id);
                    $penalty->remaining_penalty     = 0;
                    $penalty->status                = "Completed";
                    $penalty->save();

                    $penalty_id .= $pen->id.',';
                    $penalty_amnt .= $remaining_penalty.',';
                    $penalty_sum += $remaining_penalty;
                } else {

                    $amount_reamining = $remaining_penalty - $host_amount;

                    $penalty = Penalty::find($pen->id);

                    $penalty->remaining_penalty  = $amount_reamining;

                    $penalty->save();

                    $penalty_id .= $pen->id.',';
                    $penalty_amnt .= $host_amount.',';
                    $penalty_sum += $host_amount;
                    $host_amount = 0;
                }

                $host_amount = $this->convert_currency($pen->currency_code,$currency_code,$host_amount);
            }

            $penalty_amnt   = rtrim($penalty_amnt, ',');
            $penalty_id     = rtrim($penalty_id, ',');
        } else {
            $host_amount = $booking_amount;

            $penalty_id  = 0;
            $penalty_amnt = '';
            $penalty_sum = 0;
        }

        $result['host_amount']     = $host_amount;
        $result['penalty_ids']     = $penalty_id;
        $result['penalty_total']   = $penalty_sum;
        $result['panalty_amounts'] = $penalty_amnt;

        return $result;
    }

    function randomCode($length=20)
    {
        $var_num = 3;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num_set = '0123456789';
        $low_ch_set = 'abcdefghijklmnopqrstuvwxyz';
        $high_ch_set = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';

        $randomString .= $num_set[rand(0, strlen($num_set) - 1)];
        $randomString .= $low_ch_set[rand(0, strlen($low_ch_set) - 1)];
        $randomString .= $high_ch_set[rand(0, strlen($high_ch_set) - 1)];

        for ($i = 0; $i < $length-$var_num; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        $randomString = str_shuffle($randomString);

        return $randomString;
    }

    public static function dateRange($startDate, $endDate, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates   = array();
        $current = strtotime($startDate);
        $endDate = strtotime($endDate);
        if ($current > $endDate) {
            return $dates;
        }
        while( $current <= $endDate ) {

            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    public function uploadSingleFile($file, $path = 'public/uploads/') {
        $tmp_name     = $file["tmp_name"];
        $name         = str_replace(' ', '_', $file["name"]);
        $ext          = pathinfo($name, PATHINFO_EXTENSION);
        $name         = explode('.', $name)[0] .time().'.'.$ext;
        try {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            move_uploaded_file($tmp_name, $path . $name);
            return $name;
        } catch (\Exception $e) {
            return false;
        }
    }
    function generatePIN($digits = 6){
    

    if($digits==4){
$pin='1234';
    }elseif($digits==6){
$pin='123456';
    }else{
        $pin='12345678';
    }
    // return $pin;
    $i = 0; //counter
    $pin = ''; //our default pin is blank.
    while($i < $digits){
        //generate a random number between 0 and 9.
        $pin .= mt_rand(0, 9);
        $i++;
    }
    return $pin;
}

// function costfortwo($code = null) {
   
//    $costfortwo=array('1' => '100 - 500',
//     '2' => '500 - 1000',
//      '3' => '1000 - 1500', 
//      '4' => '1500 - 2000',
//       '5' => '2000 - above');
//     return $costfortwo;
// }


  function data_output_datatable($columns, $data) {
    $out = array();
    for ($i = 0, $ien = count($data); $i < $ien; $i++) {
        $row = array();
        for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
            $column = $columns[$j];
            // Is there a formatter?
            if (isset($column['formatter'])) {
                $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
            } else {
                $row[$column['dt']] = $data[$i][$columns[$j]['db']];
            }
        }
        $out[] = $row;
    }
    return $out;
}

}
