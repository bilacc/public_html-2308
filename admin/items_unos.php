<?php 
	require_once('../lib/functions.php');
		
	$crud = new Admin_Crud;
	$crud->table = 'items';
	$crud->title_row_name = 'title_hr';
	
	$show['title'] = true; 				//naslov
	$show['text'] = true; 				//tekst
	$show['front_page'] = false;
	$show['slider'] = false; 
	$show['new'] = false; 		//izdvajanje na naslovnicu ili gdje se već poveže na frontu
	$show['status'] = false; 			//mogućnost postavljanja statusa (aktivno, neaktivno, zakazano)
	$show['date'] = false; 				//datum
	$show['expires'] = false; 			//mogućnost postavljanja datuma do kojeg će se neka stranica, članak prikazivat na stranici
	$show['single_category'] = true; 	//mogućnost pridodavanja stranice ili članka u jednu kategoriju
	$show['multiple_category'] = false; //mogućnost pridodavanja stranice ili članka u više kategorija (imploda array u string i sprema u multi_categories)
	$show['video'] = true; 				//polje za upis linka od videa
	$show['video_title'] = false;		//naslov videa
	$show['gmap'] = true; 				//google map
	$crud->files_num = null; 			// broj fajlova koje unosimo, za neograničeno upiši: null
	$crud->files_titles = true; 		// dali fajlovi trebaju naslov	
	$crud->img_num = null; 				// broj slika koje unosimo, za neograničeno upiši: null
	$crud->img_sizes = array(450,340); 	// veličine slika koje unosimo, ako ima samo 2 broja, slika se neće resizati prilikom uplouda
	$crud->img_titles = false; 			// dali slike trebaju naslov
	

	$cn = lang_data($crud->table, 'title\_');
	$column_name = $cn['column_name'];
	$lang_label = $cn['lang_label'];
		
	if( isset($_GET['action']) && isset($_GET['id']) && ! $_POST ) // ako je GET znači da trebamo dohvatiti podatke za id koji je naveden
	{
		$id = (int)$_GET['id'];
		
		$crud->id = $id;
		$data_all = $crud->get_data(); // dohvaća podatke
		
		$data = $data_all['data'];
		$imgs = $data_all['imgs'];
		
		for($i = 0; $i < sizeof($column_name); $i++)
		{
			$files[strtolower($lang_label[$i])] = $data_all['files'][strtolower($lang_label[$i])];
		}
	}
	else if( isset($_POST) && count($_POST) > 0 ) // ako je POST onda spremamo podatke, unosimo ili updateamo
	{
		if( isset($_POST['e_id']) && (int)$_POST['e_id'] > 0 )
		{
			$crud->id = (int)$_POST['e_id'];
			$crud->action = 'update';
		}
		
		$crud->save_data($_POST);
	}
	
	$cat = new Admin_ManageCategories($data['id'],'categories', $crud->table);
	
	$_SESSION['images'] = null;
	$_SESSION['images']['cnt'] = 1;
	
	for($i = 0; $i < sizeof($column_name); $i++)
	{
		$_SESSION['file-'.strtolower($lang_label[$i])] = null;
		$_SESSION['file-'.strtolower($lang_label[$i])]['cnt'] = 1;
	}

	$specifikacije = Db::query("SELECT * FROM specifikacije ORDER BY orderby DESC");
	$item_specifikacije_raw = Db::query("SELECT * FROM item_specifikacije WHERE item_id = ".$id);
	$item_specifikacije = array();

	foreach($item_specifikacije_raw as $red){
		$item_specifikacije[$red['specifikacija_id']]['vrijednost_hr'] = $red['spec_vrijednost_hr'];
	}
	
	include('include/php/header.php');
	
	if( isset($_GET['err']) )
	{
		echo '<div class="error">';
		
		foreach($_SESSION['err_collector'] as $k => $v)
		{
			echo $v.'<br/>';
		}
		
		echo '</div>';
		
		$_SESSION['err_collector'] = null;
	}

	else if( isset($_GET['status']) && $_GET['status'] == 'success' )
	{
		echo '<div class="success">Uspješno ste spremili podatke!</div>';
	}

?>

	<?php
	if( isset($_GET['action']) && isset($_GET['id']) && ! $_POST )
	{
	?>
		<h1>Uređivanje nekretnine <br/><a href="<?php echo $crud->table; ?>_unos.php" class="gumb">Dodaj novu</a></h1>
	<?php
	}else{
	?>
		<h1>Unos nekretnine</h1>
	<?php } ?>
	
	<form name="<?php echo $crud->table; ?>" id="<?php echo $crud->table; ?>" class="unos" action="" enctype="multipart/form-data" method="post">
		<input type="hidden" name="e_id" id="e_id" value="<?php if( isset($_GET['id']) ){ echo $_GET['id']; } ?>"/>		
		<div class="box-75">
			

		<?php
			if($show['title'])
			{
		?>
			<div class="tabs-title">
				<?php
					if(sizeof($column_name) != 1){
					for($i = 0; $i < sizeof($column_name); $i++){
				?>
						<a href="javascript:;" id="tab_title_<?php echo strtolower($lang_label[$i]);?>" <?php echo ($i == 0)? 'class="slc"':''; ?>>Naslov <span>(<?php echo $lang_label[$i];?>)</span></a>
				<?php
					}
					}
				?>
			</div>
			<div class="tabs-title-content">
				<?php
					if(sizeof($column_name) == 1){
				?>
					<div class="editor-title slc" id="tab_title_hr_content">
						<input type="text" placeholder="Naslov" name="title_hr" id="title_hr" value="<?php echo $data['title_hr']; ?>"/>
					</div>
				<?php }else{
					for($i = 0; $i < sizeof($column_name); $i++)
					{
				?>				
				<div class="editor-title <?php echo ($i == 0)? 'slc':''; ?>" id="tab_title_<?php echo strtolower($lang_label[$i]);?>_content">	
					<input type="text" class="" name="title_<?php echo strtolower($lang_label[$i]);?>" id="title_<?php echo strtolower($lang_label[$i]);?>" value="<?php echo isset($data['title_'.strtolower($lang_label[$i])])? $data['title_'.strtolower($lang_label[$i])] : '' ; ?>"/>
				</div>
				<?php
					}
				  }
				?>
			</div>
		<?php
			}
			else
			{
		?>
			<input type="hidden" name="title_hr" id="title_hr" value="<?php echo $data['title_hr']; ?>"/>
		<?php 
			} 
		?>
					
			<?php
			if($show['text'])
			{
			?>
				<div class="tabs">
				<?php
				if(sizeof($column_name) == 1){
				?>		
					<a href="javascript:;" id="tab_hr" class="slc">Tekst</a>
				<?php
				}else{
					for($i = 0; $i < sizeof($column_name); $i++)
					{
					?>
					<a href="javascript:;" id="tab_<?php echo strtolower($lang_label[$i]);?>" <?php echo ($i == 0)? 'class="slc"':''; ?>>Tekst <span>(<?php echo $lang_label[$i];?>)</span></a>
				<?php
					}
				}
				?>
				</div>
					
				<div class="tabs-content">
				<?php
				if(sizeof($column_name) == 1){
				?>		
					<div class="editor slc" id="tab_hr_content">
						<textarea class="ckeditor" name="text_hr"><?php echo isset($data['text_hr'])? $data['text_hr'] : '' ; ?></textarea>
					</div>
				<?php
				}else{
					for($i = 0; $i < sizeof($column_name); $i++)
					{
					?>
					<div class="editor <?php echo ($i == 0)? 'slc':''; ?>" id="tab_<?php echo strtolower($lang_label[$i]);?>_content">
						<textarea class="ckeditor" name="text_<?php echo strtolower($lang_label[$i]);?>"><?php echo isset($data['text_'.strtolower($lang_label[$i])])? $data['text_'.strtolower($lang_label[$i])] : '' ; ?></textarea>
					</div>
				<?php
					}
				}
				?>
				</div>
			<?php
			}
			?>
			


			<?php /*<div class="box-100 light padd specifikacije">	
			<h3>Specifikacije</h3>
			<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
			<?php 
			$i=1;
			foreach($specifikacije as $red)
			{
			?>
			<label class="hidden-category-label">
				<input type="checkbox" class="spec_<?php echo $i;?>" name="spec[]" value="<?php echo $red['id']; ?>" id="spec_<?php echo $i;?>" onchange="if(this.checked){$('#spec_<?php echo $i;?>').prop('checked', true);$('#spec_<?php echo $red['id'];?>_container').slideDown();}else{$('#spec_<?php echo $red['id'];?>_container').slideUp();}" 
				<?php echo (isset($item_specifikacije[$red['id']]))? 'checked':''; ?>>
				<?php echo $red['title_hr'];?>
			</label>
			<div class="clearfix"></div>
			<div class="hidden-category2 hidden-category" id="spec_<?php echo $red['id'];?>_container" style="<?php echo($item_specifikacije[$red['id']]['vrijednost_hr'] != '')? " " : "display:none;";?>">
				<input type="text" name="spec_vrijednost_hr[<?php echo $red['id'];?>]" id="spec_vrijednost_hr<?php echo $red['id'];?>" value="<?php echo $item_specifikacije[$red['id']]['vrijednost_hr']; ?>"/>
			</div>
				

			<?php $i++;
			} ?>
		</div>
		*/?>
		<?php
			if($crud->img_num > 0 || $crud->img_num === null)
			{
			?>
			
			<div class="box-100 light">
				<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
				<h3>Slike <a href="javascript:;" class="gumb-upload" onclick="sjx('open_upload','slike-upload'); return false;">Unesi slike</a></h3>
					<p class="box-100 info">	
						Označite slike tlocrta
					</p>	
				<div id="images-holder" class="image-sort">
					<?php
					if( $data && count($imgs) > 0 )
					{
						foreach($imgs as $k => $v)
						{
						?>
							<div class="unos-slika" id="img_holder_<?php echo $v['id']; ?>">
								<img src="<?php echo _SITE_URL;?>lib/plugins/thumb.php?src=<?php echo _SITE_URL; ?>upload_data/site_photos/<?php echo $v['photo_name']; ?>&w=280&h=188&zc=1" alt="" />
								<a href="javascript:;" onclick="if(confirm('Slika će se trajno izbrisati! Jeste li sigurni da želite obrisati sliku?')){sjx('del_img',<?php echo $v['id']; ?>); return false;}" class="del_img"><img src="images/icon-delete-round.png" alt="Briši" /></a>
								<?php
								if( $crud->img_titles )
								{
									if(sizeof($column_name) == 1){
									?>		
										<input type="text" name="img_title_hr_<?php echo $v['id']; ?>" value="<?php echo $v['title_hr']; ?>" placeholder="opis slike..." class="no-sort"/>
									<?php
									}else{
										for($i = 0; $i < sizeof($column_name); $i++)
										{
										?>
										<input type="text" name="img_title_<?php echo strtolower($lang_label[$i]); ?>_<?php echo $v['id']; ?>" value="<?php echo $v['title_'.strtolower($lang_label[$i])]; ?>" placeholder="[<?php echo $lang_label[$i]; ?>] opis slike..." class="no-sort"/>
									<?php
										}
									}
								}
								?>
								<div class="chck-img no-sort">
									<input type="hidden" name="tlocrt_<?php echo $v['id']; ?>" value="ne">
									<input type="checkbox" <?php echo ($v['tlocrt'] == 'da')? "checked" : "";?> name="tlocrt_<?php echo $v['id']; ?>" value="da" id="tlocrt_<?php echo $v['id']; ?>" onchange="sjx('set_tlocrt',<?php echo $v['id']; ?>); return false;">
									<label for="tlocrt_<?php echo $v['id']; ?>">
										<span></span>
									</label>
								</div>
							</div>	
						<?php
						}
					}else{
					?>
						<div class="no_entry">Trenutno nemate unesenu niti jednu sliku.</div>
					<?php } ?>
				</div>
			</div>
			<?php
			}
			?>
		
<div class="tabs-files">
<?php
	if($crud->files_num > 0 || $crud->files_num === null)
	{
		if(sizeof($column_name) == 1)
		{
?>		
		<!-- jedan jezik -->

	<?php
		}
		else
		{
			for($i = 0; $i < sizeof($column_name); $i++)
			{
	?>

			<a href="javascript:;" id="file-<?php echo strtolower($lang_label[$i]); ?>">Dokumenti <span>(<?php echo $lang_label[$i]; ?>)</span></a>
			
<?php
			}
		}
	}
?>
</div>




<!--  -->
<?php
	if($crud->files_num > 0 || $crud->files_num === null)
	{
		if(sizeof($column_name) == 1)
		{
?>		
			<!-- jedan jezik -->
	<?php
		}
		else
		{
			for($i = 0; $i < sizeof($column_name); $i++)
			{
	?>
			<div class="box-100 light file-sort tabs-files-content <?php echo ($i == 0)? 'slc':''; ?>" id="file-<?php echo strtolower($lang_label[$i]); ?>_content">
				<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
				<h3><a href="javascript:;" onclick="sjx('open_upload','dokumenti-upload-<?php echo strtolower($lang_label[$i]); ?>'); return false;" class="gumb-upload">Unesi dokumente</a></h3>
				<input type="hidden" name="file_table" class="file_table" value="site_files_<?php echo strtolower($lang_label[$i]); ?>"/>
				
				<div class="file-sort">
				<?php
					if( $data && count($files[strtolower($lang_label[$i])]) > 0 )
					{
						foreach($files[strtolower($lang_label[$i])] as $k => $v)
						{
				?>
							<div class="unos-dokument" id="file_holder_<?php echo strtolower($lang_label[$i]); ?>_<?php echo $v['id']; ?>">
								<a href="../upload_data/site_files/<?php echo $v['file_name']; ?>" target="_blank"><img src="images/doc.png" alt="" /></a>
								<a href="javascript:;" onclick="if(confirm('Dokument će se trajno izbrisati! Jeste li sigurni da želite obrisati dokument?')){sjx('del_file',<?php echo $v['id']; ?>, '<?php echo strtolower($lang_label[$i]); ?>');return false;}" class="del_img">
									<img src="images/icon-delete-round.png" alt="Briši" />
								</a>
								<input class="no-sort" type="text" name="file_title_<?php echo strtolower($lang_label[$i]); ?>_<?php echo $v['id']; ?>" value="<?php echo ($v['title'] != '')? $v['title']:$v['file_name']; ?>" placeholder="naslov dokumenta..."/>
							</div>	
				<?php
						}
					}
					else
					{
				?>
						<div class="no_entry">Trenutno nemate unesen niti jedan dokument.</div>
				<?php
					}
				?>
				</div>
			</div>
<?php
			}
		}
	}
?>

					
					
			<?php
			if($show['gmap']){
			?>
			<div class="box-100 light">

				<h3>Lokacija</h3>
				<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
				<script>
					$(document).ready(function() {
						// start_gmap();
					});
				</script>
	<div id="floating-panel">
      <input id="address" type="textbox" name="address" value="<?php echo $data['address']; ?>">
      <input id="submit" type="button" value="Pronađite na karti">
    </div>
    <div id="map" style="height:300px;"></div>
    <script>

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 8,
          center: {lat: <?php echo ($data['gmap_lat_1'] != '')? $data['gmap_lat_1']:'45.79649398143752'; ?>, lng: <?php echo ($data['gmap_lon_1'] != '')? $data['gmap_lon_1']:'15.982704162597656'; ?>}
        });

        var image = "<?php echo _SITE_URL.'images/pin-admin.png';?>";
        var beachMarker = new google.maps.Marker({
          position: {lat: <?php echo ($data['gmap_lat_1'] != '')? $data['gmap_lat_1']:'45.79649398143752'; ?>, lng: <?php echo ($data['gmap_lon_1'] != '')? $data['gmap_lon_1']:'15.982704162597656'; ?>},
          map: map,
          icon: image
        });


        var geocoder = new google.maps.Geocoder();

        document.getElementById('submit').addEventListener('click', function() {
          geocodeAddress(geocoder, map);
        });
      }

      function geocodeAddress(geocoder, resultsMap) {
        var address = document.getElementById('address').value;
        geocoder.geocode({'address': address}, function(results, status) {
          if (status === 'OK') {
          	
            resultsMap.setCenter(results[0].geometry.location);

            var marker = new google.maps.Marker({
              map: resultsMap,
              position: results[0].geometry.location
            });
            document.getElementById('lokacija').value=results[0].geometry.location;
            // alert(results[0].geometry.location);
            
            
          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });
      }





    </script>



    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBgnt-53VE5xXbvzq_fnnR-KF_luEZeZ50&libraries=places&callback=initMap"
        async defer></script>




				<input id="lokacija" value="(<?php echo ($data['gmap_lat_1'] != '')? $data['gmap_lat_1']:'45.79649398143752';?>, <?php echo ($data['gmap_lon_1'] != '')? $data['gmap_lon_1']:'15.982704162597656';?>)" type="hidden" name="lokacija"/>
				<!-- <div id="map1" style="width:100%; height:350px;"></div> -->
				<input id="gmap_lat_1" value="<?php echo ($data['gmap_lat_1'] != '')? $data['gmap_lat_1']:'45.79649398143752'; ?>" type="hidden" name="gmap_lat_1"/>
				<input id="gmap_lon_1" value="<?php echo ($data['gmap_lon_1'] != '')? $data['gmap_lon_1']:'15.982704162597656'; ?>" type="hidden" name="gmap_lon_1"/>
			</div>
			<?php
			}
			?>
			 <script>


			 

$(document).ready(function() {
 // $('#floating-panel').find('#submit').trigger('click');alert();
});
    </script>
		</div>
		
		<div class="box-25 light last" style="padding: 9px 18px 4px 0px; margin-top: -30px; <?php echo($data['prodaja'] == 'da')? " background:#7ac143; color: #fff;" : "";?>">
				<input type="hidden" name="prodaja" value="ne">
				<input type="checkbox" class="arhiva" <?php echo($data['prodaja'] == 'da')? "checked" : "";?> name="prodaja" value="da" id="prodaja" onchange="if(this.checked){$('#prodaja').prop('checked', true);}else{$('#prodaja').prop('checked', false);}">
				<label class="" style="margin-bottom: 8px; text-transform: uppercase; font-size: 14px; margin-top: 3px; <?php echo($data['prodaja'] == 'da')? " color: #fff;" : "";?>" for="prodaja">Prodaja</label>
		</div>	



		<div class="box-25 light last" style="padding: 9px 18px 4px 0px; <?php echo($data['najam'] == 'da')? " background:#7ac143; color: #fff;" : "";?>">
			
				<input type="hidden" name="najam" value="ne">
				<input type="checkbox" class="arhiva" <?php echo($data['najam'] == 'da')? "checked" : "";?> name="najam" value="da" id="najam" onchange="if(this.checked){$('#najam').prop('checked', true);}else{$('#najam').prop('checked', false);}">
				<label class="" style="margin-bottom: 8px; text-transform: uppercase; font-size: 14px; margin-top: 3px; <?php echo($data['najam'] == 'da')? " color: #fff;" : "";?>" for="najam">Najam</label>
			
		</div>
		<div class="box-25 last">
				<input type="text" placeholder="Redirect URL" name="redirect_url" id="redirect_url" value="<?php echo $data['redirect_url']; ?>"/>
			</div>	
		<?php
		if($show['status'] || $show['date'] || $show['expires']){
		?>
		<div class="box-25 light">
			<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
			<?php
			if($show['status']){
			?>
			<select name="status" id="status">
				<option value="Hrvatska" <?php echo ($data['status'] == 'Hrvatska')? 'selected="selected"':'';?>>Hrvatska</option>
				<option value="Bosna i Hercegovina" <?php echo ($data['status'] == 'Bosna i Hercegovina')? 'selected="selected"':'';?>>Bosna i Hercegovina</option>
				<option value="Srbija" <?php echo ($data['status'] == 'Srbija')? 'selected="selected"':'';?>>Srbija</option>
				<option value="ostalo" <?php echo ($data['status'] == 'ostalo')? 'selected="selected"':'';?>>Ostale države</option>
				<?php /*if(time() < strtotime($data['created'])){ ?>
					<option value="zakazano" selected="selected">Zakazano</option>
				<?php } */?>
			</select>
			<?php
			}
			?>			
			
		
		

			<div class="box-25 last">
				<input type="text" placeholder="Redirect URL" name="redirect_url" id="redirect_url" value="<?php echo $data['redirect_url']; ?>"/>
			</div>

			<?php
			if($show['expires']){
			?>
			<h4>Traje do:</h4>
			
			<div class="datum">
				<select name="expires_dan" id="">
					<option value="0" <?php echo (date("d.m.Y", strtotime($data['expires'])) == '1.1.1970')? 'selected="selected"':'';?>>-</option>
					<?php
					for($i = 1; $i <= 31; $i++){
					?>
					<option value="<?php echo $i;?>" <?php echo (date("d", strtotime($data['expires'])) == $i && date("Y", strtotime($data['expires'])) != 1970)? 'selected="selected"':'';?>><?php echo $i;?></option>
					<?php } ?>
				</select>
				
				<select name="expires_mjesec" id="">
					<option value="0" <?php echo (date("d.m.Y", strtotime($data['expires'])) == '1.1.1970')? 'selected="selected"':'';?>>-</option>
					<?php
					for($i = 1; $i <= 12; $i++){
					?>
					<option value="<?php echo $i;?>" <?php echo (date("m", strtotime($data['expires'])) == $i && date("Y", strtotime($data['expires'])) != 1970)? 'selected="selected"':'';?>><?php echo $i;?></option>
					<?php } ?>
				</select>
				
				<select name="expires_godina" id="">
					<option value="0" <?php echo (date("d.m.Y", strtotime($data['expires'])) == '1.1.1970')? 'selected="selected"':'';?>>-</option>
					<?php
					for($i = 1990; $i <= 2050; $i++){
					?>
					<option value="<?php echo $i;?>" <?php echo (date("Y", strtotime($data['expires'])) == $i && date("Y", strtotime($data['expires'])) != 1970)? 'selected="selected"':'';?>><?php echo $i;?></option>
					<?php } ?>
				</select>
			</div>
			<?php
			}
			
			?>
			
		</div>
		<?php } ?>
	
		<?php
		if($show['front_page'] || $show['single_category'] || $show['multiple_category']){
		?>
		<div class="box-25 last light">
			<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
			<?php
			if($show['slider']){
			?>
			<h4>Izdvojeno u animaciju</h4>
			
			<select name="slider" id="slider">
				<option value="ne" <?php echo ($data['slider'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['slider'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<?php
			}
			?>
			<?php
			if($show['front_page']){
			?>
			<h4>Izdvojeno na naslovnicu</h4>
			
			<select name="front_page" id="front_page">
				<option value="ne" <?php echo ($data['front_page'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['front_page'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<?php
			}
			?>
			<?php
			if($show['single_category']){
			?>
			<h4>Kategorija:</h4>
			
			<select name="categories_id" id="categories_id">
				<option value="">-- nije u kategoriji --</option>
				<?php echo $cat->display_tree_select(0,0,$data['categories_id'],2); ?>
			</select> 
			<?php
			}
			?>
			<?php 
			$city= Db::query("SELECT * FROM city WHERE parent_id=0 ORDER BY orderby ASC");
			?>

			<select name="city_id" id="city_id">
				<option value=""> -- </option>
				<?php foreach ($city as $red) {
					$sub_city= Db::query("SELECT * FROM city WHERE parent_id=".$red['id']." ORDER BY orderby ASC");
				?>
					<option value="<?php echo $red['id']?>" <?php echo ($data['city_id'] == $red['id'])? 'selected="selected"':'';?>>
						<?php echo $red['title_hr'];?>
					</option>
					<?php 
						foreach ($sub_city as $sub_red) {
						$subsub_city= Db::query("SELECT * FROM city WHERE parent_id=".$sub_red['id']." ORDER BY orderby ASC");
					?>
						<option value="<?php echo $sub_red['id']?>" <?php echo ($data['city_id'] == $sub_red['id'])? 'selected="selected"':'';?>>
						  &nbsp;&nbsp;-<?php echo $sub_red['title_hr'];?>
						</option>
						<?php 
							foreach ($subsub_city as $subsub_red) {
							$subsubsub_city= Db::query("SELECT * FROM city WHERE parent_id=".$subsub_red['id']." ORDER BY orderby ASC");
						?>
							<option value="<?php echo $subsub_red['id']?>" <?php echo ($data['city_id'] == $subsub_red['id'])? 'selected="selected"':'';?>>
						   	&nbsp;&nbsp;&nbsp;&nbsp;--<?php echo $subsub_red['title_hr'];?>
							</option>

							<?php 
								foreach ($subsubsub_city as $subsubsub_red) {
								$subsubsubsub_city= Db::query("SELECT * FROM city WHERE parent_id=".$subsubsub_red['id']." ORDER BY orderby ASC");
							?>
							<option value="<?php echo $subsubsub_red['id']?>" <?php echo ($data['city_id'] == $subsubsub_red['id'])? 'selected="selected"':'';?>>
								   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---<?php echo $subsubsub_red['title_hr'];?>
							</option>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</select>	
			
			
			<h4>Adresa</h4>
			<!-- <input type="text" name="address" id="address" value="<?php echo $data['address']; ?>"/> -->
			<?php
			if($show['new']){
			?>
			<h4>Izdvoji kao NOVO</h4>
			
			<select name="novo" id="novo">
				<option value="ne" <?php echo ($data['novo'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['novo'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<?php
			}
			?>
			
					
			
			
			<?php
			if($show['multiple_category']){
			$kategorije = Db::query('SELECT title_hr, id FROM categories ORDER BY orderby ASC');
			$checked = explode(",", $data['multi_categories']);
			?>
				<h4>Kategorija:</h4>
				
				<input type="checkbox" name="multi_categories[]" id="cat-0" value="0" <?php echo (in_array(0, $checked))? 'checked="checked"':''; ?> onchange="if(this.checked){$('.multi_categories').prop('checked', false);}">
				<label for="cat-0">Nije u kategoriji</label>
				<div class="clearfix"></div>
				<?php
				foreach($kategorije as $kat){
				?>
					<input type="checkbox" class="multi_categories" name="multi_categories[]" id="cat-<?php echo $kat['id']; ?>" value="<?php echo $kat['id']; ?>" <?php echo (in_array($kat['id'], $checked))? 'checked="checked"':''; ?> onchange="if(this.checked){$('#cat-0').prop('checked', false);}">
					<label for="cat-<?php echo $kat['id']; ?>"><?php echo $kat['title_hr']; ?></label>
					<div class="clearfix"></div>	
				<?php 
				} 
			}
			?>
		</div>
		<?php } ?>
		
		<?php/*
		
		$featured_img = Db::query_one('SELECT photo_name FROM site_photos WHERE id = '.$data['image']);
		?>
		<div class="box-25 light">
			<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
			<h3>Istaknuta slika</h3>
			
				
			<?php
			if( isset($_GET['action']) && isset($_GET['id']) && ! $_POST ){
			?>
				<a href="javascript:;" onclick="sjx('open_img_selection', '<?php echo $crud->table; ?>', <?php echo $id; ?>);return false;" class="set_featured_img">Postavi istaknutu sliku</a>
			<?php }else{ ?>
				<div class="no_entry" style="font-size:14px;">Stranicu prvo morate spremiti kako bi mogli postaviti istaknutu sliku.</div>
			<?php } ?>
		
			<div id="featured_holder" class="featured-holder">
				<?php
				if($featured_img){
				?>
					<a href="javascript:;" onclick="$('#image').val('0');$('#featured_holder').empty();" class="del_img"><img src="images/icon-delete-round.png" alt="Briši" /></a>
					<img class="featured_img" src="../upload_data/site_photos/th_<?php echo $featured_img; ?>" alt="" />
				<?php } ?>
			</div>
			<input type="hidden" name="image" id="image" value="<?php echo $data['image']; ?>" />
		</div>
		<?php
		*/
		?>
	<div class="box-25 last light">	
			<h4>Broj soba</h4>
			

			<select name="rooms2" id="rooms2">
				<option value="0" <?php echo ($data['rooms2'] == '0')? 'selected="selected"':'';?>>nije odabrano </option>
				<option value="1" <?php echo ($data['rooms2'] == '1')? 'selected="selected"':'';?>>1</option>
				<option value="2" <?php echo ($data['rooms2'] == '2')? 'selected="selected"':'';?>>2</option>
				<option value="3" <?php echo ($data['rooms2'] == '3')? 'selected="selected"':'';?>>3</option>
				<option value="4+" <?php echo ($data['rooms2'] == '4+')? 'selected="selected"':'';?>>4+</option>
			</select>


			<h4>Broj kupaonica</h4>
			<input type="text" name="bathrooms" id="bathrooms" value="<?php echo $data['bathrooms']; ?>"/>
			
			
			<h4>Površina</h4>
			<input type="text" name="quadrature1" id="quadrature1" value="<?php echo $data['quadrature1']; ?>"/>
			<h4>Broj etaža</h4>
			<input type="text" name="etaze" id="etaze" value="<?php echo $data['etaze']; ?>"/>
			<h4>Katnost </h4>
			<?php 
				$katnost= Db::query("SELECT * FROM katnost ORDER BY orderby ASC");
			?>
			<select name="floor" id="floor">
				<option value=""> -- </option>
				<?php foreach ($katnost as $red) {
				?>
				<option value="<?php echo $red['id']?>" <?php echo ($data['floor'] == $red['id'])? 'selected="selected"':'';?>>
					<?php echo $red['title_hr'];?>
				</option>
				<?php } ?>
			</select>

			<h4>Namještenost</h4>
			<?php 
				$namjestenost= Db::query("SELECT * FROM namjestenost ORDER BY orderby ASC");
			?>
			<select name="namjestenost_id" id="namjestenost_id">
				<option value=""> -- </option>
				<?php foreach ($namjestenost as $red) {
				?>
				<option value="<?php echo $red['id']?>" <?php echo ($data['namjestenost_id'] == $red['id'])? 'selected="selected"':'';?>>
					<?php echo $red['title_hr'];?>
				</option>
				<?php } ?>
			</select>

			<h4>Grijanje</h4>
			<?php 
			$grijanje= Db::query("SELECT * FROM grijanje ORDER BY orderby ASC");
			?>

			<select name="heating_id" id="heating_id">
				<option value=""> -- </option>
				<?php foreach ($grijanje as $red) {
				?>
					<option value="<?php echo $red['id']?>" <?php echo ($data['heating_id'] == $red['id'])? 'selected="selected"':'';?>>
						<?php echo $red['title_hr'];?>
					</option>
					<?php } ?>
			</select>

			

			<h4>Godina izgradnje</h4>
			<input type="text" name="year_built" id="year_built" value="<?php echo $data['year_built']; ?>"/>

			<h4>Godina adaptacije</h4>
			<input type="text" name="adaptacija" id="adaptacija" value="<?php echo $data['adaptacija']; ?>"/>

			<h4>Energetski certifikat</h4>
			<?php 
			$energetski_certifikat= Db::query("SELECT * FROM energetski_certifikat ORDER BY orderby ASC");
			?>
			<select name="energy_cert" id="energy_cert">
				<option value=""> -- </option>
				<?php foreach ($energetski_certifikat as $red) {
				?>
					<option value="<?php echo $red['id']?>" <?php echo ($data['energy_cert'] == $red['id'])? 'selected="selected"':'';?>>
						<?php echo $red['title_hr'];?>
					</option>
					<?php } ?>
			</select>
		</div>
		<div class="box-25 last light">
			<h4>Parkiralište</h4>
			
			<div class="box-25 light last" style="padding: 9px 18px 4px 0px; margin-top: 0; width: 100%;margin-bottom:5px;margin-left: 0%;border: 0 none; <?php echo($data['garage'] == 'da')? " background:#7ac143; color: #fff;" : "";?>">
				<input type="hidden" name="garage" value="ne">
				<input type="checkbox" class="arhiva" <?php echo($data['garage'] == 'da')? "checked" : "";?> name="garage" value="da" id="garage" onchange="if(this.checked){$('#garage').prop('checked', true);}else{$('#garage').prop('checked', false);}">
				<label class="" style="margin-bottom: 8px;font-size: 14px; margin-top: 3px; <?php echo($data['garage'] == 'da')? " color: #fff;" : "";?>" for="garage">Garaža</label>
			</div>
			<input type="text" name="garage_broj" id="garage_broj" placeholder="Broj garaža" value="<?php echo $data['garage_broj']; ?>"/>

			<div class="box-25 light last" style="padding: 9px 18px 4px 0px; margin-top: 0; width: 100%;margin-bottom:5px;margin-left: 0%;border: 0 none; <?php echo($data['parking'] == 'da')? " background:#7ac143; color: #fff;" : "";?>">
				<input type="hidden" name="parking" value="ne">
				<input type="checkbox" class="arhiva" <?php echo($data['parking'] == 'da')? "checked" : "";?> name="parking" value="da" id="parking" onchange="if(this.checked){$('#parking').prop('checked', true);}else{$('#parking').prop('checked', false);}">
				<label class="" style="margin-bottom: 8px;font-size: 14px; margin-top: 3px; <?php echo($data['parking'] == 'da')? " color: #fff;" : "";?>" for="parking">Parkirno mjesto</label>
			</div>
			<input type="text" name="parking_broj" id="parking_broj" placeholder="Broj parkirnih mjesta" value="<?php echo $data['parking_broj']; ?>"/>
		</div>
		<div class="box-25 last light">
			<h4>Cijena</h4>
			<input type="text" name="price" id="price" value="<?php echo $data['price']; ?>"/>
			<h4>Akcijska cijena</h4>
			<input type="text" name="action_price" id="action_price" value="<?php echo $data['action_price']; ?>"/>
			
		</div>
		<div class="box-25 last light">
			<h4>Klima uređaj</h4>
			<select name="klima" id="klima">
				<option value="ne" <?php echo ($data['klima'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['klima'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<h4>Novogradnja</h4>
			<select name="novogradnja" id="novogradnja">
				<option value="ne" <?php echo ($data['novogradnja'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['novogradnja'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<h4>Lift</h4>
			<select name="lift" id="lift">
				<option value="ne" <?php echo ($data['lift'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['lift'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<h4>Balkon/terasa/lođa</h4>
			<select name="balkon" id="balkon">
				<option value="ne" <?php echo ($data['balkon'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['balkon'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<h4>Vrt</h4>
			<select name="vrt" id="vrt">
				<option value="ne" <?php echo ($data['vrt'] == 'ne')? 'selected="selected"':'';?>>Ne</option>
				<option value="da" <?php echo ($data['vrt'] == 'da')? 'selected="selected"':'';?>>Da</option>
			</select>
			<h4>Orjentacija</h4>
			<select name="orjentacija" id="orjentacija">
			<option value="">-</option>
				<option value="1" <?php echo ($data['orjentacija'] == '1')? 'selected="selected"':'';?>>sjever</option>
				<option value="2" <?php echo ($data['orjentacija'] == '2')? 'selected="selected"':'';?>>jug</option>
				<option value="3" <?php echo ($data['orjentacija'] == '3')? 'selected="selected"':'';?>>istok</option>
				<option value="4" <?php echo ($data['orjentacija'] == '4')? 'selected="selected"':'';?>>zapad</option>
			</select>

		</div>

		<div class="box-25 last">
			<h4>Agent</h4>
			<?php 
			$our_team= Db::query("SELECT * FROM our_team ORDER BY orderby ASC");
			?>

			<select name="agent_id" id="agent_id">
				<option value=""> -- </option>
				<?php foreach ($our_team as $red) {
				?>
					<option value="<?php echo $red['id']?>" <?php echo ($data['agent_id'] == $red['id'])? 'selected="selected"':'';?>>
						<?php echo $red['title_hr'];?>
					</option>
					<?php } ?>
			</select>
		</div>
		<?php
		if($show['video']){
		?>
		<div class="box-25 last light">
			<a class="toggle" href="javascript:;">Otvori / Zatvori</a>
			<h3>Youtube video</h3>
			
			<?php
			if($show['video_title']){
				if(sizeof($column_name) == 1)
				{
				?>
					<h4>Naslov videa</h4>
					<input type="text" name="video_title_hr" id="video_title_hr" value="<?php echo $data['video_title_hr']; ?>">
				<?php
				}else{
					for($i = 0; $i < sizeof($column_name); $i++)
					{
					?>
					<h4>Naslov videa <span>(<?php echo $lang_label[$i];?>)</span></h4>
					<input type="text" name="video_title_<?php echo strtolower($lang_label[$i]);?>" id="video_title_<?php echo strtolower($lang_label[$i]);?>" value="<?php echo isset($data['video_title_'.strtolower($lang_label[$i])])? $data['video_title_'.strtolower($lang_label[$i])] : '' ; ?>" />
					<?php
					}
				}
			}
			?>
			
			<h4>URL</h4>
			<input type="text" name="video_url" id="video_url" value="<?php echo $data['video_url']; ?>">
				
			<?php
			if($data['video_url'] != ''){
			?>
				<iframe width="94%" height="315" src="<?php echo generiraj_youtube_embed_link($data['video_url']); ?>?wmode=transparent" frameborder="0" allowfullscreen></iframe>
			<?php
			}
			?>
			<div class="clearfix"></div>
		</div>
		<?php
		}
		?>
		<div class="clearfix"></div>	
		<div class="save">
			<div class="submit-wrapper">
				<input type="submit" name="spremi3" class="spremi_s" value="Spremi" />
			</div>
			<div class="submit-wrapper">
				<input type="submit" name="spremi2" class="spremi_pr" value="Spremi i pregledaj sve" />
			</div>
		</div>
	</form>
	
	<!-- KRAJ FORME, DALJE SU BOXOVI ZA UPLOAD SLIKA -->
	<div class="clearfix"></div>
	<div class="upload-box slike-upload" style="display:none;">
		<script>
			Dropzone.options.imageUpload = {
				paramName: "images",
				maxFiles: <?php echo ($crud->img_num === null)? 'null':($crud->img_num - count($imgs)); ?>,
				init: function(){
					this.on("complete", function(file){
						if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
							$(".save-uploads").show();
						}
					});
				}
			};
		</script>
		<div class="upload-container">
			<form action="include/php/upload.php" class="dropzone" id="image-upload">
				<div class="fallback">
					Vaš preglednik je zastario. Molimo vas ažurirajte ga kako bi nesmetano mogli prenositi slike i dokumente.
				</div>
			</form>
			<div class="set-image save-uploads" style="display:none;">
				<?php
				if( isset($_GET['action']) && isset($_GET['id']) && ! $_POST ){
				?>
					<a href="javascript:;" onclick="$(this).hide();$('.loader').show();sjx('save_uploads', '<?php echo $crud->table; ?>', <?php echo $crud->id; ?>,  '<?php echo implode("-", $crud->img_sizes); ?>'); return false;">Spremi prenesene dokumente</a>
					<div class="loader" style="display:none;">Pohrana u tijeku...</div>
				<?php }else{ ?>
					<a href="javascript:;" onclick="sjx('preview_uploads', 'images'); return false;">U redu</a>
				<?php } ?>
			</div>
		</div>
		<a href="javascript:;" onclick="sjx('close_upload','slike-upload'); return false;" class="del_img"><img src="images/icon-delete-round.png" alt="Zatvori" /></a>
	</div>
		
		
	<?php
	if(sizeof($column_name) == 1)
	{
	?>
	<div class="upload-box dokumenti-upload-hr" style="display:none;">
		<script>
			Dropzone.options.fileHrUpload = {
				paramName: "file-hr",
				maxFiles: <?php echo ($crud->files_num === null)? 'null':($crud->files_num - count($files['hr'])); ?>,
				init: function(){
					this.on("complete", function(file){
						if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
							$(".save-uploads").show();
						}
					});
				}
			};
		</script>
		<div class="upload-container">
			<form action="include/php/upload.php" class="dropzone" id="file-hr-upload">
				<div class="fallback">
					Vaš preglednik je zastario. Molimo vas ažurirajte ga kako bi nesmetano mogli prenositi slike i dokumente.
				</div>
			</form>
			<div class="set-image save-uploads">
				<?php
				if( isset($_GET['action']) && isset($_GET['id']) && ! $_POST ){
				?>
					<a href="javascript:;" onclick="$(this).hide();$('.loader').show();sjx('save_uploads', '<?php echo $crud->table; ?>', <?php echo $crud->id; ?>,  '<?php echo implode("-", $crud->img_sizes); ?>'); return false;">Spremi prenesene dokumente</a>
					<div class="loader" style="display:none;">Pohrana u tijeku...</div>
				<?php }else{ ?>
					<a href="javascript:;" onclick="sjx('preview_uploads', 'file-hr'); return false;">U redu</a>
				<?php } ?>
			</div>
		</div>
		<a href="javascript:;" onclick="sjx('close_upload','dokumenti-upload-hr'); return false;" class="del_img"><img src="images/icon-delete-round.png" alt="Zatvori" /></a>
	</div>
	<?php
	}else{
		for($i = 0; $i < sizeof($column_name); $i++)
		{
		?>
		<div class="upload-box dokumenti-upload-<?php echo strtolower($lang_label[$i]);?>" style="display:none;">
			<script>
				Dropzone.options.file<?php echo ucfirst(strtolower($lang_label[$i]));?>Upload = {
					paramName: "file-<?php echo strtolower($lang_label[$i]);?>",
					maxFiles: <?php echo ($crud->files_num === null)? 'null':($crud->files_num - count($files[strtolower($lang_label[$i])])); ?>,
					init: function(){
						this.on("complete", function(file){
							if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
								$(".save-uploads").show();
							}
						});
					}
				};
			</script>
			<div class="upload-container">
				<form action="include/php/upload.php" class="dropzone" id="file-<?php echo strtolower($lang_label[$i]);?>-upload">
					<div class="fallback">
						Vaš preglednik je zastario. Molimo vas ažurirajte ga kako bi nesmetano mogli prenositi slike i dokumente.
					</div>
				</form>
				<div class="set-image save-uploads">
				<?php
				if( isset($_GET['action']) && isset($_GET['id']) && ! $_POST ){
				?>
					<a href="javascript:;" onclick="$(this).hide();$('.loader').show();sjx('save_uploads', '<?php echo $crud->table; ?>', <?php echo $crud->id; ?>,  '<?php echo implode("-", $crud->img_sizes); ?>'); return false;">Spremi prenesene dokumente</a>
					<div class="loader" style="display:none;">Pohrana u tijeku...</div>
				<?php }else{ ?>
					<a href="javascript:;" onclick="sjx('preview_uploads', 'file-<?php echo strtolower($lang_label[$i]);?>'); return false;">U redu</a>
				<?php } ?>
			</div>
			</div>
			<a href="javascript:;" onclick="sjx('close_upload','dokumenti-upload-<?php echo strtolower($lang_label[$i]);?>'); return false;" class="del_img"><img src="images/icon-delete-round.png" alt="Zatvori" /></a>
			content
		</div>
	<?php 
		}
	} ?>
<div class="clearfix"></div>	
<?php include('include/php/footer.php'); ?>
