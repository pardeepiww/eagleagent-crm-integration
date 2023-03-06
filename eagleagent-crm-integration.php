<?php
/*
 * Plugin Name: Eagleagent CRM Integration
 * Plugin URI:  
 * Description: A plugin that makes an API request to eagleagent.com.au to fetch property details.
 * Version: 1.0.0
 * Author: Yasir Majeed
 * Whatsapp: +92-314-6850-461
 * Author URI:  https://github.com/yasirmajeed1991/eagleagent-crm-integration
 */

function my_plugin_fetch_data() {
    
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.eagleagent.com.au/api/v3/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer 2fc011962cb141cce76891fa:7f5aea32888fdcfee4a7be753616ec65eba076b5803e4bc1a543dc1059f136a3"
  ),
));

$response = curl_exec($curl);

$response_data = json_decode($response, true);
 $token = $response_data['data']['token']['token'];
 $expiry = $response_data['data']['token']['expiresAt'];
 curl_close($curl);
 

 // Second API request with received token and GraphQL query
    
 if(time() > $expiry) {
     
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.eagleagent.com.au/api/v3/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer 2fc011962cb141cce76891fa:7f5aea32888fdcfee4a7be753616ec65eba076b5803e4bc1a543dc1059f136a3"
  ),
));

$response = curl_exec($curl);

$response_data = json_decode($response, true);
 $token = $response_data['data']['token']['token'];
 $expiry = $response_data['data']['token']['expiresAt'];
 curl_close($curl);
}


 // Second API request with received token and GraphQL query
 $query = 'query GetEmailTemplates { properties { nodes { id    
    headline
    formattedAddress   
    advertisedPrice
     status
    thumbnailSquare
	listingDetails {
                ... on Business {
                    businessName
                    floorArea
                }
                ... on Commercial {
                    totalCarSpaces
                    warehouseArea
                }
                ... on Land {
                    frontage
                    rearDepth
                }
                ... on ResidentialRental {
                    bedrooms
                    bathrooms
                    garageSpaces
                }
                ... on ResidentialSale {
                    bedrooms
                    bathrooms
                    openCarSpaces
                    houseSizes
                }
                ... on Rural {
                    bathrooms
                    bedrooms
                }
            }
       
        } } } ';
$headers = array(
    'Authorization' => 'Bearer ' . $token,
    'Content-Type' => 'application/json'
);
$body = array(
    'query' => $query
);
$response = wp_remote_post( 'https://www.eagleagent.com.au/api/v3/graphql', array( 'headers' => $headers, 'body' => json_encode($body) ) );
$response_data = json_decode(wp_remote_retrieve_body($response), true);
return $response_data;
}


add_shortcode( 'my_plugin_data', 'my_plugin_display_data' );
function my_plugin_display_data() {
    $data = my_plugin_fetch_data();
	$nodes = $data['data']['properties']['nodes'];
	$nodeCount = count($nodes);
	$in = 2;
	$count = 0;
	
	$output .='
	<style>
		.crm-container {
		  display: flex;
		  flex-wrap: wrap;
		  justify-content: center;
		  border: 2px solid #424A4F;
		   background-color: #f2f2f2;
		  font-size: 14px;
		  
		}
		@media (max-width: 767px) {
  .crm-container {
    margin-right: 0;
  }
  .column {
		  width: 100%;
		  padding: 10px;
		  margin: 10px;
		 

		}
}

		.row {
		  display: flex;
		  flex-wrap: wrap;

		}

		.column {
		  width: 250px;
		  padding: 10px;
		  margin: 10px;
		  background-color: #f2f2f2;

		}
		select {
		  width: 100%;
		  padding: 5px 10px;
		  margin: 5px 0;
		  border: 1px solid #ccc;
		  border-radius: 4px;
		  box-sizing: border-box;
		}

		label {
		  display: block;
		  margin-bottom: 10px;
		}
		.search-button {
		  background-color: black;
		  color: white;
		  padding: 9px 65px;
		  font-size: 14px;
		  border: none;
		  cursor: pointer;
		  margin-top: 19px;
		}
		.search-buttonl {
		  width: 250px;
		  
		  margin: 10px;
		  padding-top: 15px;
		  text-align: center;
		}

	</style>
	
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $(".select2").select2();
  });
</script>
	<div class="et_pb_row et_pb_row_'.$in.' et_pb_equal_columns et_pb_gutters2 et_had_animation"  style="padding: 5px 0 !important; margin-bottom: 30px;">
<form method="POST" action="https://smproperty.co.nz/current-developments/">
<div class="crm-container">
  <div class="row">
    <div class="column">
      <label for="option1">Search suburb</label>
      <select id="option1" class="select2" name="searchSuburb"><option value="">Any</option>';
	
			function extractInfoAfterLastComma($str) {
			  $substrings = explode(',', $str);
			  $lastSubstring = end($substrings);
			  $infoAfterLastComma = trim($lastSubstring);
			  return $infoAfterLastComma;
			}
	
			function removeDollarAndComma($value) {
				// Use str_replace to replace the dollar sign with an empty string
				$value = str_replace('$', '', $value);

				// Use str_replace again to remove any commas
				$value = str_replace(',', '', $value);

				// Return the updated value
				return $value;
			}
			  $storedValue = array(); // initialize $storedValue outside of the loop

			$selectedsearchSuburb = isset($_POST['searchSuburb']) ? $_POST['searchSuburb'] : '';

			for ($b = 0; $b < $nodeCount; $b++) {
			  if ($nodes[$b]['status'] == 'ACTIVE') { 
				$inputString = $nodes[$b]['formattedAddress'];
				$infoAfterLastComma = extractInfoAfterLastComma($inputString);
				$selected = '';
				if (isset($_POST['searchSuburb']) && $_POST['searchSuburb'] == $infoAfterLastComma) {
				  $selected = 'selected';
				}
				if (!in_array($infoAfterLastComma, $storedValue)) {
				  $storedValue[] = $infoAfterLastComma;
				  $output .= '<option value="'.$infoAfterLastComma.'" '.$selected.'>'.$infoAfterLastComma.'</option>';
				}
			  }
			}

    
			$output .='
			</select>
    </div>
    
    <div class="column">
      <label for="option4">Bedrooms</label>
      <select id="option4" class="select2" name="bedrooms"><option value="">Any</option>';
	
	$selectedBedrooms = isset($_POST['bedrooms']) ? $_POST['bedrooms'] : '';

	for ($i = 1; $i <= 8; $i++) {
		$value = ($i === 8) ? '8+' : $i;
		$selected = ($selectedBedrooms == $value) ? 'selected' : '';
		$output.= "<option value='$value' $selected>$value</option>";
	}
	
        $output .='
      </select>
    </div>
	<div class="column">
      <label for="option5">Bathrooms</label>
      <select id="option5" class="select2" name="bathrooms">
        <option value="">Any</option>';
	$selectedBathrooms = isset($_POST['bathrooms']) ? $_POST['bathrooms'] : '';

	for ($i = 1; $i <= 8; $i++) {
		$value = ($i === 8) ? '8+' : $i;
		$selected = ($selectedBathrooms == $value) ? 'selected' : '';
		$output.= "<option value='$value' $selected>$value</option>";
	}
		$output.='
      </select>
    </div>
	 <div class="column">
      <label for="option6">Min Price</label>
      <select id="option6" class="select2" name="minPrice">
        <option value="">$ 0</option>';
		$selectedminPrice = isset($_POST['minPrice']) ? $_POST['minPrice'] : '';

		$priceOptions = array(
		'100000' => '$100k',
		'150000' => '$150k',
		'200000' => '$200k',
		'300000' => '$300k',
		'400000' => '$400k',
		'500000' => '$500k',
		'600000' => '$600k',
		'700000' => '$700k',
		'800000' => '$800k',
		'1000000' => '$1.0M',
		'1500000' => '$1.5M',
		'2000000' => '$2.0M',
		'2500000' => '$2.5M',
		'3000000' => '$3.0M',
		'3500000' => '$3.5M',
		'4000000' => '$4.0M',
		'4500000' => '$4.5M',
		'5000000' => '$5.0M',
		);

		foreach ($priceOptions as $value => $label) {
		$selected = ($selectedminPrice == $value) ? 'selected' : '';
		$output .= "<option value='$value' $selected>$label</option>";
		}
		
		
		
     $output .=' </select>
    </div>
  </div>
  <div class="row">
    
   
    <div class="column">
      <label for="option7">Max Price</label>
      <select id="option7" class="select2" name="maxPrice">
        <option value="">Any</option>';
		$selectedmaxPrice = isset($_POST['maxPrice']) ? $_POST['maxPrice'] : '';

		$priceOptions = array(
		'100000' => '$100k',
		'150000' => '$150k',
		'200000' => '$200k',
		'300000' => '$300k',
		'400000' => '$400k',
		'500000' => '$500k',
		'600000' => '$600k',
		'700000' => '$700k',
		'800000' => '$800k',
		'1000000' => '$1.0M',
		'1500000' => '$1.5M',
		'2000000' => '$2.0M',
		'2500000' => '$2.5M',
		'3000000' => '$3.0M',
		'3500000' => '$3.5M',
		'4000000' => '$4.0M',
		'4500000' => '$4.5M',
		'5000000' => '$5.0M',
		);

		foreach ($priceOptions as $value => $label) {
		$selected = ($selectedmaxPrice == $value) ? 'selected' : '';
		$output .= "<option value='$value' $selected>$label</option>";
		}
	
	
		$output .='
		</select>
    </div>
    <div class="search-buttonl">
       <button class="search-button " type="submit">Search</button>
    </div>
	 <div class="column">
	 </div>
	 <div class="column">
	 </div>
  </div>
</div>
</div></form>';
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			for ($i = 0; $i < $nodeCount; $i++) {
				if ($nodes[$i]['status']=='ACTIVE'){ 
					if ($count % 3 == 0) {
						$output .='<div class="et_pb_row et_pb_row_'.$in.' et_pb_equal_columns et_pb_gutters2 et_had_animation"  style="padding: 20px 0 !important;">';
						$in = $in + 1;
					}
				$output .= '
					<div class="et_pb_column et_pb_column_1_3 et_pb_column_2 et_pb_css_mix_blend_mode_passthrough" style="pointer-events: none;">
						<div class="et_pb_module et_pb_divider_0 et_pb_space et_pb_divider_hidden "  style="background-image: linear-gradient(180deg,rgba(253,244,222,0) 0%,rgba(253,244,222,0) 
						80%,#fdf4de 	98%),url('.$nodes[$i]['thumbnailSquare'].') !important;">
						</div>
						<div class="et_pb_module et_pb_text et_pb_text_1 dev-header  et_pb_text_align_left et_pb_bg_layout_light"  >
							<div class="et_pb_text_inner">
								<h3>'.$nodes[$i]['headline'].'</h3>
							</div>
						</div>
						<div class="et_pb_module et_pb_text et_pb_text_2 dev-desc  et_pb_text_align_left et_pb_bg_layout_light"  >
							<div class="et_pb_text_inner" >
								<p>Built for durability and located near amenities for convenience. </p>
							</div>
						</div>
						<div class="et_pb_module et_pb_text et_pb_text_3  et_pb_text_align_left et_pb_bg_layout_light" >
							<div class="et_pb_text_inner">
								<p>'.$nodes[$i]['formattedAddress'].'</p>
							</div>
						</div>
						<div class="et_pb_module et_pb_divider et_pb_divider_1 et_pb_divider_position_ et_pb_space" >
							<div class="et_pb_divider_internal"></div>
						</div>
						<div class="et_pb_module et_pb_text et_pb_text_4  et_pb_text_align_left et_pb_bg_layout_light" >
							<div class="et_pb_text_inner">
								<p>Starting at '.$nodes[$i]['advertisedPrice'].'</p>
							</div>
						</div>
						<div class="et_pb_button_module_wrapper et_pb_button_0_wrapper et_pb_button_alignment_right et_pb_module et_had_animation"
							style="" >
							<a class="et_pb_button et_pb_button_0 reverse et_pb_bg_layout_light" style="position: relative; pointer-events: auto;"
								href="https://smproperty.co.nz/property-detail/?id='.$nodes[$i]['id'].'" data-icon="$" id="click8596">See Details</a>
						</div>
					</div>';	
					$count++;
					if ($count % 3 == 0 || ($i + 1) === $nodeCount)  {
						$output .='</div> ';
					}
				}
			}
	 }
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$loopWorked = 0;
			for ($i = 0; $i < $nodeCount; $i++) {
				if ($nodes[$i]['status']=='ACTIVE'){ 
					
					$searchSuburb=$_POST['searchSuburb'];
					$bedroom=$_POST['bedrooms'];
					$bathroom=$_POST['bathrooms'];
					$minPrice=$_POST['minPrice'];
					$maxPrice=$_POST['maxPrice'];
					$addressAfterLastComma = extractInfoAfterLastComma($nodes[$i]['formattedAddress']);
					$dollarSignRemoved = removeDollarAndComma($nodes[$i]['advertisedPrice']);
					$bedrooms = $nodes[$i]['listingDetails']['bedrooms'];
					$bathrooms = $nodes[$i]['listingDetails']['bathrooms'];
					if ((empty($searchSuburb) || $searchSuburb == $addressAfterLastComma)
						&& (empty($bedroom) || $bedroom == $bedrooms)
						&& (empty($bathroom) || $bathroom == $bathrooms)
						&& (empty($minPrice) || $dollarSignRemoved >= $minPrice)
						&& (empty($maxPrice) || $dollarSignRemoved <= $maxPrice)
					){
							if ($count % 3 == 0) {
								$output .='<div class="et_pb_row et_pb_row_'.$in.' et_pb_equal_columns et_pb_gutters2 et_had_animation"  style="padding: 20px 0 !important;">';
								$in = $in + 1;
							}
							$output .= '
							<div class="et_pb_column et_pb_column_1_3 et_pb_column_2 et_pb_css_mix_blend_mode_passthrough" style="pointer-events: none;">
								<div class="et_pb_module et_pb_divider_0 et_pb_space et_pb_divider_hidden "  style="background-image: linear-gradient(180deg,rgba(253,244,222,0) 0%,rgba(253,244,222,0) 
								80%,#fdf4de 	98%),url('.$nodes[$i]['thumbnailSquare'].') !important;">
								</div>
								<div class="et_pb_module et_pb_text et_pb_text_1 dev-header  et_pb_text_align_left et_pb_bg_layout_light"  >
									<div class="et_pb_text_inner">
										<h3>'.$nodes[$i]['headline'].'</h3>
									</div>
								</div>
								<div class="et_pb_module et_pb_text et_pb_text_2 dev-desc  et_pb_text_align_left et_pb_bg_layout_light"  >
									<div class="et_pb_text_inner" >
										<p>Built for durability and located near amenities for convenience. </p>
									</div>
								</div>
								<div class="et_pb_module et_pb_text et_pb_text_3  et_pb_text_align_left et_pb_bg_layout_light" >
									<div class="et_pb_text_inner">
										<p>'.$nodes[$i]['formattedAddress'].'</p>
									</div>
								</div>
								<div class="et_pb_module et_pb_divider et_pb_divider_1 et_pb_divider_position_ et_pb_space" >
									<div class="et_pb_divider_internal"></div>
								</div>
								<div class="et_pb_module et_pb_text et_pb_text_4  et_pb_text_align_left et_pb_bg_layout_light" >
									<div class="et_pb_text_inner">
										<p>Starting at '.$nodes[$i]['advertisedPrice'].'</p>
									</div>
								</div>
								<div class="et_pb_button_module_wrapper et_pb_button_0_wrapper et_pb_button_alignment_right et_pb_module et_had_animation"
									style="" >
									<a class="et_pb_button et_pb_button_0 reverse et_pb_bg_layout_light" style="position: relative; pointer-events: auto;"
										href="https://smproperty.co.nz/property-detail/?id='.$nodes[$i]['id'].'" data-icon="$" id="click8596">See Details</a>
								</div>
							</div>';	

							$count++;
						$loopWorked = 1;
						if($loopWorked ==1){
						if ($count % 3 == 0 || ($i + 1) === $nodeCount)  {
							$output .='</div> ';
						}
				}
						
						
					} 
					
				}
			}
			if ($loopWorked == 0)
			{
				$output .='<div class="et_pb_row ">
								
										<div class="et_pb_text_inner"><h2>No listings found.</h2></div>
									
							</div>';
			}
		
		
		
	 }
	
			$output .='<script>
						 document.getElementById("213").style.display = "none";
					  </script>
					  <style>
// 						.et_pb_gutters2 .et_pb_column, .et_pb_gutters2.et_pb_row .et_pb_column {
// 							margin-right: 10px !important;
// 						}
					</style>';
		
			return $output;
	
}

// A function that can fetch individual data of specific Property ID and display it on the page
function my_plugin_fetch_data2() {
    
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.eagleagent.com.au/api/v3/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer 2fc011962cb141cce76891fa:7f5aea32888fdcfee4a7be753616ec65eba076b5803e4bc1a543dc1059f136a3"
  ),
));

$response = curl_exec($curl);

$response_data = json_decode($response, true);
 $token = $response_data['data']['token']['token'];
 $expiry = $response_data['data']['token']['expiresAt'];
 curl_close($curl);
 

 // Second API request with received token and GraphQL query
    
 if(time() > $expiry) {
     
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.eagleagent.com.au/api/v3/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer 2fc011962cb141cce76891fa:7f5aea32888fdcfee4a7be753616ec65eba076b5803e4bc1a543dc1059f136a3"
  ),
));

$response = curl_exec($curl);

$response_data = json_decode($response, true);
 $token = $response_data['data']['token']['token'];
 $expiry = $response_data['data']['token']['expiresAt'];
 curl_close($curl);
}



 // Second API request with received token and GraphQL query
 $query = 'query GetEmailTemplates {
    properties {
        nodes {
            id
            headline
            formattedAddress
            listingDetails {
                ... on Business {
                    businessName
                    floorArea
                }
                ... on Commercial {
                    totalCarSpaces
                    warehouseArea
                }
                ... on Land {
                    frontage
                    rearDepth
                }
                ... on ResidentialRental {
                    bedrooms
                    bathrooms
                    garageSpaces
                }
                ... on ResidentialSale {
                    bedrooms
                    bathrooms
                    openCarSpaces
                    houseSizes
                }
                ... on Rural {
                    bathrooms
                    bedrooms
                }
            }
            landSizeUnits
            landSize
            description
            images {
                url
            }
        }
    }
}';
$headers = array(
    'Authorization' => 'Bearer ' . $token,
    'Content-Type' => 'application/json'
);
$body = array(
    'query' => $query
);
$response = wp_remote_post( 'https://www.eagleagent.com.au/api/v3/graphql', array( 'headers' => $headers, 'body' => json_encode($body) ) );
$response_data = json_decode(wp_remote_retrieve_body($response), true);
return $response_data;
}


add_shortcode( 'my_plugin_data2', 'my_plugin_display_data2' );
function my_plugin_display_data2() {
	$url_id = $_GET['id'];
    $data = my_plugin_fetch_data2();
    
    foreach ($data['data']['properties']['nodes'] as $node) {

		if ($node['id'] == $url_id)
		{
// 			add_shortcode( 'property_address', 'property_address_function' );
//  function property_address_function() {
//     return $node['formattedAddress'];
// }

        $output .= '<style>
    div.et_pb_section.et_pb_section_0 {
        background-color: #d9cfc4;
    }

    #header:after {
        position: absolute;
        bottom: 0;
        left: 0;
        content: "";
        width: 100%;
        height: 130px;

    }

    #header .hero__image {
        display: block;
        margin: auto;
		width: 100%
		
    }

	
    @media only screen and (max-width: 768px) {
        #header .hero__image {
            height: auto;
			
        }

        #header .grid .img__grid {
            height: auto;
            width: 75%;
        }
    }

    @media only screen and (max-width: 920px) {
        #header .hero__image {
            height: auto;
			
        }

    }

    @media (max-width: 768px) {
        #post-4839 .et_pb_row {
            width: 95%;
        }
    }

    @media only screen and (max-width: 768px) {
        #header .grid .img__grid {
            height: auto;
            width: 100%;
        }
    }


    @media (max-width: 768px) {
        #header .icons {
            display: flex !important;
            gap: 20px;
        }
    }

    @media (max-width: 500px) {
        #header .icons {
            display: grid !important;
            grid-template-columns: 1fr 1fr 1fr;
            align-items: center;
			gap: 0px;
        }
        #header .hero__image img {
            height: auto;
        }
    }
  
    .main__heading {
        font-weight: 400;
        font-size: 50px;
        line-height: 74px;
        color: #424a4f;
		padding-top: 30px;
    }
    p.address {
        font-weight: 400;
        font-size: 30px;
        line-height: 34px;
        color: #424a4f;
    }
    .icons {
        display: flex;
        gap: 40px;
        margin: 30px 0px;
    }
    .icons svg {
        height: 25px;
    }
    .bed__icon,
    .shower__icon,
    .car__icon,
    .house__icon,
    .scale__icon {
        display: flex;
        position: relative;
        align-items: baseline;
        justify-content: baseline;
        gap: 5px;
    }
    p.number {
        position: relative;
        bottom: 5px;
        font-size: 18px;
        color: #424a4f;
    }
    .img_grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        grid-template-rows: 2fr 0.5fr 0.1fr 2fr;
        gap: 25px;
        z-index: 3;
        margin-right: 100px;
        position: relative;
    }
   
    .img__grid {
        height: 100%;
        width: 100%;
    }
    .para {
        max-width: 932px;
        line-height: 29px;
        display: block;
        margin: auto;
        padding: 40px 30px;
    }
    @media (max-width: 768px) {
        .main__heading {
            font-size: 30px;
            width: fit-content;
        }
        p.address {
            width: fit-content;
        }
        .img_grid {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 20px;
            gap: 20px;
            height: auto;
        }
        .grid {

            width: 100% !important;
            height: auto;
        }
        .grid:nth-child(4),
        .grid:nth-child(8) {
            left: 0;
        }
        .icons {
            gap: 1px;
            height: auto;
            display: block;
        }
    }


	 #masonry-container {     
	 column-count: 3;
         /* number of columns */        
		 column-gap: 0px;
         /* space between columns */
      }
	  
    .masonry-item {
        break-inside: avoid-column;
        /* prevent items from breaking columns */
        margin-bottom: 10px;
         /* space between items */
         display: inline-block;
         /* display items as inline-block elements */
         width: 100%;
         /* width of items */
     }
	 
 	#masonry-container {
     display: flex;
     flex-wrap: wrap;
	 justify-content: space-between;
 	
   	}
	
	.masonry-item img {
  	 height: 250px;
     object-fit: cover;
 	 }
	 
	@media (min-width: 768px) {
  	.masonry-item {
      width: calc(33.33% - 10px);
    	}
  	}
	
   .masonry-item img {
      width: 100%;
      /* width of images */    
		}
		
	






	@media (max-width: 600px) {
       .grid {
            height: auto;
        }
        p.address {
            width: fit-content;
            line-height: 1.2;
        }
        .main__heading {
            line-height: 1.1;
        }
        .icons {
            align-items: center;
            justify-content: space-between;
            height: auto;
        }
        .icons svg {
            height: 20px;
        }
        .bed__icon,
        .shower__icon,
        .car__icon,
        .house__icon,
        .scale__icon {
         flex-direction: column;
         justify-content: center;
        }
    }
	

.lb-outerContainer {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  height: 100% !important;
  overflow: hidden !important;
  background-color: rgba(0, 0, 0, 0.8) !important;
}

.lb-container {
  position: absolute !important;
  top: 50% !important;
  left: 50% !important;
  transform: translate(-50%, -50%) !important;
}

.lb-dataContainer {
  padding: 0 !important;
  background-color: transparent !important;
}

.lb-closeContainer {
  position: fixed !important;
  bottom: 20px !important;
  right: 20px !important;
  text-align: right !important;
  z-index: 9999 !important;
}

.lb-close {
  font-size: 20px !important;
  color: white !important;
  cursor: pointer !important;
  text-decoration: none !important;
}

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/js/lightbox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js" integrity="sha512-zkY9jQ1hrq3U3bOrKj1gQZli6UvV7Cj1KiRf7WYO75oY6xBx6bzKZ1m2IeB+hVjGKjRU7VYW+MvibjnN/fJzjw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/picturefill /3.0.3/picturefill .min.js"></script>


<script>
lightbox.option({
  showImageNumberLabel: false,
  alwaysShowNavOnTouchDevices: true,
  enableKeyboardNav: true,
  
})





			</script>
<div style="height: 0; width: 100%; padding-bottom: 56.25%; background-repeat: no-repeat; background-position: center; background-size: cover; background-image: url('.$node['images'][0]['url'].');">
</div>

<div id="header">
  <div class="hero__image" >
  
</div>
  <h1 class="main__heading">'.$node['headline'].'</h1>
  <p class="address">'.$node['formattedAddress'].'</p>
  <div class="icons">
    <!-- BED ICOn -->
    <div class="bed__icon">
      <svg
        width="34"
        height="24"
        viewBox="0 0 34 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M1.7 15.4286H13.6V7.71429C13.6 6.29464 14.7422 5.14286 16.15 5.14286H28.05C31.3384 5.14286 34 7.82679 34 11.1429V23.1429C34 23.6143 33.6175 24 33.15 24C32.6825 24 32.3 23.6143 32.3 23.1429V20.5714H1.7V23.1429C1.7 23.6143 1.31963 24 0.85 24C0.380587 24 0 23.6143 0 23.1429V0.857143C0 0.383571 0.380587 0 0.85 0C1.31963 0 1.7 0.383571 1.7 0.857143V15.4286ZM32.3 17.1429H1.7V18.8571H32.3V17.1429ZM28.05 6.85714H16.15C15.6825 6.85714 15.3 7.24286 15.3 7.71429V15.4286H32.3V11.1429C32.3 8.775 30.3981 6.85714 28.05 6.85714ZM11.9 9.42857C11.9 11.7964 9.99813 13.7143 7.65 13.7143C5.30294 13.7143 3.4 11.7964 3.4 9.42857C3.4 7.06071 5.30294 5.14286 7.65 5.14286C9.99813 5.14286 11.9 7.06071 11.9 9.42857ZM7.65 6.85714C6.24219 6.85714 5.1 8.00893 5.1 9.42857C5.1 10.8482 6.24219 12 7.65 12C9.05781 12 10.2 10.8482 10.2 9.42857C10.2 8.00893 9.05781 6.85714 7.65 6.85714Z"
          fill="#424A4F"
        />
      </svg>

      <p class="number bed__number">'.$node['listingDetails']['bedrooms'].'</p>
    </div>
    <!-- shower icon -->
    <div class="shower__icon">
      <svg
        width="30"
        height="30"
        viewBox="0 0 30 30"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M27.1875 18.75C26.6693 18.75 26.25 19.1693 26.25 19.6875V21.5625C26.25 24.1471 24.1471 26.25 21.5625 26.25H8.4375C5.85293 26.25 3.75 24.1465 3.75 21.5625V19.6875C3.75 19.1719 3.33047 18.75 2.8125 18.75C2.29453 18.75 1.875 19.1719 1.875 19.6875V21.5625C1.875 23.3437 2.59395 24.9568 3.75 26.1404V29.0625C3.75 29.5781 4.16953 30 4.6875 30C5.20547 30 5.625 29.5781 5.625 29.0625V27.467C6.48047 27.8789 7.42969 28.125 8.4375 28.125H21.5625C22.5727 28.125 23.5189 27.8764 24.375 27.467V29.0625C24.375 29.5802 24.7948 30 25.3125 30C25.8304 30 26.25 29.5802 26.25 29.0625V26.1404C27.4061 24.9568 28.125 23.3432 28.125 21.5625V19.6875C28.125 19.1719 27.7031 18.75 27.1875 18.75ZM29.0625 15H3.75V3.58887C3.75 2.64434 4.51934 1.875 5.46387 1.875C5.86523 1.875 6.35742 2.0584 6.67383 2.37656L8.15742 3.86016C7.125 5.50078 7.3125 7.69336 8.74219 9.12305L9.49688 9.87773C9.31055 10.2305 9.35156 10.6758 9.65039 10.9746C9.83203 11.1563 10.0723 11.25 10.3125 11.25C10.5527 11.25 10.7923 11.1585 10.9752 10.9753L16.6002 5.35031C16.9664 4.9841 16.9664 4.39055 16.6002 4.02492C16.3011 3.72586 15.8566 3.68443 15.5021 3.87363L14.748 3.11836C13.916 2.28926 12.7793 1.875 11.748 1.875C10.957 1.875 10.1719 2.09941 9.48633 2.53477L8.00391 1.05117C7.32422 0.373535 6.42188 0 5.46387 0C3.48516 0 1.875 1.60957 1.875 3.58887V15.0029L0.9375 15C0.419766 15 0 15.4219 0 15.9375C0 16.4531 0.419766 16.875 0.9375 16.875H29.0625C29.5781 16.875 30 16.4531 30 15.9375C30 15.4219 29.5781 15 29.0625 15ZM10.0664 4.44434C10.5176 3.99668 11.1094 3.75 11.748 3.75C12.3867 3.75 12.9762 3.9965 13.4244 4.44434L14.141 5.16094L10.7871 8.51367L10.0664 7.79883C9.14648 6.87305 9.14648 5.36836 10.0664 4.44434Z"
          fill="#424A4F"
        />
      </svg>
      <p class="number shower__number">'.$node['listingDetails']['bathrooms'].'</p>
    </div>
    <!-- CAR ICON -->
    <div class="car__icon">
      <svg
        width="35"
        height="24"
        viewBox="0 0 35 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M35 15.4286V18C35 18.9482 34.218 19.7143 33.25 19.7143H31.4289C31.0133 22.1464 28.8531 24 26.25 24C23.6469 24 21.4867 22.1464 21.0711 19.7143H13.9289C13.5133 22.1464 11.3531 24 8.75 24C6.14688 24 3.98891 22.1464 3.57273 19.7143H1.75C0.783672 19.7143 0 18.9482 0 18V12C0 10.3125 1.24578 8.90893 2.88641 8.57679L5.30742 2.69411C5.97188 1.06714 7.57969 0 9.36797 0H19.3156C20.6445 0 21.9023 0.591964 22.7336 1.60821L28.432 8.58214C32.0961 8.80179 35 11.7375 35 15.4286ZM9.36797 1.71429C8.29609 1.71429 7.33359 2.35446 6.93438 3.33054L4.79227 8.57143H12.25V1.71429H9.36797ZM14 8.57143H26.1789L21.3664 2.67911C20.8688 2.06946 20.1141 1.71429 19.3156 1.71429H14V8.57143ZM29.5914 19.7143C29.7117 19.4411 29.75 19.1518 29.75 18.8571C29.75 18.5625 29.7117 18.2732 29.5914 18C29.2523 16.5214 27.8797 15.4286 26.25 15.4286C24.6203 15.4286 23.2477 16.5214 22.8594 18C22.7883 18.2732 22.75 18.5625 22.75 18.8571C22.75 19.1518 22.7883 19.4411 22.8594 19.7143C23.2477 21.1929 24.6203 22.2857 26.25 22.2857C27.8797 22.2857 29.2523 21.1929 29.5914 19.7143ZM21.0711 18C21.4867 15.5679 23.6469 13.7143 26.25 13.7143C28.8531 13.7143 31.0133 15.5679 31.4289 18H33.25V15.4286C33.25 12.5411 30.8984 10.2857 28 10.2857H3.5C2.53367 10.2857 1.75 11.0518 1.75 12V18H3.57273C3.98891 15.5679 6.14688 13.7143 8.75 13.7143C11.3531 13.7143 13.5133 15.5679 13.9289 18H21.0711ZM12.0914 19.7143C12.2117 19.4411 12.25 19.1518 12.25 18.8571C12.25 18.5625 12.2117 18.2732 12.0914 18C11.7523 16.5214 10.3797 15.4286 8.75 15.4286C7.12031 15.4286 5.74766 16.5214 5.36047 18C5.28828 18.2732 5.25 18.5625 5.25 18.8571C5.25 19.1518 5.28828 19.4411 5.36047 19.7143C5.74766 21.1929 7.12031 22.2857 8.75 22.2857C10.3797 22.2857 11.7523 21.1929 12.0914 19.7143Z"
          fill="#424A4F"
        />
      </svg>

      <p class="number car__number">'.$node['listingDetails']['openCarSpaces'].'</p>
    </div>

    <!-- HOUSE ICON -->
    <div class="house__icon">
      <svg
        width="30"
        height="27"
        viewBox="0 0 30 27"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M29.7188 12.7083C30.0625 13.0104 30.0938 13.4948 29.7448 13.8854C29.4896 14.2292 28.9583 14.2604 28.6146 13.9115L26.6667 12.1927V22.5C26.6667 24.8021 24.8021 26.6667 22.5 26.6667H7.50001C5.19897 26.6667 3.33334 24.8021 3.33334 22.5V12.1927L1.38491 13.9115C1.03959 14.2604 0.51298 14.2292 0.208501 13.8854C-0.0960306 13.4948 -0.0631139 13.0104 0.28199 12.7083L14.4479 0.208437C14.7656 -0.0694792 15.2344 -0.0694792 15.5521 0.208437L29.7188 12.7083ZM7.50001 25H10.8333V16.6667C10.8333 15.7448 11.5781 15 12.5 15H17.5C18.4219 15 19.1667 15.7448 19.1667 16.6667V25H22.5C23.8802 25 25 23.8802 25 22.5V10.7656L15 1.94479L5.00001 10.7656V22.5C5.00001 23.8802 6.1198 25 7.50001 25ZM12.5 25H17.5V16.6667H12.5V25Z"
          fill="#424A4F"
        />
      </svg>

      <p class="number house__number">'.$node['listingDetails']['houseSizes'].'sqm</p>
    </div>
    <!-- SCALE ICON -->
    <div class="scale__icon">
      <svg
        width="26"
        height="26"
        viewBox="0 0 26 26"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M6.19023 12.2484C6.49492 12.1215 6.84023 12.1926 7.07383 12.4262L13.5738 18.9262C13.8074 19.1598 13.8785 19.5051 13.7516 19.8098C13.6246 20.1145 13.3301 20.3125 13 20.3125H6.5C6.05312 20.3125 5.6875 19.9469 5.6875 19.5V13C5.6875 12.6699 5.88555 12.3754 6.19023 12.2484ZM7.3125 14.9602V18.6875H11.0398L7.3125 14.9602ZM3.92336 0.673359L25.3246 22.0746C25.7563 22.5063 26 23.0902 26 23.6996C26 24.9691 24.9691 26 23.6996 26H3.25C1.45488 26 0 24.5426 0 22.75V2.29785C0 1.02883 1.02883 0 2.29785 0C2.90773 0 3.49223 0.242125 3.92285 0.672852L3.92336 0.673359ZM1.625 2.29836V22.75C1.625 23.6488 2.3527 24.375 3.25 24.375H23.6996C24.0754 24.375 24.375 24.0754 24.375 23.6996C24.375 23.5219 24.3039 23.3543 24.177 23.2273L22.1762 21.2215L20.8863 22.5113C20.5715 22.8312 20.0535 22.8312 19.7387 22.5113C19.4188 22.1965 19.4188 21.6785 19.7387 21.3637L21.0285 20.0738L18.1137 17.159L16.8238 18.4488C16.509 18.7687 15.991 18.7687 15.6762 18.4488C15.3563 18.134 15.3563 17.616 15.6762 17.3012L16.966 16.0113L14.0512 13.0965L12.7613 14.3863C12.4465 14.7062 11.9285 14.7062 11.6137 14.3863C11.2937 14.0715 11.2937 13.5535 11.6137 13.2387L12.9035 11.9488L9.98867 9.03398L8.69883 10.3238C8.38398 10.6438 7.86602 10.6438 7.55117 10.3238C7.23125 10.009 7.23125 9.49102 7.55117 9.17617L8.84102 7.88633L5.92617 4.97352L4.63684 6.26133C4.31996 6.58125 3.80555 6.58125 3.48816 6.26133C3.17078 5.94648 3.17078 5.42852 3.48816 5.11367L4.77648 3.82434L2.77418 1.82203C2.64773 1.69609 2.4766 1.625 2.29836 1.625C1.92664 1.625 1.625 1.92664 1.625 2.29836Z"
          fill="#424A4F"
        />
      </svg>

      <p class="number scale__number">'.$node['landSize'].'sqm</p>
    </div>
  </div>
  <div id="masonry-container" >';
			
			
			
        foreach ($node['images'] as $key => $image) {
            $output .= '<div class="masonry-item ">';
            $output .= '<a href="'. $image['url'].'" data-lightbox="image-set" ><img src="' . $image['url'] . '" alt=""></a>';
            $output .= '</div>';
        }
   
			
			
   $output .='
</div>

<div class="paragraph__section">
  <p class="para">
	'.$node['description'].'
	</p>
  
</div>

<script>






</script>


';
 
	}
		
    }
 
    return $output;
	  
}
