<?php
     error_reporting(E_ERROR | E_PARSE);
	//error_reporting(E_ALL & ~E_NOTICE);
	session_start();
	
	require_once('config.php');
	
	function __autoload($class_name)
	{
		if( strpos($class_name, '_') )
		{
			list($dir, $cl) = explode('_', $class_name);
			include_once _SITE_ROOT.'lib/classes/'.$dir.'/'.$class_name.'.php';
		}
		else
		{
			if( is_file(_SITE_ROOT.'lib/classes/'.$class_name.'.php') )
				include_once _SITE_ROOT.'lib/classes/'.$class_name.'.php';
			else if( is_file(_SITE_ROOT.'lib/classes/Front/'.$class_name.'.php') )
				include_once _SITE_ROOT.'lib/classes/Front/'.$class_name.'.php';
			else
				echo 'Dogodila se greška! functions.php ER.1 : '.$class_name._SITE_URL;
		}
	}
	
	if( get_conf('use_database') == 1 )
	{
		// moramo koristiti varijablu $db zato jer se klasa Db oslanja na tu varijablu
		$db = new Database;
	}
	
	function lang_data($table, $like)
	{
		$sql = "SHOW COLUMNS FROM `$table` WHERE Field LIKE '".$like."%'";
		//print $sql.'<br />';
		$rez = Db::query($sql);
		$numrows_lang = count($rez);
		if($rez)
		{
			$i = 0;
			$column_name = array();
			$lang_label = array();
			foreach ($rez as $row)
			{
				$column_name[$i] = Db::clean($row['Field']);
				$lang_label[$i] = strtoupper(substr(Db::clean($row['Field']), -2, 2));
				//echo $column_name[$i].'<br />';
				$i++;
			}
		}
		$cn['column_name'] = $column_name;
		$cn['lang_label'] = $lang_label;
		
		return $cn;
	}
		
	function imageCreateTransparent($x, $y)
	{ 
		$imageOut = imagecreatetruecolor($x, $y);
		$colourBlack = imagecolorallocate($imageOut, 55, 55, 55);
		imagefill ( $imageOut, 0, 0, $colourBlack );
		imagecolortransparent($imageOut, $colourBlack);

		return $imageOut;
	}
	
	function cut_paragraph($paragraph, $limit=20)
	{
		if(strlen($paragraph) > $limit)
		{
			$rough_short_par = substr($paragraph, 0, $limit);
			$last_space_pos = strrpos($rough_short_par, " "); 
			
			$clean_short_par = substr($rough_short_par, 0, $last_space_pos);
			$clean_sentence = $clean_short_par . "...";
			
			return $clean_sentence;
		}
		else
		{
			return $paragraph;
		}
	}
	
	function clean_uri($string)
	{
		$url = str_replace("'", '', $string);
		$url = str_replace('%20', ' ', $url);
		$hr = array('Č','Ć','Ž','Đ','Š','č','ć','ž','đ','š');
		$en = array('C','C','Z','D','S','c','c','z','d','s');
		$url = str_replace($hr, $en, $url);
		$url = preg_replace('~[^\\pL0-9\._\+]+~u', '-', $url); // substitutes anything but letters, numbers and '_' '+' with separator
		$url = trim($url, "-");
		@$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);  // you may opt for your own custom character map for encoding.
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9\._]+~', '', $url); // keep only letters, numbers, '_' and separator
		return $url;
	}
	
	function valid_email($address)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? false : true;
	}
	
	// vraća array sa id-ovima svih parent kategorija od kategorije koje smo proslijedili id + id te kategorije
	function get_parent_categories($id, $table, $first=true)
	{
		$is_cat = Db::query_one('SELECT id FROM '.Db::clean($table).' WHERE id = '.(int)$id.' LIMIT 1');
		
		if( ! $is_cat )
			return false;
		
		static $ids = array();
		
		if( $first )
		{
			$ids = array();
			$ids[] = $id;
		}
		
		$cat = Db::query_one('SELECT parent_id FROM '.Db::clean($table).' WHERE id = '.(int)$id.' LIMIT 1');
		
		if( $cat )
		{
			$ids[] = (int)$cat;
			get_parent_categories($cat, $table, false);
		}
		
		return array_reverse($ids);
	}
	
	// vraća id-ove svih podkategorija od kategorije koje smo proslijedili id + id te kategorije
	function get_subcategories($id, $table, $first=true)
	{
		$is_cat = Db::query_one('SELECT id FROM '.Db::clean($table).' WHERE id = '.(int)$id.' LIMIT 1');
		
		if( ! $is_cat )
			return false;
		
		static $ids = array();
		
		if( $first )
		{
			$ids = array();
			$ids[] = $id;
		}
		
		$cat = Db::query('SELECT id FROM '.Db::clean($table).' WHERE parent_id = '.(int)$id);
		
		if( $cat )
		{
			foreach($cat as $k => $v)
			{
				$ids[] = (int)$v['id'];
				get_subcategories($v['id'], $table, false);
			}
		}
		
		return array_unique($ids);
	}
	
	function get_subcategories_for_sql($cat_id, $table)
	{
		$subcats = get_subcategories($cat_id, $table);
		$subcat_dod = '';
		$z = 0;
		foreach($subcats as $s)
		{
			if($z == 0)
			{
				$z_dod = '';
			}
			else 
			{
				$z_dod = ',';
			}
			$subcat_dod .= $z_dod.$s;
			$z++;
		}
		return $subcat_dod;
	}
	
	function get_cat_sef_title($id, $table, $direction='down')
	{
		$lng = ( get_conf('multi_language') == 1 ) ? '_'._LNG : '_hr' ;
		
		if( $direction == 'up' )
			$parents = get_parent_categories($id, $table);
		else
			$parents = get_subcategories($id, $table);
		
		$uri = '';
		$cnt = count($parents);
		$i = 0;
		foreach($parents as $k => $v)
		{
			$i++;
			$uri .= clean_uri(Db::query_one('SELECT title'.$lng.' FROM '.Db::clean($table).' WHERE id = '.(int)$v.' LIMIT 1'));
			
			$uri .= ( $i < $cnt ) ? '-' : '' ;
		}
		
		return $uri;
	}
	
	function send_html_mail($from, $to, $subject, $msg)
	{
		$message = '
			<html>
			<head>
			  <title>'.$subject.'</title>
			</head>
			<body>
				'.$msg.'
			</body>
			</html>
		';
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		
		$headers .= 'From: '._SITE_TITLE.'<'.$from.'>'."\r\n";
		$headers .= 'X-Mailer: PHP/'.phpversion();
		
		$m = mail($to, $subject, $message, $headers);
		
		return $m;
	}
	
	function image_resize_to($input_file_name, $destination_file_name, $width, $height, $quality=false, $watermark=false)
	{
		// MIJENJA VELIČINU SLIKE I SPREMA SLIKU
		//		$input_file_name           mora biti path do slike npr.                                   './slike/tmp_aaa.jpg'
		//		$destination_file_name     mora biti path sa imenom slike gdje ju želimo spremiti npr.    './slike/aaa.jpg'
		
		$thumbw = $width;
		$thumbh = $height;
		
		$imagedata = getimagesize("$input_file_name");
		$imagewidth = $imagedata[0];
		$imageheight = $imagedata[1];
		$imagetype = $imagedata[2];
		
		// type definitions
		// 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP
		// 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order)
		// 9 = JPC, 10 = JP2, 11 = JPX
		
		if($imagetype == 2)
		{
			$src_img = imagecreatefromjpeg("$input_file_name");
		}
		elseif($imagetype == 1)
		{
			$src_img = imagecreatefromgif("$input_file_name");
		}
		elseif($imagetype == 3)
		{
			$src_img = imagecreatefrompng("$input_file_name");
			imagealphablending($dst_img, false);
			imagesavealpha($dst_img, true);  

		}
		
		if($src_img)
		{
			if($imagewidth <= $width)
			{
				$thumbw = $imagewidth;
				$thumbh = $imageheight;
			}
			
			$shrinkage = $thumbw/$imagewidth;
			$dest_width = $thumbw;
			$dest_height = $shrinkage * $imageheight;
			
			if( $dest_height > $imageheight )
			{
				$shrinkage = $thumbh/$imageheight;
				$dest_height = $thumbh;
				$dest_width = $shrinkage * $imagewidth;
			}
			
			$dst_img = imagecreatetruecolor($dest_width,$dest_height);
			imagealphablending($dst_img, false);
			imagesavealpha($dst_img, true);  
			imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dest_width,$dest_height, $imagewidth, $imageheight);
			
			if($watermark && is_file($watermark))
			{
				$watermark = imagecreatefrompng($watermark);
				$watermark_width = imagesx($watermark);
				$watermark_height = imagesy($watermark);
				
				$dest_x = $dest_width - $watermark_width - 30;
				$dest_y = $dest_height - $watermark_height - 30;
				
				imagecopy($dst_img, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
			}
			
			imageinterlace($dst_img,1); 
			
			if($imagetype == 2)
			{
				if($quality)
					imagejpeg($dst_img, $destination_file_name, $quality);
				else
					imagejpeg($dst_img, $destination_file_name);
			}
			elseif($imagetype == 1)
			{
				imagegif($dst_img, $destination_file_name);
			}
			elseif($imagetype == 3)
			{
				imagepng($dst_img, $destination_file_name);
			}
			
			imagedestroy($src_img);
			imagedestroy($dst_img);
		}
	}
	function get_constant($term, $lng, $type='')
	{	
		if($type == 'admin'){
			if( is_file(_SITE_ROOT.'admin/include/lang/lang_'.$lng.'.txt') )
			$handle = @fopen(_SITE_ROOT.'admin/include/lang/lang_'.$lng.'.txt', "r");
		}else{
			if( is_file(_SITE_ROOT.'lib/lang_'.$lng.'.txt') )
			$handle = @fopen(_SITE_ROOT.'lib/lang_'.$lng.'.txt', "r");
		}
		
		if( $handle )
		{
			while ( $buffer = fgets($handle) )
			{
				if( $buffer != '' && substr($buffer,0,1) == '_' )
				{
					$pos = strpos($buffer, '=');
					
					if( $pos !== false )
					{
						$const = trim(substr($buffer,0,$pos));
						$val = trim(substr($buffer,($pos+1)));
						
						if($const == $term)
						{
							$translation = $val;
							break;
						}
					}
				}
			}
			
			fclose ($handle);
		}
		
		return $translation;
	}
	function load_lang()
	{
		if( isset($_SESSION['lng']) && in_array($_SESSION['lng'], get_conf('languages')) )
		{
			$lng = $_SESSION['lng'];
			
			if( is_file(_SITE_ROOT.'lib/lang_'.$lng.'.txt') )
				$handle = @fopen(_SITE_ROOT.'lib/lang_'.$lng.'.txt', "r");
			
			if( $handle )
			{
				while ( $buffer = fgets($handle) )
				{
					if( $buffer != '' && substr($buffer,0,1) == '_' )
					{
						$pos = strpos($buffer, '=');
						
						if( $pos !== false )
						{
							$const = trim(substr($buffer,0,$pos));
							$val = trim(substr($buffer,($pos+1)));
							
							define($const, $val);
						}
					}
				}
				
				fclose ($handle);
			}
		}
	}
	
	function get_domain($site_url)
	{
		preg_match('/http:\/\/(((www).)|())([A-Za-z0-9\.-]+\.\w+)(.*)/', _SITE_URL, $m);
		
		return $m[5];
	}
	
	function redirect($url)
	{
		header('Location:'.$url);
		exit;
	}
	
	function get_web_page( $url )
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_USERAGENT      => "spider", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);

		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );

		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;
		return $header;
	}
	
	function last_query($return=false)
	{
		if( $return )
			return $_SESSION['sql_log']['last_sql'];
		else
			print $_SESSION['sql_log']['last_sql']."\n";
	}
		
	function get_first_subcat($id, $table)
	{
		$sql = 'SELECT id FROM '.Db::clean($table).' WHERE parent_id = '.(int)$id.' order by orderby asc LIMIT 1';
		//print $sql.'<br />';
		$row = Db::query_row($sql);
		$link = _SITE_URL._LNG.'/'._URL_KATEGORIJA.'/'.get_cat_sef_title($row['id'], 'categories', 'up').'/'.clean_uri($row['id']);
		return $link;
	}
	
	function get_first_subcat_gal($table)
	{
		$sql = 'SELECT id, title_'._LNG.' as title FROM '.Db::clean($table).' order by orderby desc LIMIT 1';
		//print $sql.'<br />';
		$row = Db::query_row($sql);
		$link = _SITE_URL._LNG.'/'._URL_PHOTOS.'/'.clean_uri($row['title']).'/'.clean_uri($row['id']);
		return $link;
	}
	
	function get_supcat($id, $table)
	{
		$content = '';
		$sql = 'SELECT parent_id FROM '.Db::clean($table).' WHERE id = '.(int)$id.' LIMIT 1';
		//print $sql.'<br />';
		$row = Db::query_row($sql);
		
		$sql = 'SELECT title_hr FROM '.Db::clean($table).' WHERE id = '.(int)$row['parent_id'].' LIMIT 1';
		//print $sql.'<br />';
		$supcat = Db::query_one($sql);
		
		$link = _SITE_URL._LNG.'/'._URL_CATEGORIES.'/'.get_cat_sef_title($row['parent_id'], 'categories', 'up').'/'.clean_uri($row['parent_id']);
		
		$content['title'] = $supcat;
		$content['link'] = $link;
		
		return $content;
	}
	
	/**
	* Truncates text.
	*
	* Cuts a string to the length of $length and replaces the last characters
	* with the ending if the text is longer than length.
	*
	* @param string  $text String to truncate.
	* @param integer $length Length of returned string, including ellipsis.
	* @param string  $ending Ending to be appended to the trimmed string.
	* @param boolean $exact If false, $text will not be cut mid-word
	* @param boolean $considerHtml If true, HTML tags would be handled correctly
	* @return string Trimmed string.
	*/
	function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag (f.e. </b>)
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
					// if tag is an opening tag (f.e. <b>)
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= mb_substr($line_matchings[2], 0, $left+$entities_length, 'UTF-8');
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
	
	
	function izvuci_youtube_id($youtube_link)
	{
		$findme   = '?v=';
		$findme_len = strlen($findme);
		$pos = strpos($youtube_link, $findme);
		if ($pos === false) 
		{
		    //
		} else {
			$youtube_link_skraceno = substr($youtube_link, ($pos+$findme_len));
			//echo $youtube_link_skraceno.'<br />';
			$findme2   = '&';
			$findme2_len = strlen($findme2);
			$pos_and = strpos($youtube_link_skraceno, $findme2);
			//echo $pos.'<br />';
			//echo $pos_and.'<br />';
			if ($pos_and === false) 
			{
				return $youtube_link_skraceno;
			} else {
		    	$sub = substr($youtube_link_skraceno, 0, $pos_and);
		    	//echo $sub.'<br />';
		    	return $sub;
			}
		}
	}
	function remove_url_action($url,$varname){
		return preg_replace('/[?&]'.$varname.'=[^&]+(&|$)/','$1',$url);
	}
	function generiraj_youtube_sliku($youtube_link, $velicina)
	{ // $velicina moze biti 0, 1, 2, 3, default ili hqdefault
		$youtube_id = izvuci_youtube_id($youtube_link);
		$content = '<img src="http://img.youtube.com/vi/'.$youtube_id.'/'.$velicina.'.jpg">';
		return $content;
	}
	function generiraj_youtube_url($youtube_link, $velicina)
	{ // $velicina moze biti 0, 1, 2, 3, default ili hqdefault
		$youtube_id = izvuci_youtube_id($youtube_link);
		$content = 'http://img.youtube.com/vi/'.$youtube_id.'/'.$velicina.'.jpg';
		return $content;
	}
	function generiraj_youtube_embed_link($youtube_link)
	{ 
		$youtube_id = izvuci_youtube_id($youtube_link);
		$content = 'http://www.youtube.com/embed/'.$youtube_id.'/';
		return $content;
	}
	
	function format_link($link1)
	{
		if(($link1 != '') && (!strstr($link1,"http://")))
		{
			$link1 = 'http://'.$link1;
		}
		return $link1;
	}
	function generate_slider_gallery($id,$table){

	$prva_slika = Db::query_row('SELECT id,photo_name,tlocrt FROM site_photos WHERE table_name = "'.$table.'" AND table_id = '.$id.' ORDER BY orderby ASC LIMIT 1');
    $slike = Db::query('SELECT * FROM site_photos WHERE table_name = "'.$table.'" AND table_id = '.$id.' AND table_id != '.$prva_slika['id'].' ORDER BY orderby ASC');
    $broj_slika = count($slike);

    	$content = '';
		$content .='<span class="frame_">
	           <div class="details-slider-frame o_nama">';
	                if($broj_slika > 1){
	                $content .='<ul class="details-slider">';
	                    foreach($slike as $red){
	                    	if($red['tlocrt']=='da'){
	                        		$zc=2;
	                        	}else{
	                        		$zc=1;
	                        	}
	                        $content .='<li class="gal">
	                            <a href="'._SITE_URL.'upload_data/site_photos/'.$red['photo_name'].'">
	                                <img src="'._SITE_URL.'lib/plugins/thumb.php?src='._SITE_URL.'upload_data/site_photos/'.$red['photo_name'].'&w=1060&h=550&zc='.$zc.'" />
	                            </a>
	                        </li>';
	                     }                      
	                $content .='</ul>';
	                }else{
	                if($prva_slika['photo_name']){
	                    $content .='<img src="'._SITE_URL.'lib/plugins/thumb.php?src='._SITE_URL.'upload_data/site_photos/'.$prva_slika['photo_name'].'&w=1060&h=550&zc='.(($prva_slika['tlocrt']=='da')?'2':'1').'" />';
	                } 
	            } 
	            $content .='</div>';
	            if($broj_slika > 1){
	                $content .='<div class="thumbs">
	                    <div id="bx-pager">';
	                        $i=0;
	                        foreach($slike as $red){
	                        	if($red['tlocrt']=='da'){
	                        		$zc=2;
	                        	}else{
	                        		$zc=1;
	                        	}
	                        $content .='<a data-slide-index="'.$i.'" href="javascript:;">
	                                <img src="'._SITE_URL.'lib/plugins/thumb.php?src='._SITE_URL.'upload_data/site_photos/'.$red['photo_name'].'&w=123&h=80&zc='.$zc.'" alt="'.$item['title_'._LNG].'-'.$i.'" />
	                            </a>';
	                           $i++;
	                         }   
	                    $content .='</div>
	                    <div class="clearfix"></div>
	                </div>';
	            }
	            $video =  Db::query_one('SELECT video_url FROM '.$table.' WHERE id = "'.$id.'"');
				if($video){
	            	$content .= '<a href="#video-'.$id.'" target="_blank" class="pop video-link">Video</a>
						<div id="video-'.$id.'" class="mfp-with-anim mfp-hide popup-container">
							<iframe width="100%" height="360" src="'.generiraj_youtube_embed_link($video).'" frameborder="0" allowfullscreen></iframe>
						</div>';
	            }
            $content .='</span>';
		return $content;

	}
	function generate_gallery_r($id,$table){
	$prva_slika = Db::query_row('SELECT id,photo_name,tlocrt FROM site_photos WHERE table_name = "'.$table.'" AND table_id = '.$id.' ORDER BY orderby ASC LIMIT 1');
    $slike = Db::query('SELECT * FROM site_photos WHERE table_name = "'.$table.'" AND table_id = '.$id.' AND table_id != '.$prva_slika['id'].' ORDER BY orderby ASC');
    $video =  Db::query_one('SELECT video_url FROM '.$table.' WHERE id = "'.$id.'"');
    $broj_slika = count($slike);




		$content = '';
		if($prva_slika){
		$content .='<span class="img-frame r-gallery">
	           <div class="details-slider-frame">';
	                if($broj_slika > 1){
	                $content .='<ul class="details-slider">';
	                    foreach($slike as $red){
	                    	if($red['tlocrt']=='da'){
	                        		$zc=2;
	                        	}else{
	                        		$zc=1;
	                        	}
	                        $content .='<li class="gal">
	                            <a href="'._SITE_URL.'upload_data/site_photos/'.$red['photo_name'].'">
	                                <img src="'._SITE_URL.'lib/plugins/thumb.php?src='._SITE_URL.'upload_data/site_photos/'.$red['photo_name'].'&w=500&h=290&zc='.$zc.'" />
	                            </a>
	                        </li>';
	                     }                      
	                $content .='</ul>';
	                }else{
	                if($prva_slika['photo_name']){
	                    $content .='<img src="'._SITE_URL.'lib/plugins/thumb.php?src='._SITE_URL.'upload_data/site_photos/'.$prva_slika['photo_name'].'&w=500&h=290&zc='.(($prva_slika['tlocrt']=='da')?'2':'1').'" />';
	                } 
	            } 
	            $content .='</div>';
	            if($video){
	            	$content .= '<a href="#video-'.$id.'" target="_blank" class="pop video-link">Video</a>
						<div id="video-'.$id.'" class="mfp-with-anim mfp-hide popup-container">
							<iframe width="100%" height="360" src="'.generiraj_youtube_embed_link($video).'" frameborder="0" allowfullscreen></iframe>
						</div>';
	            }
            $content .='</span>';
        }
        
		return $content;
	}
	function generate_gallery_l($id,$table){
$prva_slika = Db::query_row('SELECT id,photo_name,tlocrt FROM site_photos WHERE table_name = "'.$table.'" AND table_id = '.$id.' ORDER BY orderby ASC LIMIT 1');
    $slike = Db::query('SELECT * FROM site_photos WHERE table_name = "'.$table.'" AND table_id = '.$id.' AND table_id != '.$prva_slika['id'].' ORDER BY orderby ASC');
    $broj_slika = count($slike);




		$content = '';
		if($prva_slika){
		$content .='<span class="img-frame l-gallery">
	           <div class="details-slider-frame">';
	                if($broj_slika > 1){
	                $content .='<ul class="details-slider">';
	                    foreach($slike as $red){
	                    	if($red['tlocrt']=='da'){
	                        		$zc=2;
	                        	}else{
	                        		$zc=1;
	                        	}
	                        $content .='<li class="gal">
	                            <a href="'._SITE_URL.'upload_data/site_photos/'.$red['photo_name'].'">
	                                <img src="'._SITE_URL.'lib/plugins/thumb.php?src='._SITE_URL.'upload_data/site_photos/'.$red['photo_name'].'&w=500&h=290&zc='.$zc.'" />
	                            </a>
	                        </li>';
	                     }                      
	                $content .='</ul>';
	                }else{
	                if($prva_slika['photo_name']){
	                    $content .='<img src="'._SITE_URL.'lib/plugins/thumb.php?src='._SITE_URL.'upload_data/site_photos/'.$prva_slika['photo_name'].'&w=500&h=290&zc='.(($prva_slika['tlocrt']=='da')?'2':'1').'" />';
	                } 
	            } 
	            $content .='</div>';
	            $video =  Db::query_one('SELECT video_url FROM '.$table.' WHERE id = "'.$id.'"');
				if($video){
	            	$content .= '<a href="#video-'.$id.'" target="_blank" class="pop video-link">Video</a>
						<div id="video-'.$id.'" class="mfp-with-anim mfp-hide popup-container">
							<iframe width="100%" height="360" src="'.generiraj_youtube_embed_link($video).'" frameborder="0" allowfullscreen></iframe>
						</div>';
	            }
            $content .='</span>';
            
        }
        
		return $content;
	}

	function generate_select($table, $triggerTxt, $name, $fields, $function, $active,$sql)
	{
		$data = Db::query("SELECT ".$fields." FROM ".$table." ORDER BY orderby ASC");

		$content = '';
		$content .= '<div class="select">
						<div class="select-frame inactive">
							<a href="javascript:;" class="select-trigger"><span class="triggerText">'.$triggerTxt.'</a>
							<select name="'.$name.'" id="'.$name.'" class="hidden-select" '.$function.'>';
				            	$i=1;
				            	foreach ($data as $red) {
				             	$selected = ($active==$red['id'])?'selected':'';
				             	$content .= '<option id="'.$name.''.$red['id'].'" '.$selected.' value="'.$red['id'].'">'.$red['title_en'].'</option>';
				            	$i++;
				            	}
		$content .= '		</select>
							<div class="select-frame-max">
								<ul class="select-ul">';
									$i=1;
							        foreach ($data as $red) {
							        $selected = ($active==$red['id'])?' class="active"':'';
							       
						$content .= ' <li'.$selected.'><a rel="'.$name.''.$red['id'].'" data-title="'.$red['title_en'].'">'.$red['title_en'].'</a></li>';
							        $i++;
							        }
		$content .= '			</ul>
							</div>
						</div>
					</div>';
		
		return $content;
	}
?>