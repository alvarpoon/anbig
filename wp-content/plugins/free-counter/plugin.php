<?php
    ob_start();
    /**
    * Plugin Name: Free-Counter!!!!
    * Plugin URI: www.free-counter.org
    * Description: <a href="http://www.free-counter.org/">Counter and statistics</a> plugin and Widget for WordPress.
    * Author: Free counter
    * Version: 1.0.5
    * Author URI: http://www.free-counter.org/
    */

    define("SERVER_URL", "www.free-counter.org");
    define("SERVER_URL_VISIT", "http://www.free-counter.org/");
    define("_PREFIX", "counter_free_");   
    define("IMG", "http://www.free-counter.org/images/");   
    define("PAGES_NEXT_PREV_COUNT", 3);
    class counter_free_plagin {

        static private $result = "";

        static private $data_counter = array(); 

        static private $file_hash = "";

        static function on_activate()
        {
            $data_post = array("action" => "create_new_counter", "site_url" => get_option('siteurl'));
            if ($result = self::sendToServer($data_post)) {  
                if (isset($result['status']) && $result['status'] == "ok" && 
                isset($result['default_image']) && isset($result['default_hidden']) && 
                isset($result['code']) && isset($result['images'])) {
                    add_option(_PREFIX . 'counter_id', $result['counter_id'], '', true);
                    add_option(_PREFIX . 'default_image', $result['default_image'], '', true);
                    add_option(_PREFIX . 'default_hidden', $result['default_hidden'], '', true);
                    add_option(_PREFIX . 'counter_code', $result['code']);
                    add_option(_PREFIX . 'image_color', $result['image_color']);
                    add_option(_PREFIX . 'images', $result['images'], '', true);
                    add_option(_PREFIX . 'email', $result['email'], '', true);
                    add_option(_PREFIX . 'password', $result['password'], '', true);
                    self::$data_counter['counter_id'] = $result['counter_id'];
                    self::$data_counter['images'] = $result['images'];
                }
            } 
        }

        static public function sendToServer($postdata = array())
        {

            if (count($postdata) > 0) {
                if (isset(self::$data_counter['counter_id'])) {
                    $postdata['counter_id'] = self::$data_counter['counter_id'];
                } 
                if ($counter_id = get_option(_PREFIX . 'counter_id')) {
                    $postdata['counter_id'] = $counter_id;
                }
                $postdata = http_build_query($postdata, '', '&');

                $length = strlen($postdata);           

                if (function_exists("fsockopen")) {
                    $out = "POST /Api.php HTTP/1.1\r\n";
                    $out.= "HOST: " . SERVER_URL . "\r\n";
                    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
                    $out.= "Content-Length: ".$length."\r\n";
                    $out.= "Connection:Close\r\n\r\n";
                    $out.= $postdata."\r\n\r\n";
                    try {
                        $errno='';
                        $errstr = '';
                        $socket = @fsockopen(SERVER_URL, 80, $errno, $errstr, 30);
                        if($socket){
                            if(!fwrite($socket, $out)){
                                throw new Exception("unable to write");
                            } else {
                                while ($in = @fgets ($socket, 1024)){
                                    self::$result .= $in;
                                } 

                            }
                            self::$result = explode("\r\n\r\n", self::$result);
                            preg_match("/(a:|s:|i:).*/", self::$result[1], $res);
                            if (isset($res[0])) {
                                return unserialize($res[0]);
                            }
                            throw new Exception("error in data");
                        } else {
                            throw new Exception("unable to create socket");
                        }
                        fclose($socket);
                    } catch(exception $e) {
                        return false;
                    }
                } elseif (function_exists("curl_init") && function_exists("curl_setopt") && function_exists("curl_exec") && function_exists("curl_close")) {
                    $curl = curl_init(SERVER_URL_VISIT . "/Api.php");
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
                    self::$result = curl_exec($curl);
                    curl_close($curl);
                    preg_match("/(a:|s:|i:).*/", self::$result, $res);
                    if (isset($res[0])) {
                        return unserialize($res[0]);
                    }
                } 
            }

        }
        private static function getWeekDates() 
        {
            $week_day = date("w");
            $week_day = ($week_day == 0) ? 7 : $week_day;
            $day_now = date("j");
            $end_week_day = $day_now + (7 - $week_day);
            $start_week_day = $day_now - ($week_day - 1);
            $day_in_month = date("t");
            $day_in_month_pref = date("t", mktime(0,0,0, date("n")-1));
            $now_month = date("n");
            $change_month = "";
            if($end_week_day > $day_in_month) {
                $day_next_month = $end_week_day - $day_in_month  ;
                $end_week_day = $day_next_month;
                $change_month = $now_month + 1;
            } 
            if($start_week_day < 1) {
                $day_next_month = $day_in_month_pref + $start_week_day; // т.к. число  $start_week_day отрецательное
                $start_week_day = $day_next_month; 
                $change_month = $now_month - 1;
            }
            $year_change  = $year = 0;

            if ($change_month != "" && $change_month < 1) {   
                $year_change = date("Y") - 1;   
                $change_month = 12;
            } elseif ($change_month != "" && $change_month > 12) {
                $year_change = date("Y") + 1;
                $change_month = 1;
            } 

            $year = date("Y");

            if ($change_month != "" && $change_month > $now_month) {       
                if (!empty($year_change) && $year_change > $year) {
                    $dates_start_week = mktime(0,0,0, $now_month, $start_week_day, $year);
                    $date_end_week = mktime(23, 59,59, $change_month, $end_week_day, $year_change);
                } elseif (!empty($year_change) && $year_change < $year) {
                    $dates_start_week = mktime(0,0,0, $change_month , $start_week_day, $year_change);
                    $date_end_week = mktime(23, 59,59, $now_month, $end_week_day, $year);
                } else {                            
                    $dates_start_week = mktime(0,0,0, $now_month, $start_week_day, $year);
                    $date_end_week = mktime(23, 59,59, $change_month, $end_week_day, $year);
                }

            } elseif ($change_month != "" && $change_month < $now_month) { 
                if (!empty($year_change) && $year_change > $year) {
                    $dates_start_week = mktime(0,0,0, $now_month, $start_week_day, $year);
                    $date_end_week = mktime(23, 59, 59, $change_month, $end_week_day, $year_change);
                } elseif (!empty($year_change) && $year_change < $year) {
                    $dates_start_week = mktime(0,0,0, $change_month, $start_week_day, $year_change);
                    $date_end_week = mktime(23, 59, 59, $now_month, $end_week_day, $year);
                } else {
                    $dates_start_week = mktime(0,0,0, $change_month, $start_week_day, $year);
                    $date_end_week = mktime(23, 59, 59, $now_month, $end_week_day, $year);
                }
            } else {
                if (!empty($year_change) && $year_change > $year) {
                    $dates_start_week = mktime(0,0,0, $now_month, $start_week_day, $year);
                    $date_end_week = mktime(23, 59,59, $now_month, $end_week_day, $year);
                } elseif (!empty($year_change) && $year_change < $year) {
                    $dates_start_week = mktime(0,0,0, $now_month, $start_week_day, $year);
                    $date_end_week = mktime(23, 59,59, $now_month, $end_week_day, $year);
                }
                $dates_start_week = mktime(0,0,0, $now_month, $start_week_day, $year);
                $date_end_week = mktime(23, 59,59, $now_month, $end_week_day, $year);
            }
            $array_dates['start_week_time'] = $dates_start_week;
            $array_dates['end_week_time'] = $date_end_week;
            return $array_dates;
        }

        static function on_deactivate()
        {
            delete_option(_PREFIX . 'counter_id');
            delete_option(_PREFIX . 'images');
            delete_option(_PREFIX . 'default_image');
            delete_option(_PREFIX . 'default_hidden');
            delete_option(_PREFIX . 'counter_code');
            delete_option(_PREFIX . 'image_color');
            delete_option(_PREFIX . 'email');
            delete_option(_PREFIX . 'password');
        }

        static function draw_menu()
        {
            global $menu;

            //tries to detect an available menu position. Usually at position 3 (right after the Dashboard menu)
            //there is an opening, but if not, it will find the next available position.
            $counterize_menu_position = 26;

            add_menu_page(
            'Statistic for Counter', 
            'Counter Statistic', 
            'manage_options', 
            'counter_free_plagin', 
            array('counter_free_plagin', 'stat_view'),
            plugins_url('/free-counter.org_icon.png', __FILE__),
            $counterize_menu_position
            );
        }
        static function save_account()
        {
            if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_repeat'])) {

                $image_default = get_option(_PREFIX . 'default_image');
                $image_hidden = get_option(_PREFIX . 'default_hidden');
                $image_color = get_option(_PREFIX . 'image_color');

                $email = $_POST['email'];
                if ($_POST['password'] == $_POST['password_repeat']) {
                    $password = trim($_POST['password']);
                } 
                $act = false;
                if (empty($email)) {
                    $act = true;
                } elseif(isset($password) && empty($password)) {
                    $act = true;
                } elseif(!preg_match("/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i", $email)) {
                    $act = true;
                }
                if (!$act) {
                    $data_post_default = array(
                    "action" => "get_count_image_on_setting", 
                    "image" => $image_default, 
                    "hidden" => $image_hidden, 
                    "image_color" => $image_color,
                    "email" => $email,
                    "password" => $password);
                    $result_default_image_setting = counter_free_plagin::sendToServer($data_post_default);
                    if (isset($result_default_image_setting['code'])) {
                        add_option(_PREFIX . 'email', $result_default_image_setting['email']);
                        update_option(_PREFIX . 'email', $result_default_image_setting['email']); 
                        add_option(_PREFIX . 'password', $result_default_image_setting['password']);
                        update_option(_PREFIX . 'password', $result_default_image_setting['password']);
                    }
                }
                header("location: " . admin_url( 'admin.php?page=counter_free_plagin' ));
            }
        }
        static function stat_view() 
        {

            $data = array('action' => 'get_statistic');
            // hash
            self::$file_hash = plugin_dir_path( __FILE__ ) . "/temp/data";
            $data_hash = self::getHash();
            $data['count_hash'] = count($data_hash);
            $data['time_hash'] = filemtime(self::$file_hash);
            if ($data['count_hash'] == 0) {
                $data['first'] = true;
            }          
            //end hash  
            $email = get_option(_PREFIX . 'email');
            $password = get_option(_PREFIX . 'password'); 
            $month_now = date("n");
            $year_now = date("Y");
            $start_today = date("j");
            $id_counter = get_option(_PREFIX . 'counter_id');
            $image_default = get_option(_PREFIX . 'default_image');
            $image_hidden = get_option(_PREFIX . 'default_hidden');
            $date_week = self::getWeekDates();
            if ($res = self::sendToServer($data)) {   

                if (isset($res["stat"]['all']['months'])) {
                    $browser = isset($res["stat"]['all']['details']['browser']) ? self::sort_data($res["stat"]['all']['details']['browser']) : array();
                    $os = isset($res["stat"]['all']['details']['os']) ? self::sort_data($res["stat"]['all']['details']['os']) : array();
                    $data_screen = isset($res["stat"]['all']['details']['data_screen']) ? self::sort_data($res["stat"]['all']['details']['data_screen']) : array();
                    $data_bit = isset($res["stat"]['all']['details']['data_bit']) ? self::sort_data($res["stat"]['all']['details']['data_bit']) : array();
                    $data_countries = isset($res["stat"]['all']['details']['countries']) ? self::sort_data($res["stat"]['all']['details']['countries']) : array(); 
                    $data_city = isset($res["stat"]['all']['details']['cities']) ? self::sort_data($res["stat"]['all']['details']['cities']) : array();
                }

                if (isset($res['stat']['record']['data']) && isset($res['stat']['count_data'])) {   
                    $data_hash = chackArray($res['stat']['record']['data'], $data_hash, $res['stat']['record']['visitors_count']);
                    $data['count_hash'] = count($data_hash);
                    if ($data['count_hash'] > $res['stat']['count_data']) {
                        $temp = array_chunk($data_hash, $res['stat']['count_data']);
                        $data_hash = $temp[0];
                    }
                    self::saveHash($data_hash);
                }
            }
            $record = self::getHash();
            $stat_chart_day = (isset($res["stat"]['all']["all_month"]) ? $res["stat"]['all']["all_month"] : array());
            $stat_chart_month = (isset($res["stat"]['all']["months"]) ? $res["stat"]['all']["months"] : array());
            $stat_chart_week = (isset($res["stat"]['weel_days_stat']) ? $res["stat"]['weel_days_stat'] : array());
            ob_start();
            include(plugin_dir_path( __FILE__ ) . "template/statistic.php");
            $form = ob_get_clean();
            echo $form;
        }
        function exportToCsv() 
        {

            $filename = "export.csv";
            self::$file_hash = plugin_dir_path( __FILE__ ) . "/temp/data";
            $data_hash = self::getHash(); 

            $data_keys = array(
            'date', 'ip', 'country', 'city',
            'system', 'browser', 'screen resolution' ,
            'screen color', 'javascript', 
            'query', 'referrer',
            );
            $data = array();
            $data[0] = $data_keys;
            $i = 1;
            foreach($data_hash as $k => $val) {
                $data[$i][1] = $val['4'];
                $data[$i][2] = $val['3']['ip'];
                $data[$i][3] = $val['3']['country'];
                $data[$i][4] = $val['3']['city'];
                $data[$i][5] = $val['6']['name'];
                $data[$i][6] = $val['5']['name'];
                $data[$i][7] = $val['1'];
                $data[$i][8] = $val['2'] . " Bit";
                $data[$i][9] = $val['0'];
                $data[$i][10] = $val['8'];
                $data[$i][11] = $val['9'];
                $i++;
                if ($i == 200) {
                    break;
                }
            }
            self::download_send_headers($filename);
            echo self::array2csv($data);
            exit;
        }
        function  download_send_headers($filename)
        {
            // disable caching
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");
            // force download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");
        }
        function array2csv(array &$array)
        {
            if (count($array) == 0) {
                return null;
            }
            ob_start();
            $df = fopen("php://output", 'w');
            foreach ($array as $row) {
                fputcsv($df, $row);
            }
            fclose($df);
            return ob_get_clean();
        }


        function saveHash($data = array())
        {
            file_put_contents(self::$file_hash, serialize($data));
        }

        function getHash() 
        {
            $data = array();
            if (file_exists(self::$file_hash)) {
                $contant = file_get_contents(self::$file_hash);
                $data = unserialize($contant);
            } else {
                self::saveHash();
            }
            return $data;
        }

        function get_pages($page, $pages)
        {
            echo '<div id="page_item">Page Item: <select  onchange="setCountShowVisitorsOnDays(this)">';
            $count_item_in_pages = array(15, 30, 50);
            for($i=0, $n = count($count_item_in_pages); $i < $n; $i++) {
                $sel = "";
                if (isset($_SESSION['selected']) && $_SESSION['selected'] == $count_item_in_pages[$i]) {
                    $sel = 'selected = "selected"'; 
                }
                echo '<option value="' . $count_item_in_pages[$i] . '" ' . $sel . ' >' . $count_item_in_pages[$i] . '</option>';
            }
            echo '</select></div>&nbsp;&nbsp;';
            if ($page != 1) {
                $prev = $page - 1;
                $url_prev = plugins_url("admin.php?page=counter_free_plagin&p={$prev}#header-visitors-days", __FILE__);
                echo "<a href=\"$url_prev\">Prev Page</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

            }
            $prev_pages_visible = $page - PAGES_NEXT_PREV_COUNT;
            if ($prev_pages_visible > 0) {
                $url_first = admin_url("admin.php?page=counter_free_plagin&p=1#header-visitors-days", __FILE__);
                echo "<a href=\"$url_first\">1</a>&nbsp;&nbsp;...&nbsp;&nbsp;";
            }
            if (($page - PAGES_NEXT_PREV_COUNT) > 0) {
                $prev_pages_visible = $page - PAGES_NEXT_PREV_COUNT;
            } elseif (($page - PAGES_NEXT_PREV_COUNT) == 0) {
                $prev_pages_visible = 1 ;
            } else {
                $prev_pages_visible = PAGES_NEXT_PREV_COUNT - $page;
            }
            if ($prev_pages_visible >= 1) {
                for($i = $prev_pages_visible;  $i <= ($page-1) ; $i++) {
                    $url_i_prev = admin_url("admin.php?page=counter_free_plagin&p=$i#header-visitors-days", __FILE__);
                    echo "<a href=\"$url_first\">$i</a>&nbsp;&nbsp;";
                }
            }
            echo "$page &nbsp;&nbsp;"; 
            if (($page + PAGES_NEXT_PREV_COUNT) < $pages) {
                $next_pages_visible = $page + PAGES_NEXT_PREV_COUNT;
            } else {
                $next_pages_visible = $pages;
            }
            if ($next_pages_visible <= $pages) {
                for($i = ($page + 1); $i <= $next_pages_visible; $i++) {
                    $url_i_next = admin_url("admin.php?page=counter_free_plagin&p=$i#header-visitors-days", __FILE__);
                    echo "<a href=\"$url_i_next\">$i</a>&nbsp;&nbsp;";
                }
            }
            $next_pages_visible = $page + PAGES_NEXT_PREV_COUNT;
            if ($next_pages_visible < $pages) {
                $url_last = admin_url("admin.php?page=counter_free_plagin&p=$pages#header-visitors-days", __FILE__);
                echo "...&nbsp;&nbsp;<a href=\"$url_last\">". $pages ." </a>";
            }
            if ($page != $pages) {
                $next = $page + 1;
                $url_next = admin_url("admin.php?page=counter_free_plagin&p=$next#header-visitors-days", __FILE__);
                echo "&nbsp;&nbsp;&nbsp;<a href=\"$url_next\">Next Page</a>&nbsp;&nbsp;";
            }
        }
        static function sort_data($data = array())
        {
            $max = 0;
            $record_data = array();
            $is_array = false;
            foreach($data as $data_id => $data_value) {
                if (is_array($data_value)) {
                    $max = $max + $data_value['count'];
                    $record_data[] = $data_value['count']; 
                    $is_array = true;
                } else {
                    $max = $max + $data_value;
                    $record_data[] = $data_value;
                }
            }    
            if ($is_array) {
                array_multisort($record_data, SORT_DESC, SORT_NUMERIC, $data);
            } else {
                arsort($data);
            }

            return array("max" => $max, "data" => $data);
        }
        static function widgets_initial() 
        {
            if (get_option(_PREFIX . 'images')) {
                register_widget('counter_free_widget');
                /*  wp_register_script('jquery_my', "http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, false);
                wp_enqueue_script('jquery_my');    */
            }
        }
        static function check_stat()
        {
            if (isset($_POST["id_counter"]) && isset($_POST['value_'])) {
                $id_counter = get_option(_PREFIX . 'counter_id');
                if ($id_counter == $_POST["id_counter"]) {
                    $code = get_option(_PREFIX . 'counter_code');
                    $code = preg_replace("/<!--end-->(.*)<\/div>/i", "<!--end-->{$_POST['value_']}</div>", $code);
                    update_option(_PREFIX . 'counter_code', $code);
                    echo "ok";
                }
            }
            return true;
        }


    }

    class counter_free_widget extends WP_Widget {

        function __construct()
        {
            $widget_ops = array( 'classname' => 'counter_free_widget', 'description' => 'Counter Free for your site' );

            $control_ops = array( 'width' => 400, 'height' => 550, 'id_base' => 'counter_free_widget' );
            if (version_compare(phpversion(), '5.0.0', '>=')) {
                parent::__construct('counter_free_widget', 'Free Counter', $widget_ops, $control_ops);
            } else {
                $this->WP_Widget('counter_free_widget', 'Free Counter', $widget_ops, $control_ops );
            }
        }
        function widget( $args, $instance ) 
        {
            $code = get_option(_PREFIX . 'counter_code');
            echo $code;
        }
        function form( $instance ) 
        {

            $images = get_option(_PREFIX . 'images');
            $image_default = get_option(_PREFIX . 'default_image');
            $image_hidden = get_option(_PREFIX . 'default_hidden');
            $image_color = get_option(_PREFIX . 'image_color');

            $defaults = array("images" => $images, "default_image" => $image_default, "default_hidden" => $image_hidden, "color_image" => $image_color);
            $instance = wp_parse_args($defaults, $instance  ); 
            ob_start();
            include(plugin_dir_path( __FILE__ ) . "template/settings_form.php");
            $form = ob_get_clean();
            echo $form;

        }

        function update( $new_instance, $old_instance ) 
        {
            if (isset($new_instance['counter_type']) && isset($new_instance['counter_hidden']) && isset($new_instance['counter_image_color'])) {   
                update_option(_PREFIX . 'default_image', $new_instance['counter_type']);
                update_option(_PREFIX . 'default_hidden', $new_instance['counter_hidden']);
                update_option(_PREFIX . 'image_color', $new_instance['counter_image_color']);
                $email = "";
                $password = "";
                $data_post_default = array(
                "action" => "get_count_image_on_setting", 
                "image" => $new_instance['counter_type'], 
                "hidden" => $new_instance['counter_hidden'], 
                "image_color" => $new_instance['counter_image_color'],
                "email" => $email,
                "password" => $password);
                $result_default_image_setting = counter_free_plagin::sendToServer($data_post_default);
                if (isset($result_default_image_setting['code'])) {
                    update_option(_PREFIX . 'counter_code', $result_default_image_setting['code']);
                }
            }
        }
    } 
    if(is_admin()) {
        register_activation_hook( __FILE__, array('counter_free_plagin','on_activate'));
        register_deactivation_hook( __FILE__, array('counter_free_plagin','on_deactivate'));
        add_action('admin_menu', array('counter_free_plagin', 'draw_menu'));
        add_action('admin_post_exportToCsv', array('counter_free_plagin', 'exportToCsv') );
        add_action('admin_post_save_account', array('counter_free_plagin', 'save_account') );
        add_action('wp_ajax_nopriv_check_stat', array('counter_free_plagin', 'check_stat') );   
        add_action('admin_print_styles', "adding_files_style" );   
        add_action('admin_print_scripts', "adding_files_script" );   

    }      
    add_action('widgets_init', array('counter_free_plagin', 'widgets_initial') );

    /**
    * check hash and server data
    */
    function chackArray($ar1, $ar2, $count_array)
    {
        if (count($ar2) > 0) {
            $unique = array();
            foreach ($ar2 as $elem) {
                $flag = inArray($elem, $ar1);
                if ($flag !== false) {
                    unset($ar1[$flag]);
                } 
                $unique[] = $elem;
            }
            if (count($ar1) > 0) {
                foreach($ar1 as $k => $v) {
                    if (isset($count_array[$ar1[$k][10]])) {
                        $ar1[$k][10] = $count_array[$ar1[$k][10]];
                    }
                }
                $unique = array_merge_recursive($ar1, $ar2);
            }
            return $unique;
        } else {
            foreach ($ar1 as $key =>$elem) {
                if (isset($count_array[$ar1[$key][10]])) {
                    $ar1[$key][10] = $count_array[$ar1[$key][10]];
                }
            }
            return $ar1;
        }
    }
    /**
    * search element in array
    */
    function inArray($element, $array) 
    {
        $f = false;
        $elem_hash = md5($element[4]);
        foreach($array as $i => $value) {
            if ($elem_hash == md5($array[$i][4])) {
                $f = $i;
                break;
            }
        }
        return $f;
    }
    /**
    * load styles and scrits
    * 
    * @param mixed $stat - all load or general
    */
    function adding_files_style() {
        wp_register_style('free_counter_css', plugins_url('template/counter_style.css', __FILE__));
        wp_enqueue_style('free_counter_css');
        wp_register_style('free_counter_jquery_minicolors_css', plugins_url("template/jquery.minicolors.css",__FILE__));
        wp_enqueue_style('free_counter_jquery_minicolors_css');


    }
    function adding_files_script()
    {
        wp_register_script('free_counter_script', plugins_url("template/counter.js",__FILE__));
        wp_enqueue_script('free_counter_script');
        wp_register_script('free_counter_jquery_minicolors_js', plugins_url("template/jquery.minicolors.js",__FILE__));
        wp_enqueue_script('free_counter_jquery_minicolors_js');
    }



?>
