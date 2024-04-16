<?php
//synced to CRM API 26/03/2024
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SEOAiAutomation;
use PDO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\FileUploader;
use Aws\S3\S3Client;
use App\Models\PicStat;
use GuzzleHttp\Client;


class SMAI_SEO_PUNBOTController extends Controller
{

  private $conn;

  public function __construct()
  {

          date_default_timezone_set('Asia/Bangkok');
          // Host Name
          $db_hostname = 'localhost';
          // Database Name
          $db_name = 'cafealth_punbot_seo';
          // Database Username
          $db_username = 'cafealth_punbot_seo';
          // Database Password
          $db_password = 'YSvKdba1e2}k';
          try {

              $conn = new PDO("mysql:host=$db_hostname;dbname=$db_name",$db_username,$db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
              $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              $this->conn = $conn;
                
          }
          catch(PDOException $e){
              echo $e->getMessage('utf8mb4');
          }

  }

  public function getConn() {
    return $this->conn;
  }

  
  public function sanitize_filename($filename) {
  if (!mb_check_encoding($filename, 'UTF-8')) {
      $filename = iconv(mb_detect_encoding($filename, mb_detect_order(), true), 'UTF-8', $filename);
  }
  // Replacing forbidden characters in filenames
  $filename = str_replace(array('<','>',':', '"', '/', '\\', '|', '?', '*'), '_', $filename);
  // Making sure the filename only ends with .jpg
  if (pathinfo($filename, PATHINFO_EXTENSION) != "jpg") {
      $filename = pathinfo($filename, PATHINFO_FILENAME) . ".jpg";
  }
  return $filename;
}

public function transliteration($str) {
  // Same array as before
  $translit = array(
      'ก' => 'k', 'ข' => 'kh', 'ฃ' => 'kh', 'ค' => 'kh',
      'ฅ' => 'kh', 'ฆ' => 'kh', 'ง' => 'ng', 'จ' => 'j',
      'ฉ' => 'ch', 'ช' => 'ch', 'ซ' => 's', 'ฌ' => 'ch',
      'ญ' => 'y', 'ฎ' => 'd', 'ฏ' => 't', 'ฐ' => 'th',
      'ฑ' => 'th', 'ฒ' => 'th', 'ณ' => 'n', 'ด' => 'd',
      'ต' => 't', 'ถ' => 'th', 'ท' => 'th', 'ธ' => 'th',
      'น' => 'n', 'บ' => 'b', 'ป' => 'p', 'ผ' => 'ph',
      'ฝ' => 'f', 'พ' => 'ph', 'ฟ' => 'f', 'ภ' => 'ph',
      'ม' => 'm', 'ย' => 'y', 'ร' => 'r', 'ฤ' => 'rue',
      'ล' => 'l', 'ว' => 'w', 'ศ' => 's', 'ษ' => 's',
      'ส' => 's', 'ห' => 'h', 'ฬ' => 'l', 'อ' => 'o',
      'ฮ' => 'h'
  );

  $str = strtr($str, $translit); // Transliterate the characters in the array 

  // This will remove any other characters
  return preg_replace('/[^a-z]/i', '', $str); 
}
   
    
    public function  get_wp_user_app_password($website_id,$conn)
    {
    
      $return_wp_arr=[];
      $statement = $conn->prepare( "SELECT * FROM websites WHERE 	website_id = ?  ORDER BY website_id DESC LIMIT 1" );
        $statement->execute( array($website_id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
    
      if($total>0)
      {
         
      array_push($return_wp_arr,$result['user_name']);
      array_push($return_wp_arr,$result['wp_app_password']);
      array_push($return_wp_arr,$result['user_full_name']);
    
      
    
      }
      else{
    
        $return_wp_arr=0;
    
      }
    
      return  $return_wp_arr;
    
    
    
    }
    
    
    //public function  for spin add content
    
    
    public function  update_lang_smart_content($lang_table,$lang_key_id,$lang_key_array,$lang_desc_array,$conn_smart)
    {
    
      //echo "/n/n debug ID ".$lang_key_id;
      echo "\n\n!!!!!!!!!!! \n\n ";
      echo "\n\n debug Table ".$lang_table;
      echo "\n\n!!!!!!!!!!! \n\n ";
    
      $translated_val = 1;
      $update = $conn_smart->prepare("UPDATE $lang_table SET `desc_value` = ?, translated = ? WHERE `id` = ?");
      
      try {
    
      $update->execute(array( strval($lang_desc_array), $translated_val, $lang_key_id));
    
      $count =  $update->rowCount() ;
    
      if( $count > 0)
      {
        echo "Successss Updated translate";
      }
      else{
    
    
        echo "<br><br>!!!!!!!!!! Update Smart Content Lang failed ";
        //echo '<br><br> Error: ' . mysql_error() .'<br><br>';
    
        print_r($update->errorInfo());
    
        echo "Now try again with another Qry ";
    
    
      }
      
          }
      catch(PDOException $e) {
            echo "<br><br>!!!!!!!!!! Update Smart Content Lang failed";
            echo '<br><br> Error: ' . $e->getMessage().'<br><br>';
          }
    
    
    }
    
    public function  ins_lang_smart_content($lang_table,$lang_key_array,$lang_desc_array,$conn_smart,$file_name=NULL)
    {
    
      $t_now = date('Y-m-d H:i:s');

      if(isset($file_name) && $file_name!=NULL)
      {
        $insert = $conn_smart->prepare("INSERT IGNORE INTO $lang_table (key_lang, desc_value, desc_en_value, created_at, translated, file_name ) VALUES(?,?,?,?,?,?)");
      }
      else
      {
        $insert = $conn_smart->prepare("INSERT IGNORE INTO $lang_table (key_lang, desc_value, desc_en_value, created_at, translated ) VALUES(?,?,?,?,?)");
      }
    
    
      //$insert = $conn_smart->prepare("INSERT IGNORE INTO $lang_table (key_lang, desc_value, desc_en_value, created_at, translated ) VALUES(?,?,?,?,?)");
    
     if( count($lang_key_array)>0)
     {
      try {
    
    
      for($i=0;$i<count($lang_key_array); $i++)
      {
        
        if($lang_desc_array[$i]==NULL)
        $lang_desc_array[$i]='';
    
        if(isset($file_name) && $file_name!=NULL)
        $insert->execute(array( $lang_key_array[$i] , $lang_desc_array[$i] , $lang_desc_array[$i], $t_now ,0, $file_name));
        else
        $insert->execute(array( $lang_key_array[$i] , $lang_desc_array[$i] , $lang_desc_array[$i], $t_now ,0));
          
          
          //echo "<br><br>!!!!!!!!!! Insert Smart Content Lang  success of ".$i."  ".$lang_key_array[$i];
          $lang_ins=1;
      }
    
        
      
      } catch(PDOException $e) {
        $lang_ins=0;
        //echo "<br><br>!!!!!!!!!! Insert Smart Content Lang failed of !!!!!!!!!!!!!!!!!!! ".$i;
        //echo '<br><br> Error: ' . $e->getMessage().'<br><br>';
      }
    
    }
    
    return $lang_ins;
    
    }
    
    public function  add_Big_Content($keyrelate_en_old, $keyrelate_en_old_id, $keyword_en, $ids_to_skip, $conn, $keyword) {
    
      $arr_bigpost_content= array();
      $round=1;
      $response_code=100;
      $start_id=$keyrelate_en_old_id;
    
      if(strlen ($keyrelate_en_old) <2)
      $keyword_en=$keyword_en;
      else
      $keyword_en=$keyrelate_en_old;
    
    
      while($response_code==100)
      {
    
        //case use keyword_en
        if($round==1)
        {
    
          if(strlen ($keyrelate_en_old) <2)
          $keyword_en=$keyword_en;
          else
          $keyword_en=$keyrelate_en_old;
    
         
          $en_keyword_id=  $keyrelate_en_old_id;
          $start_id=$en_keyword_id;
          $en_keyword=  $keyword_en;
          
    
          echo '\n\n Debug Case ROUND 1 \n\n';
    
        }
    
        //case use relate keyword
        else{
    
          echo '\n\n Debug Case ROUND > 1 \n\n'.$round;
       
          echo "\n\n Debug Keyword EN : ".$keyword;
        
        //arg back from get_relate_keyword fnc
        $en_keyword_arr=get_relate_keyword($keyword,$start_id,$conn);
       
        $en_keyword=  $en_keyword_arr[1];
        $en_keyword_id=  $en_keyword_arr[0];
        $keyword_en=$en_keyword;
        $start_id=$en_keyword_id;
    
        echo "\n\n New keyword EN : ".$en_keyword;
    
        unset($ids_to_skip);
        $ids_to_skip=array();
        $ids_to_skip=json_encode($ids_to_skip); 
    
        }
    
         //seo punbot Big Content add new post
         $url = "https://members.bigcontentsearch.com/api/article_get_by_search_term";
    
         $arr_bigpost_content=array();
         $curl = curl_init($url);
         //curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_POST, true);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      
      $data = "username=haggcass@gmail.com&api_key=01381b9c-be05-496c-a1fe-229f59c0691e&search_term=".$keyword_en."&ids_to_skip=".$ids_to_skip;
      // $data = "username=haggcass@gmail.com&api_key=01381b9c-be05-496c-a1fe-229f59c0691e&search_term=".$keyword_en;
       
     
      
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
       $response = curl_exec($curl);
    
        echo "\n\n !!!!!!!! Start Fnc BigContent response ";
        print_r($response);
        echo $response;
        
       //$html_response=str_replace("/\\n/","\n",$response);
       $html_response=nl2br($response);
       // $html_response=$response;
    
       var_dump(json_decode($html_response,true));
       $read_response=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '<br>', $html_response), true );
       
       //add postbody
       $post_body= $read_response['response']['text'];
       // convert newline to br tag
       $post_body=nl2br($post_body);
       //trim the begining
       $post_body=str_replace("ï»¿","",$post_body);
       // change n to br
       $post_body = str_replace("\r\n", '<br>', $post_body);
       $post_body = preg_replace('/(\.{20}?|.{10,20}?\. )/', '$1<br />', $post_body );
       //$post_body=nl2br($post_body)." <br>";
    
    
       $big_post_id= $read_response['response']['uid'];
       $big_post_title= $read_response['response']['title'];
       $response_code= $read_response['status_code'];
       echo "\n\n !!!!!!!!!!!!!!!!!! Debug Response Big ID Code : ".$response_code;
       array_push($arr_bigpost_content, $big_post_id, $big_post_title,$post_body,$en_keyword,$en_keyword_id);
      
       curl_close($curl);
    
       $round++;
    
    
      }
    
       return $arr_bigpost_content;
    
    
       
    }
    
    
    
    
    
    public function  startsWith ($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
    
    public function  endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }
      
    
    
    public function  get_title_from_origi($edit_post_id,$conn)
    {
    
      $return_post_arr=[];
      $statement = $conn->prepare( "SELECT * FROM posts WHERE post_id = ?  ORDER BY post_id DESC LIMIT 1" );
        $statement->execute( array($edit_post_id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      if($total>0)
      {
        
        echo "\n\n Original Translated from Post ID is : ". $result['translated_from_id'];
      $origi_post_id=$result['translated_from_id'];
      $statement2 = $conn->prepare( "SELECT * FROM posts WHERE post_id = ?  ORDER BY post_id DESC LIMIT 1" );
        $statement2->execute( array($origi_post_id) );
        $total2 = $statement2->rowCount();
        $result2 = $statement2->fetch( PDO::FETCH_ASSOC );
      
    
          if($total2 >0 )
          {
            $orginal_post_id=$result2['post_id'];
            $original_post_v=$result2['post_version'];
            $original_post_title=$result2['post_title'];
            array_push($return_post_arr,$original_post_title);
    
            echo "\n\n Debug in punpage_fnc.php Original Post Title ".$original_post_title;
    
    
          }
          else{
    
            $original_post_title='';
            array_push($return_post_arr,$posttitle);
          }
    
      $post_v=$result['post_version'];
      array_push($return_post_arr,$post_v);
      array_push($return_post_arr,$origi_post_id);
      }
      else{
    
        $return_post_arr=0;
      }
    
        return ($return_post_arr);
    
    
    
    }
    
    public function  updateNewContent($post_id,$new_content,$conn)
    {
    
      $update = $conn->prepare("UPDATE `posts` SET `post_description` = ? WHERE `post_id` = ?");
      $update->execute(array($new_content,$post_id));
    
    
    }
    
    public function  protected_linkdec($post_id,$conn)
    {
    
       $return_post_arr=[];
      $statement = $conn->prepare( "SELECT * FROM posts WHERE post_id = ?  ORDER BY post_id DESC LIMIT 1" );
        $statement->execute( array($post_id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      if($total>0)
      {
        $last_id=$result['post_id'];
    
      $translated=$result['translated'];
      array_push($return_post_arr,$translated);
    
      $post_v=$result['post_version'];
      array_push($return_post_arr,$post_v);
      }
      else{
    
        $return_post_arr=0;
      }
    
        return ($return_post_arr);
    
    }
    
    
    
    /* BOF Cron Jobs Fnc */
    public function  update_cron_share_loop($bl_id, $conn)
    {
    //update ROund Share Backlin Option the data
    $update = $conn->prepare("UPDATE `backlinks_option` SET `backlinks_option`.`round_count` = `backlinks_option`.`round_count`+1 WHERE id = ?");
    $update->execute(array($bl_id));
    
    }
    
    
    public function   update_cron_keyword_loop($keyword_id, $keywords, $conn)
    {
    //update ROund Keyword ai_automation the data
    $update = $conn->prepare("UPDATE websites_option SET inq = 1 WHERE id = ?");
    $update->execute(array($keyword_id));
    
    }
    
    
    public function  count_cron_log($conn)
    {
    
        $statement1 = $conn->prepare( "SELECT link_dec_post_log.id, DATE_FORMAT(link_dec_post_log.date_shared, '%Y-%m-%d') 
        FROM link_dec_post_log 
        WHERE  DATE(date_shared) = CURDATE()" );
        $statement1->execute( array( ) );
        $total1 = $statement1->rowCount();
        return ($total1);
    
    
    }
    
    public function  count_share_log($conn)
    {
    
        $statement1 = $conn->prepare( "SELECT shared_log.id, DATE_FORMAT(shared_log.date_shared, '%Y-%m-%d') 
        FROM shared_log
        WHERE  DATE(date_shared) = CURDATE()" );
        $statement1->execute( array( ) );
        $total1 = $statement1->rowCount();
        return ($total1);
    
    
    }
    
    public function  count_main_share_log($conn)
    {
    
        $statement1 = $conn->prepare( "SELECT shared_log.id, DATE_FORMAT(shared_log.date_shared, '%Y-%m-%d') 
        FROM shared_log
        WHERE bl_type='main' AND DATE(date_shared) = CURDATE()" );
        $statement1->execute( array( ) );
        $total1 = $statement1->rowCount();
        return ($total1);
    
    
    }
    
    
    public function  swith_on_all_cronpost($conn)
    {
    //update ai_automation the data
    $id_start_at=0;
    $update = $conn->prepare("UPDATE ai_automation SET today = 1 WHERE id > ?");
    $update->execute(array($id_start_at));
    
    }
    
    
    public function  swith_on_all_cronMainPost($conn)
    {
    //update ai_automation the data
    $id_start_at=0;
    $update = $conn->prepare("UPDATE ai_automation SET post_today = 1 WHERE id > ?");
    $update->execute(array($id_start_at));
    
    }
    
    public function  swith_on_all_cronBackLinkPost($conn)
    {
    //update ai_automation the data
    $id_start_at=0;
    $update = $conn->prepare("UPDATE ai_automation SET bl_today = 1 WHERE id > ?");
    $update->execute(array($id_start_at));
    
    
    }
    
    
    public function  mainbl_small_round_check($conn)
    {
    
      $bl_type='main';
        $statement = $conn->prepare( "SELECT *
      FROM `backlinks_option`
      WHERE `backlinks_option`.`bl_type` = ?
      AND `backlinks_option`.`round_num` > `backlinks_option`.`round_count`
      ORDER BY id DESC LIMIT 1" );
    
        $statement->execute( array($bl_type) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
        if($total>0 && $total != NULL)
      {
        echo "\n\n Case Small need not to Reset ROund : ";
      }
      else{
        echo "\n\n !!!!!!! Case RESET Small ROund  ";
        swith_on_all_SmallRoundMainLink($conn);
       
      }
      
        return ($total);
    
    
    
    }
    
    public function  bl_small_round_check_webid($conn,$web_id,$type=NULL)
    {
    
      if($type=NULL || $type= 'backlink')
      $bl_type='backlink';
      else
      $bl_type='main';
    
    
        $statement = $conn->prepare( "SELECT *
      FROM `backlinks_option`
      WHERE `backlinks_option`.`bl_type` = ?
      AND `backlinks_option`.`round_num` > `backlinks_option`.`round_count`
      AND `backlinks_option`.`website_id` = ?
      ORDER BY id DESC LIMIT 1" );
    
        $statement->execute( array($bl_type,$web_id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
        if($total>0 && $total != NULL)
      {
        echo "\n\n Case Small need not to Reset ROund : ";
      }
      else{
        echo "\n\n !!!!!!! Case RESET Small ROund  ";
         //swith_on_all_SmallRoundBackLink($conn);
         swith_on_all_SmallRoundBackLink_webid($conn,$web_id);
       
      }
      
        return ($total);
    
    
    
    }
    
    
    public function  bl_small_round_check($conn)
    {
    
      $bl_type='backlink';
        $statement = $conn->prepare( "SELECT *
      FROM `backlinks_option`
      WHERE `backlinks_option`.`bl_type` = ?
      AND `backlinks_option`.`round_num` > `backlinks_option`.`round_count`
      ORDER BY id DESC LIMIT 1" );
    
        $statement->execute( array($bl_type) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
        if($total>0 && $total != NULL)
      {
        echo "\n\n Case Small need not to Reset ROund : ";
      }
      else{
        echo "\n\n !!!!!!! Case RESET Small ROund  ";
         swith_on_all_SmallRoundBackLink($conn);
       
      }
      
        return ($total);
    
    
    
    }
    
    public function  swith_on_all_SmallRoundBackLink($conn)
    {
    //update ai_automation the data
    $bl_type='backlink';
    $update = $conn->prepare("UPDATE backlinks_option SET round_count = 0 WHERE bl_type LIKE ?");
    $update->execute(array($bl_type));
    
    }
    
    public function  swith_on_all_SmallRoundBackLink_webid($conn,$web_id)
    {
    //update ai_automation the data
    $bl_type='backlink';
    $update = $conn->prepare("UPDATE backlinks_option SET round_count = 0 WHERE bl_type LIKE ? AND website_id = ?");
    $update->execute(array($bl_type,$web_id));
    
    }
    
    public function  swith_on_all_SmallRoundMainLink($conn)
    {
    //update ai_automation the data
    $bl_type='main';
    $update = $conn->prepare("UPDATE backlinks_option SET round_count = 0 WHERE bl_type LIKE ?");
    $update->execute(array($bl_type));
    
    }
    
    
    public function  swith_on_cronpost($siteid,$conn)
    {
    //update ai_automation the data
    $update = $conn->prepare("UPDATE ai_automation SET today = 1 WHERE website_id = ?");
    $update->execute(array($siteid));
    
    }
    
    public function  swith_on_cronMainPost($siteid,$conn)
    {
    //update ai_automation the data
    $update = $conn->prepare("UPDATE ai_automation SET post_today = 1 WHERE website_id = ?");
    $update->execute(array($siteid));
    
    }
    
    public function  swith_on_cronBackLinkPost($siteid,$conn)
    {
    //update ai_automation the data
    $update = $conn->prepare("UPDATE ai_automation SET bl_today = 1 WHERE website_id = ?");
    $update->execute(array($siteid));
    
    }
    
    public function  swith_off_cronpost($siteid,$conn)
    {
    //update ai_automation the data
    $update = $conn->prepare("UPDATE ai_automation SET today = 0 WHERE website_id = ?");
    $update->execute(array($siteid));
      
    }
    
    
    public function  swith_off_cronMainpost($siteid,$conn)
    {
    //update ai_automation the data
    $update = $conn->prepare("UPDATE ai_automation SET post_today = 0 WHERE website_id = ?");
    $update->execute(array($siteid));
      
    }
    
    public function  swith_off_cronBacklinkPost($siteid,$conn)
    {
    //update ai_automation the data
    $update = $conn->prepare("UPDATE ai_automation SET bl_today = 0 WHERE website_id = ?");
    $update->execute(array($siteid));
      
    }
    
    
    /* EOF Cron Jobs Fnc */
    
    
    /* BOF Google Transation Fnc group  */
    public function  gg_translate_detectv3($text_to_tran)
    {
    
     
                     /* $url = 'https://translation.googleapis.com/language/translate/v2/detect?';
    
                      $curl = curl_init($url);
                      curl_setopt($curl, CURLOPT_POST, true);
                      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
                      $data = 'q='.$text_to_tran.'&key=' . $apiKey ;
    
                      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
                      $result = curl_exec($curl);
                      curl_close($curl);
    
                      return ($result); */
      
                    $apiKey = 'AIzaSyCdOvJEsJYGPCEI73H7pbevwFr42t_VFx8';
                    $text = $text_to_tran;
                    //$url = 'https://translation.googleapis.com/language/translate/v2/detect?key=' . $apiKey ;
    
                
                  //$url = "https://translation.googleapis.com/v3/projects/valued-fortress-354801/locations/global:detectLanguage";
                  $url = "https://translation.googleapis.com/v3/projects/valued-fortress-354801/locations/global:detectLanguage?content=".$text ;
    
                  $curl = curl_init($url);
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($curl, CURLOPT_POST, true);
                 
    
                  $headers = array(
                      "X-Custom-Header: header-value",
                      "Content-Type: application/json",
                      "X-Goog-User-Project: valued-fortress-354801",
                      "Authorization: Bearer ya29.a0AeTM1idTKJSYEdW1qFf6Cm2ojOZS0Q_IQXjchEWPPoR_T7DHzcm2MqS3fXkwn7Bb5kdOrXV_GM9zbvaHAMg3JhANJxbn9Sl9QETHR93A0temNtd9eFeN4bbn3vjFKo9VA0HPo7qW8PaQwlOasQj7lFXmhnOXpwaCgYKAV0SARESFQHWtWOmVhJJrpDPHF7CyMWAIbrR7w0165",
                      "key: AIzaSyCdOvJEsJYGPCEI73H7pbevwFr42t_VFx8"
                      
                      
                  );
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                  curl_setopt($curl, CURLOPT_HEADER, false);
                  $response = curl_exec($curl);
                  $responseDecoded1 = json_decode( $response,true);
                  $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
    
                  print_r($responseCode1);
                  print_r( $responseDecoded1);
                  echo "<br> Language : " .$responseDecoded1['languages'][0]['languageCode'];
                  if($responseCode1==200)
                   $return_lg=$responseDecoded1['languages'][0]['languageCode'];
                   else
                   $return_lg=0;
    
                  return ($return_lg); 
             
        
        
        
    }
    
    
    
    
    
    public function  gg_translate_detect($text_to_tran)
    {
    
                     /* $url = 'https://translation.googleapis.com/language/translate/v2/detect?';
    
                      $curl = curl_init($url);
                      curl_setopt($curl, CURLOPT_POST, true);
                      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
                      $data = 'q='.$text_to_tran.'&key=' . $apiKey ;
    
                      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
                      $result = curl_exec($curl);
                      curl_close($curl);
    
                      return ($result); */
      
                    $apiKey = 'AIzaSyCdOvJEsJYGPCEI73H7pbevwFr42t_VFx8';
                    $text = $text_to_tran;
                    //$url = 'https://translation.googleapis.com/language/translate/v2/detect?key=' . $apiKey ;
    
                
                  //$url = "https://translation.googleapis.com/v3/projects/valued-fortress-354801/locations/global:detectLanguage";
                  $url = "https://translation.googleapis.com/language/translate/v2/detect?q=".$text."&key=" . $apiKey ;
    
                  $curl = curl_init($url);
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
                  $headers = array(
                      "X-Custom-Header: header-value",
                      "Content-Type: application/json"
                      
                      
                  );
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                  curl_setopt($curl, CURLOPT_HEADER, false);
                  $response = curl_exec($curl);
                  $responseDecoded1 = json_decode( $response,true);
                  $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
    
                  //print_r($responseCode1);
                  //print_r( $responseDecoded1);
                  echo "<br> Language : " .$responseDecoded1['data']['detections'][0] [0] ['language'];
                  if($responseCode1==200)
                   $return_lg=$responseDecoded1['data']['detections'][0] [0] ['language'];
                   else
                   $return_lg=0;
    
                  return ($return_lg); 
             
        
        
        
    }
    
    
    //working
    public function  gg_translate($to_language,$text_to_tran)
    {
                    
                    $apiKey = 'AIzaSyCdOvJEsJYGPCEI73H7pbevwFr42t_VFx8';
                    $text = $text_to_tran;
                    $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&target='.$to_language;
    
                    $handle = curl_init($url);
                    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($handle);
                    $responseDecoded = json_decode($response, true);
                    $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
                    curl_close($handle);
    
                    if($responseCode != 200) {
                        echo 'Fetching translation failed! Server response code:' . $responseCode . '<br>';
                        echo 'Error description: ' . $responseDecoded['error']['errors'][0]['message'];
                    }
                    else {
                       // echo 'Source: ' . $text . '<br>';
                        //echo 'Translation: ' . $responseDecoded['data']['translations'][0]['translatedText'];
                    }
    
                
                    $api_response = $responseDecoded['data']['translations'][0]['translatedText'];
                    return($api_response);
                
                
                // Output the API response.
                /*echo "<b>API response translated :</b>";
                echo "<br><br>\n\n";
                echo "<pre>";
                print_r($api_response);
                echo "</pre>";*/
        
        
        
    }
    
    public function  gg_translatev3($to_language,$text_to_tran)
    {
                    
                    $apiKey = 'AIzaSyCdOvJEsJYGPCEI73H7pbevwFr42t_VFx8';
                    $text = $text_to_tran;
                    $url = 'https://translation.googleapis.com/v3/projects/valued-fortress-354801/locations/us-central1/models/TRL1395675701985363739';
    
                    $handle = curl_init($url);
                    curl_setopt($handle, CURLOPT_URL,$url);
                    curl_setopt($handle, CURLOPT_POST, 1);
                    curl_setopt($handle, CURLOPT_POSTFIELDS,
                '?sourceLanguageCode=en&key=' . $apiKey . '&contents=' . rawurlencode($text) . '&q=' . rawurlencode($text) . '&source=en&target='.$to_language.'&targetLanguageCode='.$to_language);
                    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($handle);
    
                    print_r($response);
                    $responseDecoded = json_decode($response, true);
                    $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
                    curl_close($handle);
    
                    if($responseCode != 200) {
                        echo 'Fetching translation failed! Server response code:' . $responseCode . '<br>';
                        echo 'Error description: ' . $responseDecoded['error']['errors'][0]['message'];
                    }
                    else {
                       echo 'Source: ' . $text . '<br>';
                        echo 'Translation: ' . $responseDecoded['data']['translations'][0]['translatedText'];
                    }
    
                
                    $api_response = $responseDecoded['data']['translations'][0]['translatedText'];
                    return($api_response);
                
                
                // Output the API response.
                /*echo "<b>API response translated :</b>";
                echo "<br><br>\n\n";
                echo "<pre>";
                print_r($api_response);
                echo "</pre>";*/
        
        
        
    }
    
    
    public function  gg_translate_v3($to_language,$text_to_tran)
    {
     
      
      $translationClient = new TranslationServiceClient();
      $content = $text_to_tran;
      $targetLanguage = $to_language;
      $response = $translationClient->translateText(
          $content,
          $targetLanguage,
          TranslationServiceClient::locationName('valued-fortress-354801', 'global')
      );
      $api_response_arr=array();
      foreach ($response->getTranslations() as $key => $translation) {
          $separator = $key === 2
              ? '!'
              : ', ';
          echo $translation->getTranslatedText() . $separator;
          array_push($api_response_arr,$translation->getTranslatedText());
      }
    
    
      return($api_response_arr);
    
    }
    /* EOF Transation Fnc group */
    
    /* BOF String Fnc group  */
    
    
    public function  limit_filename_length($filename, $length)
            {
                    if (strlen($filename) < $length)
                    {
                            return $filename;
                    }
           
                    $ext = '';
                    if (strpos($filename, '.') !== FALSE)
                    {
                            $parts          = explode('.', $filename);
                            $ext            = '.'.array_pop($parts);
                            $filename       = implode('.', $parts);
                    }
           
            return substr($filename, 0, ($length - strlen($ext))).$ext;
            }
    
    
    
    //working
    public function  randomPassword( $count ) {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen( $alphabet ) - 1; //put the length -1 in cache
      for ( $i = 0; $i < $count; $i++ ) {
        $n = rand( 0, $alphaLength );
        $pass[] = $alphabet[ $n ];
      }
      return implode( $pass ); //turn the array into a string
    }
    
    //working
    public function  str_replace_SomeKeyword( $search, $replace, $content, $limit ) {
      $search = '/' . preg_quote( $search, '/' ) . '/';
      return preg_replace( $search, $replace, $content, $limit );
    }
    //working
    public function  clean_sp_backup( $string ) {
      $string = str_replace( ' ', '-', $string ); // Replaces all spaces with hyphens.
    
      return preg_replace( '/[^A-Za-z0-9\-]/', '', $string ); // Removes special chars.
    }
    //working
    public function  clean_sp( $s ) {
      $result = preg_replace( "/[^a-zA-Z0-9]+/", "", html_entity_decode( $s, ENT_QUOTES ) );
      return $result;
    }
    
    //working
    public function  clean_file_title( $s ) {
          
         $f = str_replace(array('\\','/',':','*','?',' ','"','<','>','|',',','\'','.',';','&'),'',$s);
         return($f);
         
    }
    
    //working
    public function  clean_file_title_dash( $s ) {
          
         $f = str_replace(array('\\','/',':','*','?',' ','"','<','>','|',',','\'','.',';','&'),'',$s);
         return($f);
         
    }
    
    /* EOF String Fnc group  */
    
    /* BOF Keyword Fnc group  */
    public function  get_relate_keyword($keyword,$start_id,$conn)
    {
        $statement = $conn->prepare( "SELECT * FROM `relate_keyword` WHERE `id` > ? AND `main_keyword` LIKE ? ORDER BY `piority` ASC LIMIT 1" );
        $statement->execute( array($start_id, $keyword) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      
      $retrun_relateKeyword=array();
    
        /* $relateKeyword=$result['relate_keyword'];
      $relateKeyword_id=$result['id'];
      $retrun_relateKeyword=$relateKeyword.",".$relateKeyword_id; */
    
      array_push($retrun_relateKeyword, $result['id'], $result['relate_keyword']);
        return ($retrun_relateKeyword);
    
    
    }
    
    
    /* BOF Get Image Fnc group  */
    
    //working
    public function  update_expired_img($img_id,$conn)
    {
      //update expired image to get
      $update = $conn->prepare("UPDATE pic_stat SET post_image = 'expired.png' WHERE id = ?");
      $update->execute(array($img_id));
    
    
    }
    
    public function  update_expired_img2table($img_id,$post_id,$conn)
    {
      //update expired image to get
      $update = $conn->prepare("UPDATE pic_stat SET post_image = 'expired.png' WHERE id = ?");
      $update->execute(array($img_id));
    
    //update main posts back to default.png
      $update1 = $conn->prepare("UPDATE posts SET post_image = 'default.png' WHERE post_id = ?");
      $update1->execute(array($post_id));
    
    
    }
    
    
    public function  get_original_img($img_name,$post_id,$conn)
    {
    
      $statement = $conn->prepare( "SELECT * FROM pic_stat WHERE post_id = ? AND  post_image LIKE ?  ORDER BY id DESC LIMIT 1" );
        $statement->execute( array($post_id,$img_name) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        
      //$current_count=$result['counts'];
      $arr_origi_return=[];
    
      if($total>0)
        {
          
          $last_id=$result['id'];
          $img_original=$result['original_size'];
          array_push($arr_origi_return,$img_original);
          array_push($arr_origi_return,$last_id);
         return ($img_original);
        }
      else
      {
         return (0);
      }
    
      
    
    
    
    }
    
    
    public function  check_local_img($keyword,$conn)
    {
    
      $statement = $conn->prepare( "SELECT * FROM pic_stat WHERE post_id='0' AND  post_image = 'wait.png' AND  keywords LIKE ? ORDER BY id DESC LIMIT 1" );
        $statement->execute( array($keyword) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['id'];
      //$current_count=$result['counts'];
    
      if($total>0)
         return ($last_id);
      else
         return (0);
    
    
    
    }
    
    public function  get_origi_post_img_fromLocal($img_id,$keyword,$title,$conn)
    {
      $statement = $conn->prepare( "SELECT * FROM pic_stat WHERE post_image='wait.png' AND id = ? ORDER BY id ASC LIMIT 1" );
        $statement->execute( array($img_id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $img_id=str_replace( '"', '',$result['id'])  ;
    
      $local_stock_url=str_replace( '"', '',$result['large'])  ;
      //$n_post_title=$this->clean_sp_backup($title);
      $n_post_title=$title;
      $post_img_newname = str_replace(" ", "-", $n_post_title );
      $post_img_newname = str_replace("'", "-", $post_img_newname );
      $post_img_newname =$this->clean_file_title_dash($post_img_newname);
      $post_img_newname =trim($post_img_newname);
      $post_img_newname =$this->clean_file_title($post_img_newname);
      //$post_img_newname = preg_replace("[^a-zA-Z_0-9ก-๙]","", $post_img_newname);
      //$post_img_newname = preg_replace('/[^0-9A-Za-zก-ฮ๐-๙]/','',$post_img_newname);
      $post_img_newname = str_replace("\\0", "", $post_img_newname);
      $post_img_newname = str_replace('\0', '', $post_img_newname);
      $post_img_newname = str_replace(chr(0), '', $post_img_newname);
      $post_img_newname = trim($post_img_newname);
    
      //$post_img_newname = preg_replace('/[\x00-\x08\x0B-\x1F]/', ' ', $post_img_newname);
      //chop($post_img_newname,"\0");
      //chop($post_img_newname);
      //$post_img_newname = filter_var($post_img_newname, FILTER_VALIDATE_EMAIL);
    
      //$post_img_newname = substr_replace($post_img_newname,"12345",99);
    
    
    
    
      echo "\n\n !!!!!!!!!! New File cut Name !!!!!!!!! ".$post_img_newname." Of Keyword ".$keyword;
    
      
    
    
      //$post_img_newname=gg_translate('en',$post_img_newname);
    
    
    
      $post_img_newname .= '.jpg';
      $img_file_size=0;
      //Log::debug("Final file Image name is Before while loop is ". $post_img_newname);
      //Log::debug("before try to get image from: $local_stock_url");

        // the new way  S3
        // File saved on local disk, save it to S3  
        $filePath = 'uploads/posts/' . $post_img_newname; // Added this new line
        $client = new Client(['timeout' => 5.0]);

        while (true) {
          try {
              $response = $client->request('GET', $local_stock_url);
      
              // We only accept status code 200
              if ($response->getStatusCode() !== 200) {
                  throw new \Exception('Non 200 status code');
              }
      
              break;
      
          } catch (\Exception $e) {
              Log::debug("Cannot get file content from: $local_stock_url");
      
              // Update the PicStat model
              PicStat::where('id', $img_id)->update(['post_image' => 'expired.png']);
      
              // Query for the last id that have column "post_image" = "wait.png"
              $picStat = PicStat::where('post_image', 'wait.png')->orderBy('id', 'desc')->first();
      
              // If no 'wait.png' image left
              if($picStat === null) break;
      
              $local_stock_url = $picStat->large;
              $img_id = $picStat->id;
          } 
      }
            //eof While loop to check image content and get the image

        // After loop ends regardless of how
        if(file_get_contents($local_stock_url)) {
            // File exists and can be read
            //Log::debug("File path exists, moving on to upload to S3");

            // Get file content
            $file_content = file_get_contents($local_stock_url);
            //Log::debug("File content is ". $file_content);

            // Check if file exists on S3
            if (!Storage::disk('s3')->exists($post_img_newname)) {

              Log::debug(" File path not found then try to upload to S3 ");
  
              //SMAI new way S3 save image
              //Log::debug("File content is ". $local_stock_url);
              $file_content = file_get_contents($local_stock_url);
  
              //Log::debug("File content is ". $file_content);
  
              //Save Image to Local server
              $nameOfImage = $post_img_newname;
              Storage::disk('posts')->put($nameOfImage, $file_content);
              //Log::debug("File saved to local disk");
  
              // File saved on local disk, save it to S3  
              $filePath = 'uploads/posts/' . $nameOfImage; // Added this new line
              //Log::debug("File path is ". $filePath);
  
              if (Storage::disk('s3')->exists($filePath)) {
                  Log::debug("File already exists in S3 ".$filePath);
              } else {
                  Storage::disk('s3')->put($filePath, $file_content, 'public');
              }
  
              // Confirm that file is saved on S3 before deleting it from local disk
              if (Storage::disk('s3')->exists($filePath)) {
                //Log::debug("File already exists in S3 ".$filePath);
                $img_file_size = Storage::disk('s3')->size($filePath);
                //Log::debug("File size is " . $img_file_size . " bytes");
            } else {
                Storage::disk('s3')->put($filePath, $file_content, 'public');
                $img_file_size = strlen($file_content);  // size of the content that was put into the file
                //Log::debug("New file's size is " . $img_file_size . " bytes");
            }
           
                //eof SMAI new way S3 save image
  
                //Then generate the URL like this:
                $post_img_newname_path = Storage::disk('s3')->url($filePath);
            
      
          }
          else
          {
            
            // File saved on local disk, save it to S3  
            $filePath = 'uploads/posts/' . $nameOfImage; // Added this new line
      
            $post_img_newname_path = Storage::disk('s3')->url($filePath);  
            // Image path
      
               $n=1;
      
  
               while(Storage::disk('s3')->exists($post_img_newname))
               {
                   $post_img_newname .= $n;  // add suffix to image name
                   $post_img_newname .= '.jpg';  // concatenate image extension
               
                   $post_img_newname_path = Storage::disk('s3')->url($post_img_newname);  // Image path
               
                   if(Storage::disk('s3')->exists($post_img_newname_path))
                   {
                     $post_img_newname = str_replace($n, '', $post_img_newname);
                     $post_img_newname = str_replace('.jpg', '', $post_img_newname);
                   }
               
                   $n++;
               }
              
  
                        //SMAI new way S3 save image
              $file_content = file_get_contents($local_stock_url);
  
              //Save Image to Local server
              $nameOfImage = $post_img_newname;
              Storage::disk('posts')->put($nameOfImage, $file_content);
  
              // File saved on local disk, save it to S3  
              $filePath = 'uploads/posts/' . $nameOfImage; // Added this new line
  
              if (Storage::disk('s3')->exists($filePath)) {
                  Log::debug("File already exists in S3 ".$filePath);
              } else {
                  Storage::disk('s3')->put($filePath, $file_content, 'public');
              }
  
              // Confirm that file is saved on S3 before deleting it from local disk
              if (Storage::disk('s3')->exists($filePath)) {
                //Log::debug("File already exists in S3 ".$filePath);
                $img_file_size = Storage::disk('s3')->size($filePath);
                //Log::debug("File size is " . $img_file_size . " bytes");
            } else {
                Storage::disk('s3')->put($filePath, $file_content, 'public');
                $img_file_size = strlen($file_content);  // size of the content that was put into the file
                //Log::debug("New file's size is " . $img_file_size . " bytes");
            }
              //eof SMAI new way S3 save image
  
              
      
                
                // Save image
                /* $ch = curl_init( $local_stock_url );
                $fp = fopen( $post_img_newname_path, 'wb' );
                curl_setopt( $ch, CURLOPT_FILE, $fp );
                curl_setopt( $ch, CURLOPT_HEADER, 0 );
                curl_exec( $ch );
                curl_close( $ch );
                fclose( $fp ); */
                //eof pixarbay 
  
  
                //Then generate the URL like this:
                $post_img_newname_path = Storage::disk('s3')->url($filePath);
              
      
          }
          //eof if(file_get_contents($local_stock_url))



        } else {
            Log::warning('No suitable image found for upload');
            // Handle no suitable image being found
        }
        
        
        


         // Get filesize.
        //$img_file_size = $metadata['ContentLength'];
        echo "<br> Debug Image File SIze in Punbot Fnc ".$img_file_size;
        print_r("<br> FOund Image file size after uploaded ".$img_file_size);
      
    
        $return_arr_img=[];
        array_push($return_arr_img,str_replace( '"', '',$result['id']));
        array_push($return_arr_img,$post_img_newname);
        array_push($return_arr_img,str_replace( '"', '',$result['small']  ));
        array_push($return_arr_img,str_replace( '"', '',$result['mid']  ));
        array_push($return_arr_img,str_replace( '"', '',$result['large'] ) );
        array_push($return_arr_img,str_replace( '"', '',$result['author'] ) );
        array_push($return_arr_img,str_replace( '"', '',$result['author_id'] ) );
        array_push($return_arr_img,str_replace( '"', '',$result['hd_size'] ) );
        array_push($return_arr_img,str_replace( '"', '',$result['original_size'] ) );
        array_push($return_arr_img,str_replace( '"', '',$result['tag']  ));
        array_push($return_arr_img,str_replace( '"', '',$result['source'] ) );
        array_push($return_arr_img,'');
        array_push($return_arr_img,str_replace( '"', '',$result['search_keywords'] ) );
        echo "\n\n !!!!!!!!!! Final File Name !!!!!!!!! ".$post_img_newname_path;
      
        //14 image file size
        array_push($return_arr_img,$img_file_size);

        return ( $return_arr_img );
    
    
    }
    
    
    
    //working
    public function  pixarbay_get_toal_image( $keyword ) {
      $apiKey1 = '31427895-f4f99be1dcd52c1f3ef333a99';
    
      $url1 = 'https://pixabay.com/api/?key=' . $apiKey1 . '&image_type=photo&order=latest&q=' . $keyword;
    
      $handle1 = curl_init( $url1 );
      $handle1 = curl_init( $url1 );
      curl_setopt( $handle1, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $handle1, CURLOPT_CUSTOMREQUEST, 'GET' );
      $response1 = curl_exec( $handle1 );
      $responseDecoded1 = json_decode( $response1, true );
      $responseCode1 = curl_getinfo( $handle1, CURLINFO_HTTP_CODE );
    
      $pixar_total = $responseDecoded1[ 'total' ];
      return ( $pixar_total );
    
    
    }
    
    
    //working
    public function  pixarbay_get_image( $keyword, $perpage, $totalpage, $title, $counts ) {
    
      //bof image from pixarbay
      $pic_keyword = $keyword;
      //pixabay random page select
    
     /*  if($totalpage>2)
      {
      $pixarbay_page_select = rand( 1, $totalpage );
      }
      else
      {
      $pixarbay_page_select =1;
      } */
    
      //select direct step by step
      if ($counts>$perpage)
      {
        $pixarbay_page_select  = intval($counts/$perpage) + 1;
    
      }
      else{
        $pixarbay_page_select =1;
      }
    
    
    
      $apiKey1 = '31427895-f4f99be1dcd52c1f3ef333a99';
      $url1 = 'https://pixabay.com/api/?key=' . $apiKey1 . '&image_type=photo&order=popular&page=' . $pixarbay_page_select . '&per_page=' . $perpage . '&q=' . $pic_keyword;
    
      $handle1 = curl_init( $url1 );
      curl_setopt( $handle1, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $handle1, CURLOPT_CUSTOMREQUEST, 'GET' );
      $response1 = curl_exec( $handle1 );
      $responseDecoded1 = json_decode( $response1, true );
      $responseCode1 = curl_getinfo( $handle1, CURLINFO_HTTP_CODE );
    
      $pixar_total = $responseDecoded1[ 'total' ];
      $i_position=$perpage - 1 ;
      $i_image = 0;
    
    /*   if($pixar_total>199)
      $i_image = ( rand( 0, $i_position ) );
      else
      $i_image = ( rand( 0, $pixar_total ) ); */
    
      //echo for debug
    /*   echo "random image: " . $i_image;
      echo "<br>";
      echo $responseDecoded1[ 'hits' ][ $i_image ][ 'largeImageURL' ];
      echo "<br>";
      echo "<br>";
      echo $responseDecoded1[ 'hits' ][ $i_image ][ 'webformatURL' ];
      echo "<br>";
      echo $responseDecoded1[ 'hits' ][ $i_image ][ 'pageURL' ];
      echo "<br>";
      echo "<br>";
      print_r( $responseDecoded1 );
      print_r( $responseCode1 ); */
    
    
      // Remote pixarbay image URL
      $pixarbay_url = $responseDecoded1[ 'hits' ][ $i_image ][ 'webformatURL' ];
      //$n_post_title = $title;
      $n_post_title=clean_sp_backup($title);
      $post_img_newname = str_replace( ' ', '-', $n_post_title );
      $post_img_newname = $post_img_newname;
      $post_img_newname =clean_file_title($post_img_newname);
      $post_img_newname = str_replace( '&#39;', '-', $post_img_newname);
      $post_img_newname .= '.jpg';
    
      

      // the new way  S3
      $filePath = 'uploads/posts/' . $post_img_newname; // Added this new line
      if (!Storage::disk('s3')->exists($filePath)) {

        //SMAI new way S3 save image
        $file_content = file_get_contents($pixarbay_url);

        //Save Image to Local server
        $nameOfImage = $post_img_newname;
        Storage::disk('posts')->put($nameOfImage, $file_content);

        // File saved on local disk, save it to S3  
        $filePath = 'uploads/posts/' . $nameOfImage; // Added this new line

        if (Storage::disk('s3')->exists($filePath)) {
            Log::debug("File already exists in S3 ".$filePath);
        } else {
            Storage::disk('s3')->put($filePath, $file_content, 'public');
        }

        // Confirm that file is saved on S3 before deleting it from local disk
        if (Storage::disk('s3')->exists($filePath)) {
            Storage::disk('posts')->delete($nameOfImage);
            //Log::debug(" del image success after uploaded to S3 ");
        } else {
            Log::debug(" File not found in S3 maybe upload not success ");
        }
     
         //eof SMAI new way S3 save image

         //Then generate the URL like this:
         $post_img_newname_path = Storage::disk('s3')->url($filePath);
      }


    
    
      // Save image
     /*  $ch = curl_init( $pixarbay_url );
      $fp = fopen( $post_img_newname_path, 'wb' );
      curl_setopt( $ch, CURLOPT_FILE, $fp );
      curl_setopt( $ch, CURLOPT_HEADER, 0 );
      curl_exec( $ch );
      curl_close( $ch );
      fclose( $fp );
    
      curl_close( $handle1 ); */
      //eof pixarbay 
    
      return ( $post_img_newname );
    
    
    }
    
    public function  pixarbay_get_image_arr( $keyword,$keyword_en, $perpage, $totalpage, $title, $counts, $postversion, $conn ) {
    
       $return_arr_img='';
      //bof image from pixarbay
      $pic_keyword = $keyword_en;
    
      echo "\n\n Debug Keyword En  \n\n".$keyword_en;
    
      //select direct step by step
      if ($counts>$perpage)
      {
        $pixarbay_page_select  = intval($counts/$perpage) + 1;
    
      }
      else{
        $pixarbay_page_select =1;
      }
     /*  $detect_tt_lang=gg_translate_detectv3($title);
    
      if($detect_tt_lang=='en')
     {
        $n_post_title = $title;
     }
     else{
         
      $n_post_title =  gg_translate("en",$title);
      $n_post_title .=$postversion;
    
     } */
    
     $n_post_title = $title;
     $n_post_title .=$postversion;
    
     /* if($pic_keyword='seo')
     {
      $pic_keyword='search engine optimization';
     } */
     
    
    /*  $detect_key_lang=gg_translate_detect($pic_keyword);
    
     $pic_keyword=trim($pic_keyword);
     $pic_keyword=str_replace(' ','-',$pic_keyword);
    
     if(!preg_match('/^[a-z]+$/i',$pic_keyword))
     {
      $pic_keyword =  gg_translate("en",$pic_keyword);
     }
     else{
      $pic_keyword = $pic_keyword;
     } */
    
     echo "\n\n Debug Pic Keyword En  \n\n".$pic_keyword;
    
     //BOF check keywords Pool
     $what_check='totalpool';
     $source_name='pixarbay';
     $start_id=0;
     $total_pools=pixarbay_get_image_arr_check($what_check,$keyword);
    
     echo "\n\n Debug Total Pools : ".$total_pools;
     //loop untill the Number of Total cloud Images Pools  >  Total images was save in Database
     while ($counts>= $total_pools)
     {
         $pic_keyword_arr=get_relate_keyword($keyword,$start_id,$conn);
         $pic_keyword=  $pic_keyword_arr[1];
         $pic_keyword_id=  $pic_keyword_arr[0];
    
         //reset new all value
         $start_id= $pic_keyword_id;
         $new_counts=get_cur_img_count($pic_keyword,$source_name,$conn);
    
         //reset new counts of Keyword
         $counts=$new_counts;
         //check total pool of new keyword
         $total_pools=pixarbay_get_image_arr_check($what_check,$pic_keyword);
     }
    //EOF check keywords Pool
    
     
      echo "\n\n Debug Stop Before Send cURL To PizarBay :  \n\n";
      $apiKey1 = '31427895-f4f99be1dcd52c1f3ef333a99';
      $url1 = 'https://pixabay.com/api/?key=' . $apiKey1 . '&image_type=photo&order=popular&page=' . $pixarbay_page_select . '&per_page=' . $perpage . '&q=' . $pic_keyword;
    
     
      echo $url1;
    
      echo "\n\n Stop !!!!!!!!!!!!!!!!!! Now Send cURL";
      //exit();
      //return false;
    
      if($pic_keyword!='' && intval(strlen($pic_keyword)) >= 2 )
      {
      $handle1 = curl_init( $url1 );
      curl_setopt( $handle1, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $handle1, CURLOPT_CUSTOMREQUEST, 'GET' );
      $response1 = curl_exec( $handle1 );
      $responseDecoded1 = json_decode( $response1, true );
      $responseCode1 = curl_getinfo( $handle1, CURLINFO_HTTP_CODE );
    
      $pixar_total = $responseDecoded1[ 'total' ];
      $i_position=$perpage - 1 ;
    
      if($counts>$perpage)
      {
         $i_image =  ($counts%200)+1;
      }
      else
      {
         $i_image =  $counts+1;
      }
    
      echo "<br><br><br> Debug Count : ".$i_image;
      echo "<br><br><br> Eng  Debug Count  ";
    
       //insert all pic for use next time
     foreach  ($responseDecoded1 as $pixarbay_img)
     {
          echo "\n\n<br>";
         for($k=0; $k<80; $k++)
         {
             if($k>$i_image)
             {
              print_r($pixarbay_img[$k]);
              echo "\n\n<br> !!!!!! Debug Now in for loop oF ";
              $source_id[$k]=$pixarbay_img[$k]['id'];
              echo " ".$source_id[$k];
    
              echo " Of Total : ".$pixar_total;
    
              //$source_id_i=$pixarbay_img[$k]['id'];
    
          // set image name 
          $post_img_newname[$k] = 'wait';
          $post_img_newname[$k] .= '.png';
          
           //add small img to arr
           $source_small[$k]=$pixarbay_img[$k][ 'previewURL' ];
          
          //add medium img to arr
          $source_mid[$k]=$pixarbay_img[$k][ 'webformatURL' ];
          
            //add largeimg to arr
            $source_large[$k]=$pixarbay_img[$k][ 'largeImageURL' ];
            
    
            //add Author arr
            $source_author[$k]=$pixarbay_img[$k][ 'user' ];
            
            $source_author_id[$k]=$pixarbay_img[$k][ 'user_id' ];
            
            $source_hd[$k]='noHD';
            
            $source_original[$k]=$pixarbay_img[$k][ 'pageURL' ];
            
            $source_tag[$k]=$pixarbay_img[$k][ 'tags' ];
    
            //return the keyword that use search
            
            //$keyword[$k]=$pic_keyword;
            $edit_post_id_i=0;
            $counts_new_i=0;
            
    
                $insert[$k] = $conn->prepare("INSERT INTO pic_stat (source_id, source, post_image, small, mid, large, author, author_id, counts, post_id, keywords, hd_size, original_size, tag, search_keywords) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                //print_r($insert_i);
              
               if($k>$i_image && strlen($source_small[$k])>2)
               {
                try {
                  $insert[$k]->execute(array( $source_id[$k], $source_name,  $post_img_newname[$k], $source_small[$k], $source_mid[$k], $source_large[$k], $source_author[$k],$source_author_id[$k],$counts_new_i, $edit_post_id_i, $keyword, $source_hd[$k], $source_original[$k], $source_tag[$k], $keyword_en ));
                  echo "<br><br>!!!!!!!!!! Insert Backup Image ".$k ."  success";
                
                } catch(PDOException $e) {
                  echo "<br><br>!!!!!!!!!! Insert Backup Image ".$k ." failed";
                  echo '<br><br> Error: ' . $e->getMessage().'<br><br>';
                }
    
              }
    
    
             }
         }
    
    
         
     } 
     //eof insert all pic for use next time
    
      //echo for debug
      echo "random image: " . $i_image;
      echo "<br>";
      echo $responseDecoded1[ 'hits' ][ $i_image ][ 'largeImageURL' ];
      echo "<br>";
      echo "<br>";
      echo $responseDecoded1[ 'hits' ][ $i_image ][ 'webformatURL' ];
      echo "<br>";
      echo $responseDecoded1[ 'hits' ][ $i_image ][ 'pageURL' ];
      echo "<br>";
      echo "<br>";
      print_r( $responseDecoded1 );
      print_r( $responseCode1 ); 
    
    
      // Remote pixarbay image URL
      $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'id' ];
      $return_arr_img.=',';
    
      echo "<br> Debug arr img : ";
      print_r($return_arr_img);
    
     
    
      $pixarbay_url = $responseDecoded1[ 'hits' ][ $i_image ][ 'webformatURL' ];
      //$n_post_title = $title;
      $n_post_title=clean_file_title( $n_post_title );
      $post_img_newname = str_replace( ' ', '-', $n_post_title );
      $post_img_newname = $post_img_newname;
      $post_img_newname =clean_file_title($post_img_newname);
      $post_img_newname = str_replace( '&#39;', '-', $post_img_newname);
      $post_img_newname .= '.jpg';
    
     
      //add img name to arr
      $return_arr_img.=$post_img_newname;
      $return_arr_img.=',';
      echo "<br> Debug arr img : ";
      print_r($return_arr_img);
      //add small img to arr
      $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'previewURL' ];
      $return_arr_img.=',';
      //add medium img to arr
      $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'webformatURL' ];
      $return_arr_img.=',';
       //add largeimg to arr
       $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'largeImageURL' ];
       $return_arr_img.=',';
    
       //add Author arr
       $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'user' ];
       $return_arr_img.=',';
       $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'user_id' ];
       $return_arr_img.=',';
       //$return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'fullHDURL' ];
       $return_arr_img.='noHD';
       $return_arr_img.=',';
       $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'pageURL' ];
       $return_arr_img.=',';
       $return_arr_img.=$responseDecoded1[ 'hits' ][ $i_image ][ 'tags' ];
       //$return_arr_img.='';
       //$return_arr_img.=',';
       //return the keyword that use search
      $return_arr_img.=',';
      $return_arr_img.=$pic_keyword;
      //$return_arr_img.=',';
       
       
    
       echo "<br> Debug arr img : ";
      print_r($return_arr_img);


      // the new way  S3
      $filePath = 'uploads/posts/' . $post_img_newname; // Added this new line
      if (!Storage::disk('s3')->exists($filePath)) {

        //SMAI new way S3 save image
        $file_content = file_get_contents($pixarbay_url);

        //Save Image to Local server
        $nameOfImage = $post_img_newname;
        Storage::disk('posts')->put($nameOfImage, $file_content);

        // File saved on local disk, save it to S3  
        $filePath = 'uploads/posts/' . $nameOfImage; // Added this new line

        if (Storage::disk('s3')->exists($filePath)) {
            Log::debug("File already exists in S3 ".$filePath);
        } else {
            Storage::disk('s3')->put($filePath, $file_content, 'public');
        }

        // Confirm that file is saved on S3 before deleting it from local disk
        if (Storage::disk('s3')->exists($filePath)) {
            Storage::disk('posts')->delete($nameOfImage);
            Log::debug(" del image success after uploaded to S3 ");
        } else {
            Log::debug(" File not found in S3 maybe upload not success ");
        }
     
         //eof SMAI new way S3 save image

         //Then generate the URL like this:
         $post_img_newname_path = Storage::disk('s3')->url($filePath);
      }
    
    
      // Save image
      /* $ch = curl_init( $pixarbay_url );
      $fp = fopen( $post_img_newname_path, 'wb' );
      curl_setopt( $ch, CURLOPT_FILE, $fp );
      curl_setopt( $ch, CURLOPT_HEADER, 0 );
      curl_exec( $ch );
      curl_close( $ch );
      fclose( $fp );
      echo "<br> Image save success To :".$post_img_newname;
      curl_close( $handle1 ); */
      //eof pixarbay 
    
    }
      if($return_arr_img!='')
      {
    
      return ($return_arr_img);
      }
      else{
        return (0);
      }
    
    
    }
    
    
    
    
    public function  pixarbay_get_image_arr_check($what_check,$keyword_en) {
    
    
      $return_arr_img='';
      //bof image from pixarbay
      $pic_keyword = $keyword_en;
      //select direct step by step
    
    
     //$detect_key_lang=gg_translate_detect($pic_keyword);
    
     /* $pic_keyword=trim($pic_keyword);
     $pic_keyword=str_replace(' ','-',$pic_keyword);
    
     if(!preg_match('/^[a-z]+$/i',$pic_keyword))
     {
      $pic_keyword =  gg_translate("en",$pic_keyword);
     }
     else{
      $pic_keyword = $pic_keyword;
     } */
     
    
    
      $apiKey1 = '31427895-f4f99be1dcd52c1f3ef333a99';
      $url1 = 'https://pixabay.com/api/?key=' . $apiKey1 . '&image_type=photo&order=popular&q=' . $pic_keyword;
    
      $handle1 = curl_init( $url1 );
      curl_setopt( $handle1, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $handle1, CURLOPT_CUSTOMREQUEST, 'GET' );
      $response1 = curl_exec( $handle1 );
      $responseDecoded1 = json_decode( $response1, true );
      $responseCode1 = curl_getinfo( $handle1, CURLINFO_HTTP_CODE );
    
      echo "\n\n <br><br>";
      print_r($responseDecoded1);
      var_dump($responseCode1);
    
      if($response1)
      $pixar_total = $responseDecoded1[ 'total' ];
    
      curl_close( $handle1 );
     
      if($what_check=='totalpool')
      {
      
         $number_return=$pixar_total;
      }
      else
      {
         $number_return=$responseCode1;
      
      }
     
      return ($number_return);
     
     
     }
    
    
    public function  pexels_get_image_arr_check($what_check,$keyword) {
    
    
    
     $url = "https://api.pexels.com/v1/search?query=".$keyword;
    
     $curl = curl_init($url);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     curl_setopt( $handle1, CURLOPT_CUSTOMREQUEST, 'GET' );
     
     $headers = array(
          "X-Custom-Header: header-value",
         "Content-Type: application/json", 
         //"Authorization: 563492ad6f9170000100000114c760fa81a641929cbf9020b619596a"
         "Authorization:563492ad6f91700001000001b84df21b3fb7474c85577e11a6e9b986"
     );
     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($curl, CURLOPT_HEADER, false);
     $response = curl_exec($curl);
     $responseDecoded1 = json_decode( $response,true);
     $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
    
     // total results of photo
     $pexels_total = $responseDecoded1['total_results'];
     curl_close( $curl );
    
     if($what_check=='totalpool')
     {
     
        $number_return=$pexels_total;
     }
     else
     {
        $number_return=$responseCode1;
     
     }
    
     return ($number_return);
    
    
    }
    
    
    
    public function  pexels_get_image_arr( $keyword, $keyword_en, $perpage, $totalpage, $title, $counts, $locale, $postversion, $conn ) {
    
      $return_arr_img='';
     //bof image from pexels
     $pic_keyword = $keyword_en;
     //select direct step by step
    
     //BOF check keywords Pool
     $what_check='totalpool';
     $source_name='pexels';
     $start_id=0;
     $total_pools=pexels_get_image_arr_check($what_check,$keyword);
    
    
    
    
     //loop untill the Number of Total cloud Images Pools  >  Total images was save in Database
     while ($counts>= $total_pools)
     {
         $pic_keyword_arr=get_relate_keyword($pic_keyword,$start_id,$conn);
         $pic_keyword=  $pic_keyword_arr[1];
         $pic_keyword_id=  $pic_keyword_arr[0];
    
         //reset new all value
         $start_id= $pic_keyword_id;
         $new_counts=get_cur_img_count($pic_keyword,$source_name,$conn);
    
         echo "\n\n Debug new Counts ".$new_counts;
    
         echo "\n\n Debug Old Counts ".$counts;
    
         //reset new counts of Keyword
         $counts=$new_counts;
         //check total pool of new keyword
         $total_pools=pexels_get_image_arr_check($what_check,$pic_keyword);
     }
    //EOF check keywords Pool
    
    if ($counts % 79 == 0)
    {
       echo "\n\n Debug Old Counts ".$counts;
       $counts=$counts+1;
    }
    
     if ($counts>=$perpage)
     {
       $pic_page_select  = intval($counts/$perpage) + 1;
    
     }
     else{
       $pic_page_select =1;
     }
    
    
    
     $url = "https://api.pexels.com/v1/search?query=".$pic_keyword."&per_page=".$perpage."&page=".$pic_page_select."&locale=".$locale;
    
     $curl = curl_init($url);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     curl_setopt( $handle1, CURLOPT_CUSTOMREQUEST, 'GET' );
     
     $headers = array(
          "X-Custom-Header: header-value",
         "Content-Type: application/json", 
         //"Authorization: 563492ad6f9170000100000114c760fa81a641929cbf9020b619596a"
         "Authorization:563492ad6f91700001000001b84df21b3fb7474c85577e11a6e9b986"
     );
     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($curl, CURLOPT_HEADER, false);
     $response = curl_exec($curl);
     $responseDecoded1 = json_decode( $response,true);
     $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
     // total results of photo
     $pixar_total = $responseDecoded1['total_results'];
     
     
     $i_position=$perpage - 1 ;
    
     if($counts>=$perpage)
     {
        $i_image =  ($counts%80)+1;
     }
     else
     {
        $i_image =  $counts+1;
     }
    
     echo "<br><br><br> Debug Count : ".$i_image;
     echo "<br><br><br> Eng  Debug Count  ";
    
     //echo for debug
     echo "random image: " . $i_image;
     echo "<br>";
     echo $responseDecoded1[ 'photos' ][ $i_image ]['src'][ 'large' ];
     echo "<br>";
     echo "<br>";
     echo $responseDecoded1[ 'photos' ][ $i_image ]['src'][ 'small' ];
     echo "<br>";
     //echo $responseDecoded1[ 'photos' ][ $i_image ][ 'pageURL' ];
     echo "<br>";
     echo "<br>";
     print_r( $responseDecoded1 );
     print_r( $responseCode1 ); 
    
     $total_pixels=count($responseDecoded1 );
    
      //insert all pic for use next time
      $final_round=$i_image+80;
     foreach  ($responseDecoded1 as $pexels_img)
     {
          echo "\n\n<br>";
         for($k=0; $k< 80 ; $k++)
         {
             if($k>$i_image)
             {
              print_r($pexels_img[$k]);
              echo "\n\n<br> !!!!!! Debug Now in for loop oF ";
              $source_id[$k]=$pexels_img[$k]['id'];
              echo " ".$source_id[$k];
    
              echo " Of Total : ".$total_pixels;
    
              //$source_id_i=$pexels_img[$k]['id'];
    
          // set image name 
          $post_img_newname[$k] = 'wait';
          $post_img_newname[$k] .= '.png';
          
           //add small img to arr
           $source_small[$k]=$pexels_img[$k]['src'][ 'small' ];
          
          //add medium img to arr
          $source_mid[$k]=$pexels_img[$k]['src'][ 'medium' ];
          
            //add largeimg to arr
            $source_large[$k]=$pexels_img[$k]['src'][ 'large' ];
            
    
            //add Author arr
            $source_author[$k]=$pexels_img[$k][ 'photographer' ];
            
            $source_author_id[$k]=$pexels_img[$k][ 'photographer_id' ];
            
            $source_hd[$k]=$pexels_img[$k]['src'][ 'large2x' ];
            
            $source_original[$k]=$pexels_img[$k]['src'][ 'original' ];
            
            $source_tag[$k]=$pexels_img[$k][ 'alt' ];
    
            //return the keyword that use search
            
            //$keyword[$k]=$pic_keyword;
            $edit_post_id_i=0;
            $counts_new_i=0;
            
    
                $insert[$k] = $conn->prepare("INSERT INTO pic_stat (source_id, source, post_image, small, mid, large, author, author_id, counts, post_id, keywords, hd_size, original_size, tag, search_keywords) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                //print_r($insert_i);
              
               if($k>$i_image && strlen($source_small[$k])>2)
               {
                try {
                  $insert[$k]->execute(array( $source_id[$k], $source_name,  $post_img_newname[$k], $source_small[$k], $source_mid[$k], $source_large[$k], $source_author[$k],$source_author_id[$k],$counts_new_i, $edit_post_id_i, $keyword, $source_hd[$k], $source_original[$k], $source_tag[$k], $keyword_en ));
                  echo "<br><br>!!!!!!!!!! Insert Backup Image ".$k ."  success";
                
                } catch(PDOException $e) {
                  echo "<br><br>!!!!!!!!!! Insert Backup Image ".$k ." failed";
                  echo '<br><br> Error: ' . $e->getMessage().'<br><br>';
                }
    
              }
    
    
             }
         }
    
    
         
     } 
     //eof insert all pic for use next time
    
     echo "\n\n<br> End for loop K ";
    // print_r($pexels_img[$k]);
    
     // Remote pixarbay image URL
     $return_arr_img.=$responseDecoded1['photos'][$i_image]['id'];
     $return_arr_img.=',';
    
     echo "<br> Debug arr img : ";
     //print_r($return_arr_img);
    
     $pic_url = $responseDecoded1['photos'][ $i_image ]['src'][ 'medium' ];
     $title_lang=gg_translate_detectv3($title);
     if($title_lang=='en')
     {
        $n_post_title = $title;
     }
     else{
         
      //$n_post_title =  gg_translate("en",$title);
      $n_post_title_arr = v3_translate_text( $title,"en",'valued-fortress-354801');
      $n_post_title =$n_post_title_arr[0];
      $n_post_title .=$postversion;
    
     }
    
    
     $post_img_newname = str_replace( ' ', '-', $n_post_title );
     $post_img_newname = $post_img_newname;
     $post_img_newname =clean_file_title($post_img_newname);
      $post_img_newname = str_replace( '&#39;', '-', $post_img_newname);
     $post_img_newname .= '.jpg';
     
    
     
     //add img name to arr
     echo "\n<br> Debug before remove spcial arr img File Image Name : ";
     echo($post_img_newname);
     echo "\n<br>";
     echo "\n<br>";
     echo "\n<br>";
     //$post_img_newname = $post_img_newname.preg_replace("/[^a-z0-9\_\-\.]/i", '', $post_img_newname);
     
    
     $return_arr_img.=$post_img_newname;
     
     echo "\n<br> Debug after remove spc arr img File Image Name : ";
     print_r($return_arr_img);
     echo "\n<br>";
     echo "\n<br>";
     echo "\n<br>";
    
     $return_arr_img.=',';
    
    
     
    
     //add small img to arr
     $return_arr_img.=$responseDecoded1['photos'][ $i_image ]['src'][ 'small' ];
     $return_arr_img.=',';
     //add medium img to arr
     $return_arr_img.=$responseDecoded1['photos'][ $i_image ]['src'][ 'medium' ];
     $return_arr_img.=',';
      //add largeimg to arr
      $return_arr_img.=$responseDecoded1['photos'][ $i_image ]['src'][ 'large' ];
      $return_arr_img.=',';
    
      //add Author arr
      $return_arr_img.=$responseDecoded1['photos'][ $i_image ][ 'photographer' ];
      $return_arr_img.=',';
      $return_arr_img.=$responseDecoded1['photos'][ $i_image ][ 'photographer_id' ];
      $return_arr_img.=',';
      $return_arr_img.=$responseDecoded1['photos'][ $i_image ]['src'][ 'large2x' ];
      $return_arr_img.=',';
      $return_arr_img.=$responseDecoded1['photos'][ $i_image ]['src'][ 'original' ];
      $return_arr_img.=',';
      $return_arr_img.=$responseDecoded1['photos'][ $i_image ][ 'alt' ];
    
      //return the keyword that use search
      $return_arr_img.=',';
      $return_arr_img.=$pic_keyword;
      //$return_arr_img.=',';
    
    
      
    
      echo "\n<br> Debug Tag of Pexels img : ".$responseDecoded1['photos'][ $i_image ][ 'alt' ];
      
      //$return_arr_img.=',';
    
      echo "\n<br> Debug all array of Pexels img : ";
     //print_r($return_arr_img);

     

     // the new way  S3
     $filePath = 'uploads/posts/' . $post_img_newname; // Added this new line
     if (!Storage::disk('s3')->exists($filePath)) {

       //SMAI new way S3 save image
       $file_content = file_get_contents($pic_url);

       //Save Image to Local server
       $nameOfImage = $post_img_newname;
       Storage::disk('posts')->put($nameOfImage, $file_content);

       // File saved on local disk, save it to S3  
       $filePath = 'uploads/posts/' . $nameOfImage; // Added this new line

       if (Storage::disk('s3')->exists($filePath)) {
           Log::debug("File already exists in S3 ".$filePath);
       } else {
           Storage::disk('s3')->put($filePath, $file_content, 'public');
       }

       // Confirm that file is saved on S3 before deleting it from local disk
       if (Storage::disk('s3')->exists($filePath)) {
           Storage::disk('posts')->delete($nameOfImage);
           Log::debug(" del image success after uploaded to S3 ");
       } else {
           Log::debug(" File not found in S3 maybe upload not success ");
       }
    
        //eof SMAI new way S3 save image

        //Then generate the URL like this:
        $post_img_newname_path = Storage::disk('s3')->url($filePath);
     }

  
    
     // Save image
   /*   $ch = curl_init( $pic_url );
     $fp = fopen( $post_img_newname_path, 'wb' );
     curl_setopt( $ch, CURLOPT_FILE, $fp );
     curl_setopt( $ch, CURLOPT_HEADER, 0 );
     curl_exec( $ch );
     curl_close( $ch );
     fclose( $fp );
     echo "\n<br>";
     echo "\n<br>";
     echo "\n<br> !!!!!! Image save success To :".$post_img_newname;
     echo "\n<br>";
     echo "\n<br>";
     echo "\n<br>";
     curl_close( $curl ); */
     //eof pixarbay 
    
     return ($return_arr_img);
    
    
    }
    
    public function  unsplash_get_image_arr_check($what_check,$keyword_en)
    {
      
     //$apiKey='G6_r0b9oa5ymmtn_nVjtAxKvbKpQz8bDjzAlTGIT2jM';
    /*  $url = "https://api.unsplash.com/search/photos?page=1&query=".$keyword_en;
    
     $url .="&client_id=".$apiKey;
    
     $curl = curl_init($url);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); */
     
    /*   $headers = array(
          "X-Custom-Header: header-value",
         "Content-Type: application/json", 
         //"Authorization: 563492ad6f9170000100000114c760fa81a641929cbf9020b619596a"
         "Authorization: Client-ID G6_r0b9oa5ymmtn_nVjtAxKvbKpQz8bDjzAlTGIT2jM"
         
     ); */
    
    /*  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
     curl_setopt($curl, CURLOPT_HEADER, false);
     $response = curl_exec($curl);
     $responseDecoded1 = json_decode( $response,true);
     $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE ); */
    
    
     $apiKey='G6_r0b9oa5ymmtn_nVjtAxKvbKpQz8bDjzAlTGIT2jM';
     $url = "https://api.unsplash.com/search/photos?page=1&query=".$keyword_en;
    
     $url .="&client_id=".$apiKey;
    
     $curl = curl_init($url);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_HEADER, false);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     
     $response = curl_exec($curl);
     $responseDecoded1 = json_decode( $response,true);
     $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
    
    
    
     // total results of photo
     $unsplash_total = $responseDecoded1['total'];
     curl_close( $curl );
    
     if($what_check=='totalpool')
     {
     
        $number_return=$unsplash_total;
     }
     else
     {
        $number_return=$responseCode1;
     
     }
    
     return ($number_return);
    
    
    
    }
    
    public function  unsplash_get_image_arr( $keyword, $keyword_en, $perpage, $totalpage, $title, $counts, $postversion, $conn ) {
    
    /*   Unsplash\HttpClient::init([
        'applicationId'	=> 'G6_r0b9oa5ymmtn_nVjtAxKvbKpQz8bDjzAlTGIT2jM',
        'secret'	=> 'f_cBRXKQisHCZQvtUjc-uLa4ATSI9tuv7NlzadNmgHE',
        'callbackUrl'	=> 'https://punbot.co/seo',
        'utmSource' => 'SEOASIA.Co'
      ]);
    
      $scopes = ['public', 'write_user'];
    Unsplash\HttpClient::$connection->getConnectionUrl($scopes);
    Unsplash\HttpClient::$connection->generateToken($code);
    
    $test_page=1;
    
    
    $collection = Unsplash\Search::collections($keyword_en,$test_page, $perpage);
    $photos = $collection->photos($page, $per_page);
    
    print_r($photos); */
    
    
    
      $return_arr_img='';
     //bof image from unsplash
     $pic_keyword = $keyword_en;
     //select direct step by step
    
     //BOF check keywords Pool
     $what_check='totalpool';
     $source_name='unsplash';
     $start_id=0;
     $total_pools=unsplash_get_image_arr_check($what_check,$keyword_en);
    
    
    
    
     //loop untill the Number of Total cloud Images Pools  >  Total images was save in Database
     while ($counts>= $total_pools)
     {
         $pic_keyword_arr=get_relate_keyword($pic_keyword,$start_id,$conn);
         $pic_keyword=  $pic_keyword_arr[1];
         $pic_keyword_id=  $pic_keyword_arr[0];
    
         //reset new all value
         $start_id= $pic_keyword_id;
         $new_counts=get_cur_img_count($pic_keyword,$source_name,$conn);
    
         echo "\n\n Debug new Counts ".$new_counts;
    
         echo "\n\n Debug Old Counts ".$counts;
    
         //reset new counts of Keyword
         $counts=$new_counts;
         //check total pool of new keyword
         $total_pools=unsplash_get_image_arr_check($what_check,$pic_keyword);
     }
    //EOF check keywords Pool
    
    if ($counts % 29 == 0)
    {
       echo "\n\n Debug Old Counts ".$counts;
       $counts=$counts+1;
    }
    
     if ($counts>=$perpage)
     {
       $pic_page_select  = intval($counts%$perpage) + 1;
    
     }
     else{
       $pic_page_select =1;
     }
    
    
     $apiKey='G6_r0b9oa5ymmtn_nVjtAxKvbKpQz8bDjzAlTGIT2jM';
     $url = "https://api.unsplash.com/search/collections?query=".$pic_keyword."&per_page=".$perpage."&page=".$pic_page_select;
     $url .="&client_id=".$apiKey;
    
    
     $curl = curl_init($url);
     curl_setopt($curl, CURLOPT_URL, $url);
     curl_setopt($curl, CURLOPT_HEADER, false);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     
     $response = curl_exec($curl);
     $responseDecoded1 = json_decode( $response,true);
     $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
    
     $responseDecoded1 = json_decode( $response,true);
     $responseCode1 = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
     
     // total results of photo
     $unsplash_total = $responseDecoded1['total'];
     
     
     $i_position=$perpage - 1 ;
    
     if($counts>=$perpage)
     {
        $i_image =  ($counts-30)+1;
     }
     else
     {
        $i_image =  $counts+1;
     }
    
     echo "<br><br><br> Debug Count : ".$i_image;
     echo "<br><br><br> Eng  Debug Count  ";
    
     //echo for debug
     echo "random image: " . $i_image;
     echo "<br>";
    /*  echo $responseDecoded1['results'][ $i_image ]['src'][ 'large' ];
     echo "<br>";
     echo "<br>";
     echo $responseDecoded1['results'][ $i_image ]['src'][ 'small' ];
     echo "<br>"; */
     //echo $responseDecoded1['results'][ $i_image ][ 'pageURL' ];
     //echo "<br>";
     echo "<br>";
     print_r( $responseDecoded1 );
     print_r( $responseCode1 ); 
    
     $total_unsplash=count($responseDecoded1 );
    
      //insert all pic for use next time
      $final_round=$i_image+30;
     foreach  ($responseDecoded1 as $pexels_img)
     {
          echo "\n\n<br>";
         for($k=0; $k< 28 ; $k++)
         {
             if($k>$i_image)
             {
              print_r($pexels_img[$k]);
              echo "\n\n<br> !!!!!! Debug Now in for loop oF ";
              $source_id[$k]=$pexels_img[$k]['id'];
              echo " ".$source_id[$k];
    
              echo " Of Total : ".$total_pixels;
    
              //$source_id_i=$pexels_img[$k]['id'];
    
          // set image name 
          $post_img_newname[$k] = 'wait';
          $post_img_newname[$k] .= '.png';
          
           //add small img to arr
           $source_small[$k]=$pexels_img[$k]['urls'][ 'thumb' ];
          
          //add medium img to arr
          $source_mid[$k]=$pexels_img[$k]['urls'][ 'small' ];
          
            //add largeimg to arr
            $source_large[$k]=$pexels_img[$k]['urls'][ 'regular' ];
            
    
            //add Author arr
            $source_author[$k]=$pexels_img[$k][ 'user' ][ 'name' ];
            
            $source_author_id[$k]=$pexels_img[$k][ 'user' ][ 'id' ];
            
            $source_hd[$k]=$pexels_img[$k]['urls'][ 'full' ];
            
            $source_original[$k]=$pexels_img[$k]['urls'][ 'raw' ];
            
            $source_tag[$k]=$pexels_img[$k][ 'description' ];
    
            //return the keyword that use search
            //$keyword[$k]=$pic_keyword;
            $edit_post_id_i=0;
            $counts_new_i=0;
            
    
                $insert[$k] = $conn->prepare("INSERT INTO pic_stat (source_id, source, post_image, small, mid, large, author, author_id, counts, post_id, keywords, hd_size, original_size, tag, search_keywords) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                //print_r($insert_i);
              
               if($k>$i_image && strlen($source_small[$k])>2)
               {
                try {
                  $insert[$k]->execute(array( $source_id[$k], $source_name,  $post_img_newname[$k], $source_small[$k], $source_mid[$k], $source_large[$k], $source_author[$k],$source_author_id[$k],$counts_new_i, $edit_post_id_i, $keyword, $source_hd[$k], $source_original[$k], $source_tag[$k], $keyword_en ));
                  echo "<br><br>!!!!!!!!!! Insert Backup Image ".$k ."  success";
                
                } catch(PDOException $e) {
                  echo "<br><br>!!!!!!!!!! Insert Backup Image ".$k ." failed";
                  echo '<br><br> Error: ' . $e->getMessage().'<br><br>';
                }
    
              }
    
    
             }
         }
    
    
         
     } 
     //eof insert all pic for use next time
    
     echo "\n\n<br> End for loop K ";
    // print_r($pexels_img[$k]);
    
     // Remote pixarbay image URL
     // push arr 0
     $return_arr_img.=$responseDecoded1['results'][$i_image]['id'];
     $return_arr_img.=',';
    
     echo "<br> Debug arr img : ";
     //print_r($return_arr_img);
    
     $pic_url = $responseDecoded1['results'][ $i_image ]['urls'][ 'regular' ];
    
     //$title_lang=gg_translate_detectv3($title);
    $title_lang='en';
     if($title_lang=='en')
     {
        $n_post_title = $title;
     }
     else{
         
      //$n_post_title =  gg_translate("en",$title);
      $n_post_title_arr = v3_translate_text( $title,"en",'valued-fortress-354801');
      $n_post_title =$n_post_title_arr[0];
      $n_post_title .=$postversion;
    
     }
    
    
     $post_img_newname = str_replace( ' ', '-', $n_post_title );
     $post_img_newname = $post_img_newname;
     $post_img_newname =clean_file_title($post_img_newname);
     $post_img_newname = str_replace( '&#39;', '-', $post_img_newname);
     $post_img_newname .= '.jpg';
     
    
     
     //add img name to arr
     echo "\n<br> Debug before remove spcial arr img File Image Name : ";
     echo($post_img_newname);
     echo "\n<br>";
     echo "\n<br>";
     
    // push arr 1
     $return_arr_img.=$post_img_newname;
     $return_arr_img.=',';
     
     echo "\n<br> Debug after remove spc arr img File Image Name : ";
     print_r($return_arr_img);
     echo "\n<br>";
     echo "\n<br>";
    
    
    
    
     
     //add small img to arr
     // push arr 2
     $return_arr_img.=$responseDecoded1['results'][ $i_image ]['urls'][ 'thumb' ];
     $return_arr_img.=',';
          
     //add medium img to arr
     // push arr 3
     $return_arr_img.=$responseDecoded1['results'][ $i_image ]['urls'][ 'small' ];
     $return_arr_img.=',';
     
       //add largeimg to arr
       // push arr 4
       $return_arr_img.=$responseDecoded1['results'][ $i_image ]['urls'][ 'regular' ];
       $return_arr_img.=',';
       
    
       //add Author arr
       // push arr 5
       $return_arr_img.=$responseDecoded1['results'][ $i_image ][ 'user' ][ 'name' ];
       $return_arr_img.=',';
       
       // push arr 6
       $return_arr_img.=$responseDecoded1['results'][ $i_image ][ 'user' ][ 'id' ];
       $return_arr_img.=',';
       
       // push arr 7
       $return_arr_img.=$responseDecoded1['results'][ $i_image ]['urls'][ 'full' ];
       $return_arr_img.=',';
       
       // push arr 8
       $return_arr_img.=$responseDecoded1['results'][ $i_image ]['urls'][ 'raw' ];
       $return_arr_img.=',';
       
       // push arr 9
       $return_arr_img.=$responseDecoded1['results'][ $i_image ][ 'description' ];
       $return_arr_img.=',';
    
       // push arr 10
       $return_arr_img.='';
       $return_arr_img.=',';
    
       // push arr 11
       $return_arr_img.='';
       $return_arr_img.=',';
    
      //return the keyword that use search
      // push arr 12
      $return_arr_img.=',';
      $return_arr_img.=$pic_keyword;
      //$return_arr_img.=',';
    
    
      
    
      //Log::debug("\n<br> Debug Tag of Unsplash img : ".$responseDecoded1['results'][ $i_image ][ 'description' ]);
      //$return_arr_img.=',';
      //Log::debug ("\n<br> Debug all array of UnSpash img : ");
      print_r($return_arr_img);
    

        // the new way  S3
      $filePath = 'uploads/posts/' . $post_img_newname; // Added this new line
      if (!Storage::disk('s3')->exists($filePath)) {

        //SMAI new way S3 save image
        $file_content = file_get_contents($pic_url);

        //Save Image to Local server
        $nameOfImage = $post_img_newname;
        Storage::disk('posts')->put($nameOfImage, $file_content);

        // File saved on local disk, save it to S3  
        $filePath = 'uploads/posts/' . $nameOfImage; // Added this new line

        if (Storage::disk('s3')->exists($filePath)) {
            Log::debug("File already exists in S3 ".$filePath);
        } else {
            Storage::disk('s3')->put($filePath, $file_content, 'public');
        }

        // Confirm that file is saved on S3 before deleting it from local disk
        if (Storage::disk('s3')->exists($filePath)) {
            Storage::disk('posts')->delete($nameOfImage);
            Log::debug(" del image success after uploaded to S3 ");
        } else {
            Log::debug(" File not found in S3 maybe upload not success ");
        }
     
         //eof SMAI new way S3 save image

         //Then generate the URL like this:
         $post_img_newname_path = Storage::disk('s3')->url($filePath);
      }

    
     // Save image
  /*    $ch = curl_init( $pic_url );
     $fp = fopen( $post_img_newname_path, 'wb' );
     curl_setopt( $ch, CURLOPT_FILE, $fp );
     curl_setopt( $ch, CURLOPT_HEADER, 0 );
     curl_exec( $ch );
     curl_close( $ch );
     fclose( $fp );
     echo "\n<br>";
     echo "\n<br>";
     echo "\n<br> !!!!!! Image save success To :".$post_img_newname;
     echo "\n<br>";
     echo "\n<br>";
     echo "\n<br>";
     curl_close( $curl ); */
     //eof pixarbay 
    
     return ($return_arr_img);
    
    
    }
    
    public function  get_credit_of_image($picid,$conn)
    {
      $statement = $conn->prepare( "SELECT * FROM pic_stat WHERE id = ? ORDER BY id ASC LIMIT 1" );
        $statement->execute( array($picid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      $return_arr=array();
        $last_id=$result['id'];
      $img_source=$result['source'];
      $img_author=$result['author'];
      array_push($return_arr,$img_source,$img_author);
    
      //$col_value=$result[$col];
      
    
        return ($return_arr);
    
    
    }
    
    public function  get_image_id_fromPost($post_id,$conn)
    {
    
      $statement = $conn->prepare( "SELECT * FROM pic_stat WHERE post_id = ? ORDER BY id ASC LIMIT 1" );
        $statement->execute( array($post_id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      $return_arr=array();
         $last_id=$result['id'];
      /*
      $img_source=$result['source'];
      $img_author=$result['author'];
      array_push($return_arr,$img_source,$img_author); */
    
      //$col_value=$result[$col];
      
    
        return ($last_id);
    
    
    }
    
    
    public function  get_image_detail($picid,$col,$conn)
    {
      $statement = $conn->prepare( "SELECT * FROM pic_stat WHERE id = ? ORDER BY id ASC LIMIT 1" );
        $statement->execute( array($picid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      $return_arr=array();
        /* $last_id=$result['id'];
      $img_source=$result['source'];
      $img_author=$result['author'];
      array_push($return_arr,$img_source,$img_author); */
    
      $col_value=$result[$col];
      
    
        return ($col_value);
    
    
    }
    
    
    
    
    //working
    public function  post_to_blog($to_email,$titile,$body) {
    
      $to = $to_email;
      $from = 'info@punbot.co';
      $fromName = 'SEOAsia PUNBOT';
    
      $subject = $titile;
    
      $htmlContent = $body;
    
      // Set content-type header for sending HTML email 
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
      // Additional headers 
      $headers .= 'From: ' . $fromName . '<' . $from . '>' . "\r\n";
      //$headers .= 'Cc: welcome@example.com' . "\r\n";
      //$headers .= 'Bcc: welcome2@example.com' . "\r\n";
    
      // Send email 
        $send_result='';
      if ( mail( $to, $subject, $htmlContent, $headers ) ) {
        Log::debug('Email has sent successfully.');
          $send_result.='Email has sent successfully.';
          
      } else {
          echo 'Email sending failed.';
          $send_result.='Email sending failed.';
          $e=error_get_last();
            if($e['message']!==''){
               $send_result.=' with Error : '.$e['message'];
            }
        
      }
        
        return ($send_result);
    
    
    }
    
    //working
    public function  get_last_post_id($table,$id_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM $table ORDER BY $id_field DESC LIMIT 1" );
        $statement->execute( array() );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
        return ($last_id);
        
    }
    
    public function  get_last_post_id_for_tran($table,$id_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM posts WHERE post_version LIKE 'v2-en' AND translated = 0 ORDER BY post_id DESC LIMIT 1" );
        $statement->execute( array() );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['post_id'];
        return ($last_id);
        
    }
    
    public function  get_last_post_id_for_spin($table,$id_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM $table WHERE  post_version = 'original' AND spin = 0 ORDER BY $id_field DESC LIMIT 1" );
        $statement->execute( array() );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
        return ($last_id);
        
    }
    
    public function  get_last_post_id_for_linkdec($table,$id_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM $table WHERE  ( (post_version NOT LIKE 'en' AND post_version NOT LIKE 'original' )) AND link_dec = 0 ORDER BY $id_field DESC LIMIT 1" );
        $statement->execute( array() );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
        return ($last_id);
        
    }
    
    
    public function  get_last_post_id_for_share($table,$id_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM posts WHERE post_version != 'original' AND link_dec > 0 AND shared = 0 ORDER BY post_id DESC LIMIT ?" );
        $statement->execute( array( '1' ) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
        return ($last_id);
        
    }
    
    //working
    public function  get_last_post_for_share($table,$id_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM posts WHERE post_version != 'original' AND link_dec > 0 AND shared = 0 ORDER BY post_id DESC LIMIT 1" );
        $statement->execute( array() );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
        return ($last_id);
        
    }
    
    //working
    public function  get_last_post_for_share_withsiteid($siteid,$conn)
    {
        //post_version LIKE '%en%' AND
        //$statement = $conn->prepare( "SELECT * FROM posts WHERE post_version != 'original' AND link_dec > 0 AND shared = 0 AND website_id = ? AND post_image != 'default.png' ORDER BY post_id DESC LIMIT 1" );
        
      $statement = $conn->prepare( "SELECT * FROM posts WHERE post_version != 'original' AND post_version NOT LIKE '%th%' AND link_dec > 0 AND shared = 0 AND website_id = ? AND post_image != 'default.png' ORDER BY post_id DESC LIMIT 1" );
      $statement->execute( array($siteid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['post_id'];
        return ($last_id);
        
    }
    
    //working
    public function  get_last_MainPost_for_share_withsiteid($siteid,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM posts WHERE post_version LIKE '%th%' AND post_version != 'original' AND link_dec > 0 AND shared = 0 AND website_id = ? AND post_image != 'default.png' ORDER BY post_id DESC LIMIT 1" );
        $statement->execute( array($siteid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
      if($total>0)
      {
    
        $last_id=$result['post_id'];
        return ($last_id);
      }
      else{
        
        return 0;
      }
        
    }
    
    
    //working
    public function  get_last_post_for_share_withsiteid_lang($table,$id_field,$siteid,$lang,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM posts WHERE post_version != 'original' AND link_dec > 0 AND shared = 0 AND website_id = ? AND post_version LIKE %?%  AND post_image != 'default.png'  ORDER BY post_id DESC LIMIT 1" );
        $statement->execute( array($siteid,$lang) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
        return ($last_id);
        
    }
    
    
    
    public function  get_backlink_col_for_share($linkid,$col,$conn)
    {
        
      $share_id_email_keyword_array=array();
        $statement = $conn->prepare( "SELECT * FROM backlinks_option WHERE id = ? ORDER BY id ASC LIMIT 1" );
        $statement->execute( array($linkid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
      if($total>0)
      {
        $last_id=$result['id'];
      $share_email=$result['post_to_email'];
      $share_keyword=$result['keyword'];
    
      $col_value=$result[$col];
      }
      else{
        $col_value=NULL;
    
      }
      
    
        return ($col_value);
        
    }
    
    
    public function  get_backlink_col_for_share_main($table,$col,$conn)
    {
        
      $share_id_email_keyword_array=array();
        $statement = $conn->prepare( "SELECT * FROM backlinks_option WHERE bl_type = ? AND  counts =  ( SELECT MIN(counts) FROM backlinks_option ) ORDER BY id ASC LIMIT 1" );
        $statement->execute( array('main') );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
      $share_email=$result['post_to_email'];
      $share_keyword=$result['keyword'];
    
      $col_value=$result[$col];
      
    
        return ($col_value);
        
    }
    
    //bug to fix
    public function  get_backlink_for_share($table,$siteid,$conn)
    {
        
      $share_id_email_keyword_array=array();
        $statement = $conn->prepare( "SELECT *
      FROM backlinks_option
      WHERE website_id = ? 
      AND bl_type = 'backlink'
      AND counts =  ( SELECT MIN(counts) FROM backlinks_option )
      ORDER BY id DESC LIMIT 1" );
    
        $statement->execute( array($siteid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      var_dump( $result );
    
      if($total>0)
      {
        $last_id=$result['id'];
      $share_email=$result['post_to_email'];
      $share_keyword=$result['keyword'];
      }
      else{
        $last_id=0;
      }
      
        return ($last_id);
        
    }
    
    //bug to fix
    public function  get_backlink_for_share_main($siteid,$conn)
    {
        
      $bl_type='main';
      $share_id_email_keyword_array=array();
        $statement = $conn->prepare( "SELECT *
      FROM backlinks_option
      WHERE website_id = ? 
      AND bl_type = 'main'
      AND counts =  ( SELECT MIN(counts) FROM backlinks_option )
      ORDER BY id DESC LIMIT 1" );
    
        $statement->execute( array($siteid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
      var_dump( $result );
      print_r($statement->errorInfo());
    
        if($total>0)
      {
        $last_id=$result['id'];
      $share_email=$result['post_to_email'];
      $share_keyword=$result['keyword'];
      }
      else{
        $last_id=0;
      }
      
        return ($last_id);
        
    }
    
    
    
    
    
    //Working
    public function  get_universalLink_for_share($bl_type,$siteid,$conn)
    {
        
      //$bl_type='main';
      $share_id_email_keyword_array=array();
        $statement = $conn->prepare( "SELECT *
      FROM `backlinks_option`
      WHERE `backlinks_option`.`website_id` = ? 
      AND `backlinks_option`.`bl_type` = ?
      AND `backlinks_option`.`round_num` > `backlinks_option`.`round_count`
      ORDER BY id DESC LIMIT 1" );
    
        $statement->execute( array($siteid, $bl_type) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
    
      var_dump( $result );
      
    
        if($total>0)
      {
        $last_id=$result['id'];
      $share_email=$result['post_to_email'];
      $share_keyword=$result['keyword'];
      }
      else{
        $last_id=0;
        print_r($statement->errorInfo());
      }
      
        return ($last_id);
        
    }
    
    
    
    
    
    public function  get_cur_website_keyword($table,$id_field,$website_id,$search_key,$key_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM ? WHERE  website_id = ? AND keyword_en = ?  ORDER BY ? ASC LIMIT 1" );
        $statement->execute( array( $table, $website_id, $search_key, $id_field ) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $first_id=$result[$id_field];
        $target_keyword_tran=$result[$key_field];
        
        
        return ($target_keyword_tran);
        
    }
    
    public function  get_cur_keywordlink($website_id,$search_key,$conn)
    {
      if($conn==NULL)
      $conn=$this->conn;

        
        $statement = $conn->prepare( "SELECT * FROM websites_option WHERE  website_id = ? AND keyword = ?  ORDER BY id DESC LIMIT 1" );
        //$statement->execute( array( $table, $website_id, $search_key, ) );
      $statement->execute( array( $website_id, $search_key ) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      if($total>0)
      {
        $first_id=$result['id'];
      $target_keyword_tran=$result['keyword'];
      $cur_link_footer=$result['footer_link'];
      $keyword_link=$result['url'];
    
      }
      else{
    
        $keyword_link=0;
    
      }
        
        
        return ($keyword_link);
        
    }
    
    //
    public function  get_cur_linkversion($website_id,$search_key,$conn)
    {
      Log::debug("\n\n<br> Debug Link Version Keyword : ".$search_key);
        
        $statement = $conn->prepare( "SELECT * FROM websites_option WHERE  website_id = ? AND keyword = ?  ORDER BY id DESC LIMIT 1" );
        //$statement->execute( array( $table, $website_id, $search_key, ) );
      $statement->execute( array( $website_id, $search_key ) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $first_id=$result['id'];
      $target_keyword_tran=$result['keyword'];
      $cur_link_footer=$result['footer_link'];
        
        Log::debug("\n\n<br> Debug Link Version : ".$cur_link_footer);
        return ($cur_link_footer);
        
    }
    
    
    public function  get_cur_keyword($table,$website_id,$search_key,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM websites_option WHERE  website_id = ? AND keyword_en = ?  ORDER BY id DESC LIMIT 1" );
        //$statement->execute( array( $table, $website_id, $search_key, ) );
      $statement->execute( array( $website_id, $search_key ) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $first_id=$result['id'];
      $target_keyword_tran=$result['keyword'];
        
        
        return ($target_keyword_tran);
        
    }
    
    //synced to APICRM
    public function  get_cur_keyword_en($website_id,$main_keyword,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM websites_option WHERE  website_id = ? AND keyword = ?  ORDER BY id DESC LIMIT 1" );
        //$statement->execute( array( $table, $website_id, $main_keyword, ) );
      $statement->execute( array( $website_id, $main_keyword ) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $first_id=$result['id'];
      $target_keyword_en=$result['keyword_en'];
        
        
        return ($target_keyword_en);
        
    }
    
    //temp suspend
    public function  get_cur_bl_count($table,$id,$id_field, $count_field,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM backlinks_option WHERE id = ? ORDER BY counts DESC LIMIT 1" );
        $statement->execute( array($id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result[$id_field];
      $current_count=$result[$count_field];
        return ($current_count);
        
    }
    
    //working
    public function  get_cur_bl_type($id,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM backlinks_option WHERE id = ? ORDER BY counts DESC LIMIT 1" );
        $statement->execute( array($id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['id'];
      $current_type=$result['bl_type'];
        return ($current_type);
        
    }
    
    //working
    public function  get_cur_count($id,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM backlinks_option WHERE id = ? ORDER BY counts DESC LIMIT 1" );
        $statement->execute( array($id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['id'];
      $current_count=$result['counts'];
        return ($current_count);
        
    }
    
    //working
    public function  get_cur_siteid_inq($id,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM websites_option WHERE website_id = ? AND active = 1 ORDER BY id DESC LIMIT 1" );
        $statement->execute( array($id) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['id'];
      $current_count=$result['inq'];
      $current_keyword=$result['keyword'];
      $current_keyword_act=$result['active'];
      $current_keyword_footer=$result['footer_link'];
      $current_keyword_en=$result['keyword_en'];
      $current_keyword_link=$result['url'];
    
        return ($last_id.",".$current_count.",".$current_keyword.",".$current_keyword_act.",".$current_keyword_footer.",".$current_keyword_en.",".$current_keyword_link);
        
    }
    
    //working
    public function  get_cur_siteid_keyword($siteid,$keywords,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM websites_option WHERE website_id = ? AND keyword LIKE ?  ORDER BY id ASC LIMIT 1" );
        $statement->execute( array($siteid, $keywords) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['id'];
      $current_count=$result['inq'];
      $current_keyword=$result['keyword'];
      $current_keyword_act=$result['active'];
      $current_keyword_footer=$result['footer_link'];
      $current_keyword_en=$result['keyword_en'];
      $current_keyword_link=$result['url'];
    
        return ($last_id.",".$current_count.",".$current_keyword.",".$current_keyword_act.",".$current_keyword_footer.",".$current_keyword_en.",".$current_keyword_link);
        
    }
    
    //working
    public function  get_cur_siteid_keyid($keyid,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM websites_option WHERE id = ?  ORDER BY id ASC LIMIT 1" );
        $statement->execute( array($keyid) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $last_id=$result['id'];
      $current_count=$result['inq'];
      $current_keyword=$result['keyword'];
      $current_keyword_act=$result['active'];
      $current_keyword_footer=$result['footer_link'];
      $current_keyword_en=$result['keyword_en'];
      $current_keyword_link=$result['url'];
    
        return ($last_id.",".$current_count.",".$current_keyword.",".$current_keyword_act.",".$current_keyword_footer.",".$current_keyword_en.",".$current_keyword_link);
        
    }
    
    
    public function  get_cat($key_en,$conn)
    {
        
        $statement = $conn->prepare( "SELECT * FROM post_categories WHERE category_name = ? ORDER BY category_id ASC LIMIT 1" );
        $statement->execute( array( $key_en ) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
        $cat_id=$result['category_id'];
        return ($cat_id);
        
    }
    
    
    //working
    public function  get_cur_img_count($keyword, $source_name, $conn)
    {
        
        //$statement = $conn->prepare( "SELECT * FROM `pic_stat` WHERE `counts` != '0' AND `source` LIKE ? AND `keywords` LIKE ? ORDER BY id DESC LIMIT 1" );
        
      $statement = $conn->prepare( "SELECT * FROM `pic_stat` WHERE `source` LIKE ? AND  `post_image` NOT LIKE 'wait.png' AND `keywords` LIKE ? ORDER BY `counts` DESC" );
      
      $statement->execute( array($source_name, $keyword) );
        $total = $statement->rowCount();
        $result = $statement->fetch( PDO::FETCH_ASSOC );
      if($total>0)
      {
        $last_id=$result['id'];
      $current_count=$result['counts'];
        return ($total);
      }
      else{
        return (0);
      }
        
    }
    
    //Working
    public function  get_footer_link($linkv,$body_post,$conn)
    {
      $statement_fl = $conn->prepare("SELECT * FROM posts WHERE post_id=?");
      $statement_fl->execute(array($linkv));
      $total_fl  = $statement_fl->rowCount();
      $result_fl = $statement_fl->fetch(PDO::FETCH_ASSOC);
      $return_link_footer='';
      if( $total_fl == 0 ) {
     
          //echo "<br> No Link version found<br>";
          $return_link_footer.='';
      }
      else{
          $aa = extract($result_fl,EXTR_PREFIX_ALL, "add");
    
          if($add_post_description!=0)
          {
            if (strpos($body_post, $add_post_description) !== false) {
    
                  $return_link_footer.='';
            }
            else{
    
    
              $return_link_footer.='<br><br>';
              $return_link_footer.=$add_post_description;
            }
          }
          else{
            $return_link_footer.='';
          }
    
      } 
    
    return($return_link_footer);
     
    
    }

    public function link_dec_seo($content,$keyword,$siteid,$conn=NULL)
    {
        if($conn==NULL)
        $conn=$this->conn;

        $final_conclusion='';
        $post_description=$content; 
        //search and replace Keyword with link <a></a>
        // $post_description=str_replace('น้ำหอม','<a href="https://ceresaperfume.com">น้ำหอม</a>',$content,2); 
        

        $site_option_arr=explode(",", $this->get_cur_siteid_keyword($siteid,$keyword,$conn));
        $link_keyword=$site_option_arr[6];


					if (strpos($content, $keyword) !== false) {
						Log::debug('<br> Found Keyword in BOdy Post');
						$final_conclusion.='\n\n 2.Found Keyword in Post BOdy ';
					

					$post_description = $this->str_replace_SomeKeyword($keyword, '<a href="'.$link_keyword.'">'.$keyword.'</a>', $content,2); 
				}   
				else{
					Log::debug('<br> Not Found Keyword in BOdy Post');
					$final_conclusion.='\n\n 2.2 Not Found Keyword in Post BOdy ';



					$post_description='<a href="'.$link_keyword.'">'.$keyword.'</a> '.$edit_post_description;
				}
        
            
        //add footer link  

        //re-check Linkversion
        $linkv=$this->get_cur_linkversion($siteid,$keyword,$conn);
		
			  if($linkv>0)
			  {
                $final_conclusion.='\n\n 3. Found Link Version in Table Posts ';
                $statement_fl = $conn->prepare("SELECT * FROM posts WHERE post_id=?");
                $statement_fl->execute(array($linkv));
                $total_fl  = $statement_fl->rowCount();
                $result_fl = $statement_fl->fetch(PDO::FETCH_ASSOC);
                if( $total_fl == 0 ) {
                
                  Log::debug( "<br> No Link version found<br>");
                }
                else{
                  $aa = extract($result_fl,EXTR_PREFIX_ALL, "add");
                } 
                
                    $post_description.='<br><br>'; 
                   // $post_description.=$add_post_description;
        }
        else{

                $post_description.='<br><br>'; 
              }



       return($post_description);
    }

    public  function slugify($text) {
      $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
      $text = trim($text, '-');
      if (function_exists('transliterator_transliterate')) $text = transliterator_transliterate('Any-Latin; Latin-ASCII', $text);
      $text = iconv('utf-8', 'ASCII//TRANSLIT//IGNORE', $text);
      $text = strtolower($text);
      $text = preg_replace('~[^-\w]+~', '', $text);
  
      return $text;
  }
    
 

}
