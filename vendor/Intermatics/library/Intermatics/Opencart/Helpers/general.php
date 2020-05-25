<?php

function getSetting($key){
    $setting = \Application\Entity\Setting::where('key',trim($key))->first();
    if($setting){
        return $setting->value;
    }
    else{
        return null;
    }
}

function __($key,$params=[]){

    $key = str_ireplace(' ','-',$key);
    $key = strtolower($key);

    return lang('default.'.$key,$params);
}

function lang($key,$params=[]){
    //get language from setting
    $lang = getSetting('config_language');
    if(empty($lang)){
        $lang = 'en';
    }

    //explode key
    $path = explode('.',$key);
    $file = 'data/lang/'.$lang.'/'.$path[0].'.php';

    if(!file_exists($file)){

        return $key;
    }

    $languageArray = include $file;

    if(isset($languageArray[$path[1]])){
        $translation = $languageArray[$path[1]];

        if(is_array($translation)){
            return $key;
        }

        //now loop through params
        foreach($params as $key=>$value){
            $translation = str_replace(':'.$key,$value,$translation);
        }
        return $translation;
    }
    else{
        return $key;
    }

}

function listdir_by_date($path){
    $dir = opendir($path);
    $list = array();
    while($file = readdir($dir)){
        if ($file != '.' and $file != '..'){
            // add the filename, to be sure not to
            // overwrite a array key
            $ctime = filectime($data_path . $file) . ',' . $file;
            $list[$ctime] = $file;
        }
    }
    closedir($dir);
    krsort($list);
    return $list;
}


/**
 * @param string $message
 * @param array $map
 */
function setPlaceHolders($message,$map){
   
   
    foreach ($map as $key=>$value){
        
        $key = '['.$key.']';
        
        $message = str_replace($key,$value,$message);
        
    }
    
    return $message;
    
}

function sessionType($type){
    $stype = '';
    switch ($type){
        case 'b':
            $stype = 'Training Session With Online Classes';
            break;
        case 'c':
            $stype ='Online Course';
            break;
        case 's':
            $stype = 'Training Session';
            break;
            }
    return $stype;
}
function removeTags($data){
    foreach($data as $key=>$value){
        if(is_string($value)){
            $data[$key] = strip_tags($value);
        }

    }
    return $data;
}

function forceSSL()
{

    $env = getenv('APP_MODE');
    if($env=='live'  && $_SERVER['SERVER_PORT'] != '443') {
        //	header("HTTP/1.1 301 Moved Permanently");
        header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        exit();
    }


}

function noSSL()
{

    $env = getenv('APP_MODE');
    if($env=='live'  && $_SERVER['SERVER_PORT'] == '443') {
        //	header("HTTP/1.1 301 Moved Permanently");
        $url =$_SERVER['REQUEST_URI'];
            $append = (substr_count($url,'?')>0)? 'nossl=1':'?nossl=1';
        header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$append);
        exit();
    }


}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function boolToString($val){
    if($val==0){
        return __('no');
    }
    else{
        return __('yes');
    }
}

function sanitize($string) {
    $replace="_";
    $pattern="/([[:alnum:]_.-]*)/";
    $fname=str_replace(str_split(preg_replace($pattern,$replace,$string)),$replace,$string);
    return $fname;
}

function isImage($file){
    if(empty($file)){
        return false;
    }
    $extensions = ['jpg','gif','png','jpeg'];
    $parts = pathinfo($file);

    if(!isset($parts['extension'])){
        return false;
    }
    $ext = strtolower($parts['extension']);
    if(in_array($ext,$extensions)){

        return true;
    }
    else{

        return false;
    }
    /*
    $validator = new \Zend\Validator\File\IsImage();
    if ($validator->isValid($file)) {
        echo 'true';
        return true;
    }
    else{
        echo 'false';
        return false;
    }
    */
}


function isUrl($string){
    if(preg_match('#http://#',$string) || preg_match('#https://#',$string)){
        return true;
    }
    else{
        return false;
    }
}

function sanitizeFile($cmd, $result, $args, $elfinder) {
    // do something here
    $files = $result['added'];
    foreach ($files as $file) {
        $filename = sanitize($file['name']);
        $arg = array('target' => $file['hash'], 'name' => $filename);
       // $elfinder->exec('rename', $arg);
        $filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
        $path_parts = pathinfo($filepath);

        rename($filepath,$path_parts['dirname'].'/'.$filename);

    }

    return true;
}

function fullUrl($url){
    if(!preg_match('#://#',$url)){
        $url = 'http://'.$url;
    }
    return $url;
}

function safeUrl($url) {

    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
    $url = trim($url, "-");
    $url = @iconv("utf-8", "us-ascii//TRANSLIT", $url);
    $url = strtolower($url);
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
    return $url;
}
function getFirstDigit($string){
    $pos = strpos($string,'_');
    $id = substr($string,0,$pos);
    return $id;
}
function getNumbersOnly($text)
{

    //remove any whitespace
    $text = str_replace(' ','',$text);
    $text = trim($text);
    $text = str_replace(',','',$text);

    $array = str_split($text);
    $amount = array();
    $counter = 0;
    foreach ($array as $value)
    {
        if (is_numeric($value))
        {
            @$amount[$counter] .= $value;
        }
        else
        {
            //$counter++;
        }


    }

    $price = @$amount[0];
    return $price;
}
function cleanTel($text){
    $text = str_ireplace('o','0',$text);
   // $text = str_ireplace('+234','0',$text);
    $text = getNumbersOnly($text);
    return $text;

}

function getPhoneNumber($text){

    //remove any whitespace
    $text = str_replace(' ','',$text);
    $text = trim($text);
    $text = str_replace(',','',$text);

    $array = str_split($text);
    $amount = array();
    $counter = 0;
    foreach ($array as $value)
    {
        if (is_numeric($value) || $value=='+')
        {
            @$amount[$counter] .= $value;
        }
        else
        {
            //$counter++;
        }


    }

    $price = @$amount[0];
    return $price;
}

function convert_number_to_words($number) {

	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
	);

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		trigger_error(
		'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
		E_USER_WARNING
		);
		return false;
	}

	if ($number < 0) {
		return $negative . convert_number_to_words(abs($number));
	}

	$string = $fraction = null;

	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . convert_number_to_words($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= convert_number_to_words($remainder);
			}
			break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}

	return $string;
}

function limitLength($string,$length=100)
{
	if (strlen($string) > $length) {
		$string = substr($string, 0,$length).'...';
	}
	return $string;
}

function showDate($format,$date){
    if(!empty($date)){
        return date($format,$date);
    }
    else{
        return '';
    }
}

function resizeImage($filename, $width, $height,$basePath) {

    $filename = urldecode($filename);
	$dirImage = 'public/tmp/';
	$baseDir = 'public/';
	if (!file_exists($baseDir . $filename) || !is_file($baseDir . $filename)) {
		 
		return;
	}
	 
	$info = pathinfo($filename);

	$extension = $info['extension'];

	$old_image = $filename;
	$new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

	if (!file_exists($dirImage . $new_image) || (filemtime($baseDir . $old_image) > filemtime($dirImage . $new_image))) {
		$path = '';

		$directories = explode('/', dirname(str_replace('../', '', $new_image)));

		foreach ($directories as $directory) {
			$path = $path . '/' . $directory;

			if (!file_exists($dirImage . $path)) {
				@mkdir($dirImage . $path, 0777);
			}
		}

		$image = new \Intermatics\Opencart\Library\Image($baseDir . $old_image);

		$image->resize($width, $height);
		$image->save($dirImage . $new_image);
	}

	 
	return $basePath.'/tmp/'. $new_image;
}

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir")
					rrmdir($dir."/".$object);
				else unlink   ($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}
function convertJsonBody() {
    $methodsWithDataInBody = array(
        'POST',
        'PUT',
    );

    if (
        isset($_SERVER['CONTENT_TYPE'])
        && (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== FALSE)
        && isset($_SERVER['REQUEST_METHOD'])
        && in_array($_SERVER['REQUEST_METHOD'], $methodsWithDataInBody)
    ) {
        $_POST = json_decode(file_get_contents('php://input'), TRUE);
        foreach($_POST as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
}

function getApiStudent($request){
    $authToken = $request->getHeaderLine('Authorization');
    if(!empty($authToken)){
        $student = \Application\Entity\Student::where('api_token',$authToken)->first();
    }
    else{
        $student = false;
    }

}



/**
 * Gets the complete url of
 * the current script
 * @return string
 */
function selfURL() {
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}

function isValidUpload($file){
    $allowed_types = array ('application/zip', 'application/pdf', 'image/jpeg', 'image/png','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.openxmlformats-officedocument.presentationml.presentation');
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $detected_type = finfo_file( $fileInfo, $file );
    finfo_close( $fileInfo );
    if ( !in_array($detected_type, $allowed_types) ) {

        return false;
    }
    else{
        return true;
    }
}

function getExtensionForMime($file){
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $detected_type = finfo_file( $fileInfo, $file );
    finfo_close( $fileInfo );

    $extensions = array('image/jpeg' => 'jpg',
        'application/pdf' => 'pdf',
        'image/png'=>'png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx',
        'application/msword'=>'doc',
        'application/vnd.ms-excel'=>'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'=>'pptx',
        'application/zip'=>'zip'
    );

    return $extensions[$detected_type];
}

function crop_img($imgSrc){
    //getting the image dimensions
    list($width, $height) = getimagesize($imgSrc);

    //saving the image into memory (for manipulation with GD Library)
    $myImage = imagecreatefromjpeg($imgSrc);

    // calculating the part of the image to use for thumbnail
    if ($width > $height) {
        $y = 0;
        $x = ($width - $height) / 2;
        $smallestSide = $height;
    } else {
        $x = 0;
        $y = ($height - $width) / 2;
        $smallestSide = $width;
    }

    // copying the part into thumbnail
    $thumbSize = min($width,$height);
    $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
    imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

    unlink($imgSrc);
    imagejpeg($thumb,$imgSrc);
    @imagedestroy($myImage);
    @imagedestroy($thumb);
}

function isMobileApp(){

    $session = new \Zend\Session\Container('client');
    if($session->type=='mobile'){
        return true;
    }
    else{
        return false;
    }
}

function isTrainEasySubdomain(){
    $url = selfURL();
    if(substr_count($url,'traineasy.net')>0){
        return true;
    }
    else{
        return false;
    }
}

function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}


function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => __('year'),
        'm' => __('month'),
        'w' => __('week'),
        'd' => __('day'),
        'h' => __('hour'),
        'i' => __('minute'),
        's' => __('second'),
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' '.__('ago') : __('just now');
}

function strleft($s1, $s2) {
    return substr($s1, 0, strpos($s1, $s2));
}

function getClientIp() {
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
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function booleanValue($val){
    if($val=='true'){
        return true;
    }
    elseif($val=='false'){
        return false;
    }
    else{
        return $val;
    }
}

function removeScriptTags($html){
    return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
}


function getFileMimeType($file){
       if(!function_exists('mime_content_type')) {

           function mime_content_type($filename) {

               $mime_types = array(

                   'txt' => 'text/plain',
                   'htm' => 'text/html',
                   'html' => 'text/html',
                   'php' => 'text/html',
                   'css' => 'text/css',
                   'js' => 'application/javascript',
                   'json' => 'application/json',
                   'xml' => 'application/xml',
                   'swf' => 'application/x-shockwave-flash',
                   'flv' => 'video/x-flv',

                   // images
                   'png' => 'image/png',
                   'jpe' => 'image/jpeg',
                   'jpeg' => 'image/jpeg',
                   'jpg' => 'image/jpeg',
                   'gif' => 'image/gif',
                   'bmp' => 'image/bmp',
                   'ico' => 'image/vnd.microsoft.icon',
                   'tiff' => 'image/tiff',
                   'tif' => 'image/tiff',
                   'svg' => 'image/svg+xml',
                   'svgz' => 'image/svg+xml',

                   // archives
                   'zip' => 'application/zip',
                   'rar' => 'application/x-rar-compressed',
                   'exe' => 'application/x-msdownload',
                   'msi' => 'application/x-msdownload',
                   'cab' => 'application/vnd.ms-cab-compressed',

                   // audio/video
                   'mp3' => 'audio/mpeg',
                   'qt' => 'video/quicktime',
                   'mov' => 'video/quicktime',

                   // adobe
                   'pdf' => 'application/pdf',
                   'psd' => 'image/vnd.adobe.photoshop',
                   'ai' => 'application/postscript',
                   'eps' => 'application/postscript',
                   'ps' => 'application/postscript',

                   // ms office
                   'doc' => 'application/msword',
                   'rtf' => 'application/rtf',
                   'xls' => 'application/vnd.ms-excel',
                   'ppt' => 'application/vnd.ms-powerpoint',

                   // open office
                   'odt' => 'application/vnd.oasis.opendocument.text',
                   'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
               );

               $ext = strtolower(array_pop(explode('.',$filename)));
               if (array_key_exists($ext, $mime_types)) {
                   return $mime_types[$ext];
               }
               elseif (function_exists('finfo_open')) {
                   $finfo = finfo_open(FILEINFO_MIME);
                   $mimetype = finfo_file($finfo, $filename);
                   finfo_close($finfo);
                   return $mimetype;
               }
               else {
                   return 'application/octet-stream';
               }
           }
       }
        return mime_content_type($file);
}

function forumUser($id,$type){

   $user = null;
    if($type=='s'){
        $user = \Application\Entity\Student::find($id);
    }
    elseif($type=='a'){

        $user = \Application\Entity\Account::find($id);

    }

    if($user){
        if($type=='s'){
            $name= $user->first_name;
        }
        elseif($type=='a'){
            $name= $user->first_name.' ('.$user->role->role.')';

        }
        return [
            'name'=>$name,
            'photo'=>$user->picture,
            'email'=>$user->email
        ];
    }
    else{
        return [
            'name'=>'N/A',
            'photo'=> false
        ];
    }
}

 function profilePictureUrl($picture,$basePath){
     $blank = $basePath.'/img/user.png';
     if(empty($picture)){
         return $blank;
     }

     if(isUrl($picture)){
         return $picture;
     }

     if(file_exists('public/'.$picture) && isImage($picture)){
         return resizeImage($picture,300,300,$basePath);
     }
     else{
         return $blank;
     }

 }

function sanitizeHtml($html){



    require_once 'vendor/htmLawed/htmLawed.php';
    $config = array('safe'=>1);
    $html = htmLawed($html,$config);


    $dom = new \DOMDocument();

    @$dom->loadHTML('<?xml encoding="utf-8" ?>' .$html);

    $script = $dom->getElementsByTagName('script');

    $remove = [];
    foreach($script as $item)
    {
        $remove[] = $item;
    }

    foreach ($remove as $item)
    {
        $item->parentNode->removeChild($item);
    }


    $script = $dom->getElementsByTagName('style');

    $remove = [];
    foreach($script as $item)
    {
        $remove[] = $item;
    }

    foreach ($remove as $item)
    {
        $item->parentNode->removeChild($item);
    }

    foreach($dom->getElementsByTagName('*') as $element){
        //This selects all elements
        foreach($element->attributes as $attribute){
            if(is_string($attribute)){
                if(preg_match('/on.*/',$attribute)==1){
                    /*
                     "on" looks for on and ".*" states that there
                     can be anything after the on (onmousemove,onload,etc.)
                    */
                    $element->removeAttribute($attribute);
                }
            }

        }
    }

    foreach($dom->getElementsByTagName('img') as $element){
        //This selects all elements
            $element->setAttribute('style','max-width:100%');
    }


    $html  = $dom->saveHTML();

    if(extension_loaded('tidy')){
        $tidy = new \tidy();
        $tidy->parseString($html);
        $tidy->cleanRepair();
    }
    else{
        $tidy = $html;
    }


    $dom = new \DOMDocument();

    @$dom->loadHTML('<?xml encoding="utf-8" ?>' .$tidy);

    $body = "";
    foreach($dom->getElementsByTagName("body")->item(0)->childNodes as $child) {
        $body .= $dom->saveHTML($child);
    }



    return $body;


}

function tidyHtml($html){

    if(extension_loaded('tidy')){
        $tidy = new \tidy();
        $tidy->parseString($html);
        $tidy->cleanRepair();
    }
    else{
        $tidy = $html;
    }



    return $tidy;
}

function flash($data){
    $container = new \Zend\Session\Container('flashData');
    $container->data = $data;

    $container->setExpirationHops(1);
}

function old($key,$value=null){
    $container = new \Zend\Session\Container('flashData');

    if(isset($container->data[$key]))
    {
        return  $container->data[$key];
    }
    elseif(!empty($value)){
        return $value;
    }
    else{
        return '';
    }
}

function oldData(){
    $container = new \Zend\Session\Container('flashData');
    if(isset($container->data)){
        return $container->data;
    }
    else{
        return null;
    }
}


function is_decimal( $val )
{
    return is_numeric( $val ) && floor( $val ) != $val;
}

function price($amount,$forcedCurrencyId=null,$raw=false){

    //get currency in use

    $session= new \Zend\Session\Container('currency');
    $currencyId = $session->currency_id;


    if($forcedCurrencyId){
        $currencyId= $forcedCurrencyId;
    }

    $currency = \Application\Entity\Currency::find($currencyId);

    $settingTable = new \Application\Model\SettingTable();

    if($settingTable->getSetting('country_id') != $currency->country_id){
        $amount = $amount * $currency->exchange_rate;
        $amount = round($amount,2);
    }

    if($raw){
        return $amount;
    }

    if(is_decimal($amount)){
        if(!empty($currency->country->symbol_left))
        {
            return $currency->country->symbol_left.number_format($amount,2);
        }
        else{
            return $currency->country->currency_code.number_format($amount,2);
        }

    }
    else{
        if(!empty($currency->country->symbol_left))
        {
            return $currency->country->symbol_left.number_format($amount);
        }
        else{
            return $currency->country->currency_code.number_format($amount);
        }


    }

}

function priceRaw($amount){
    return price($amount,null,true);
}

function currencies(){
    $currencies = \Application\Entity\Currency::orderBy('currency_id','desc')->get();
    return $currencies;
}

function currentCurrency(){

    $settingTable = new \Application\Model\SettingTable();
    $countryId = $settingTable->getSetting('country_id');
    $country = \Application\Entity\Country::find($countryId);

        //check for installed currency
    $defaultCurrency = \Application\Entity\Currency::where('country_id',$country->country_id)->first();
    if(!$defaultCurrency){
        $defaultCurrency = \Application\Entity\Currency::create(['currency_id'=>$country->country_id,'exchange_rate'=>1]);
    }


    $session = new \Zend\Session\Container('currency');

    if(isset($session->currency_id)){
        $currency = \Application\Entity\Currency::find($session->currency_id);
        if($currency){
            return $currency;
        }
        else{
            return $defaultCurrency;
        }
    }
    else{
        return $defaultCurrency;
    }

}

/**
 * @return \Application\Library\Cart|mixed
 */
function getCart(){
    $session = new \Zend\Session\Container('cart');
    if(!isset($session->cart)){
        $cart = new \Application\Library\Cart();
        $cart->store();
    }
    else{
        $cart = unserialize($session->cart);

    }
    return $cart;
}

function getCountry(){
    $ip_address = getClientIp();


    $ip_address = trim($ip_address);

    $settingTable = new \Application\Model\SettingTable();
    $countryId = $settingTable->getSetting('country_id');
    $country = \Application\Entity\Country::find($countryId);
    $defaultCountry = strtolower($country->iso_code_2);

    if(!filter_var($ip_address, FILTER_VALIDATE_IP)){

        return strtolower($country->iso_code_2);
    }

    if(getenv('APP_MODE')=='live'){



        if(\Application\Entity\Ip::where('ip',$ip_address)->count()==0){
            //create ip record in db
            $country = file_get_contents("http://ipinfo.io/$ip_address/country");

            $country = trim(strtolower($country));

            //   notifyAdmin('country fetched',$ip_address.' . line 31: '.$country);

            if(empty($country) || strlen($country)!=2){
                $country = $defaultCountry;
            }


            \Application\Entity\Ip::create(['ip'=>$ip_address,'country'=>$country]);
            return $country;
        }
        else{

            $ipModel = \Application\Entity\Ip::where('ip',$ip_address)->first();
            return $ipModel->country;
        }


    }
    else{

        return $defaultCountry;
    }

}

function jsonResponse($data){
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
       header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
    exit(json_encode($data));
}

function langMeta(){
    $lang = getSetting('config_language');
    if($lang=='ar'){
        return ' lang="ar" ';
    }
    else{
        return ' lang="'.$lang.'" ';
    }
}

?>