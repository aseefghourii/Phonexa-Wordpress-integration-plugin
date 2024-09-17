<?php
/*
Plugin Name: Phonexa Integration with Fluent Forms
Plugin URI: http://sparkingasia.com
Description: This is simple inttegration plugin that integrates Fluent forms to phonexa
Version: 1.0
Author: Aseef Ghouri
Author URI: http://sparkingasia.com
Text Domain: phonexa-fluentforms
Domain Path: /lang/
*/


add_action( 'fluentform_submission_inserted', 'spark_gforms_after_submission');

function spark_gforms_after_submission( $entryId, $formData, $form ){
  // Do stuff.
	  $form_id = $form['id'];

    wp_mail('aseef.ghouri00@gmail.com','test email','successful hook' );
	
	if( $form_id == 6 ){
    
    $debug_email = 'aseef.ghouri00@gmail.com';

    $hasAttorney ='Yes';//$form->get_field_value('dropdown_3');
    $casedescription ='case'; //$form->get_field_value('Comments');
    $accidenttype = 'Bike';//$form->get_field_value('dropdown_1');
    $accidentfault = 'No';//$form->get_field_value('dropdown');
    $physicalinjury = 'Yes';
    $treatedby = 'Yes';//$form->get_field_value('dropdown_4');
    $date = '02-22-2023';//$form->get_field_value('Incident_Date');
    $zip = '99999';//$form->get_field_value('zip_code');
    $firstName = 'aseef';//$form->get_field_value('first_name');
    $lastName = 'ghouri';//$form->get_field_value('last_name');
    $Email = 'aseef@gmail.com';//$form->get_field_value('email_address');
    $phone ='9823976543'; //$form->get_field_value('input_text');
    $tcpa = 'Yes';

		$body = [];
		$body['apiId'] = 'DBC56F840F8E4859B17718AF1F53B1FE';
		$body['apiPassword'] = 'd6eea42e43';
		$body['productId'] = 45;
		$body['hasAttorney'] = $hasAttorney;
		$body['caseDescription'] = $casedescription;
		$body['accidentType'] = $accidenttype;
		$body['accidentFault'] = $accidentfault;
		$body['physicalInjury'] = $physicalinjury;
		$body['treatedBy'] = $treatedby;
		$body['date'] = $date;
		$body['zip'] = $zip;
		$body['userIp'] = spark_get_user_ip();
    $body['userAgent'] = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)'; // need to change this
  
    $body['testMode'] = '1';

    $ping_url = 'https://leads-inst391-client.phonexa.com/ping/';
    $response = wp_remote_post( $ping_url, array(
      'method'      => 'POST',
      'timeout'     => 45,
      'blocking'    => true,
      'body'        => $body
    ));
    
    if ( is_wp_error( $response ) ) {
      $ping_result = 'Ping Body: <pre>' . print_r($body, true) . '</pre>Ping Response: <pre>' . $response->get_error_message() . '<pre>';
	    wp_mail($debug_email, 'Phonexa API Response', $ping_result);
    } else {

      $response_body = wp_remote_retrieve_body( $response );
      if($response_body){
        $response_body = json_decode( $response_body, true );
      }
      $ping_result = 'Ping Body: <pre>' . print_r($body, true) . '</pre>Ping Response: <pre>' . print_r($response_body, true) . '<pre>';
	    wp_mail($debug_email, 'Phonexa API Response', $ping_result);

      $post_url = 'https://leads-inst391-client.phonexa.com/post/';
      
      $body['promise'] = $response_body['promise'];
		  $body['firstName'] = $firstName;
		  $body['lastName'] = $lastName;
		  $body['email'] = $Email;
		  $body['phone'] = $phone;
      $body['tcpa'] = $tcpa;

      $response = wp_remote_post( $post_url, array(
        'method'      => 'POST',
        'timeout'     => 45,
        'blocking'    => true,
        'body'        => $body
      ));
      
      if ( is_wp_error( $response ) ) {
        $post_result = 'Post Body: <pre>' . print_r($body, true) . '</pre>Post Response: <pre>' . $response->get_error_message() . '<pre>';
        wp_mail($debug_email, 'Phonexa API Response', $post_result);
      } else {

        $response_body = wp_remote_retrieve_body( $response );
        if($response_body){
          $response_body = json_decode( $response_body, true );
        }

        $post_result = 'Post Body: <pre>' . print_r($body, true) . '</pre>Post Response: <pre>' . print_r($response_body, true) . '<pre>';
        wp_mail($debug_email, 'Phonexa API Response', $post_result);
      }

    }
	} // End 
}

function spark_get_user_ip() {
  if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
    //check ip from share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } 
  elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    //to check ip is pass from proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return apply_filters( 'wpb_get_ip', $ip );
}