<?php
/**
 * @package Retreat_Registration_For_Formidable_Forms
 * @version 1.6
 */
/*
Plugin Name: Retreat Registration For Formidable Forms
Plugin URI: https://cinecinetique.com
Description: Add validation function to a Formidable form to prevent duplicate applications.
Author: Rija Ménagé
Version: 1.6
Author URI: https://cinecinetique.com
*/

// show an error if there is a registration with a given email for a particular retreat
function two_fields_unique( $errors, $values ) {
  $first_field_id = 141; // registrant email
  $second_field_id = 186; // retreat id

  $simple_email_field_id = 205;
  $simple_retreat_field_id = 249;

  if ( $values['form_id'] == 13  ) {
    $entry_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $values_used = FrmDb::get_col( 'frm_item_metas',
		array( 'item_id !' => $entry_id,
			array( 'or' => 1,
				array( 'field_id' => $first_field_id, 'meta_value' => $_POST['item_meta'][ $first_field_id ] ),
				array( 'field_id' => $second_field_id, 'meta_value' => $_POST['item_meta'][ $second_field_id ] ),
			)
		), 'item_id', array( 'group_by' => 'item_id', 'having' => 'COUNT(*) > 1' )
	);
	if ( ! empty( $values_used ) ) {
		$errors['my_error'] = 'You have already submitted an application for this retreat';
	}
  }
  else if ( $values['form_id'] == 17  ) {
    $entry_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $values_used = FrmDb::get_col( 'frm_item_metas',
		array( 'item_id !' => $entry_id,
			array( 'or' => 1,
				array( 'field_id' => $simple_email_field_id, 'meta_value' => $_POST['item_meta'][ $simple_email_field_id ] ),
				array( 'field_id' => $simple_retreat_field_id, 'meta_value' => $_POST['item_meta'][ $simple_retreat_field_id ] ),
			)
		), 'item_id', array( 'group_by' => 'item_id', 'having' => 'COUNT(*) > 1' )
	);
	if ( ! empty( $values_used ) ) {
		$errors['my_error'] = 'You have already submitted an application for this retreat';
	}
  }
  return $errors;
}

add_filter('frm_validate_entry', 'two_fields_unique', 10, 2);

// show an error upon loading the form if the application is made after the deadline date or if the event is closed or it does not exist
add_action('frm_display_form_action', 'registration_closed', 8, 3);
function registration_closed($params, $fields, $form) {
	$retreat_status;
	$deadline;
	$deadline_field_id =  127;
	$event_status_field_id = 138;

	remove_filter('frm_continue_to_new', '__return_false', 50);
	$retreat = FrmEntry::getOne($_GET['retreat']); // retrieve the retreat this registration form points to


	if( ( $form->id == 13 || $form->id == 17  ) && !empty( $retreat ) ) { // Id for registration form

		$retreat_status = FrmProEntriesController::get_field_value_shortcode(array('field_id' => $event_status_field_id, 'entry' => $retreat->id));
		$deadline = FrmProEntriesController::get_field_value_shortcode(array('field_id' => $deadline_field_id, 'entry' => $retreat->id));

		if( ( time() > strtotime($deadline) ) || ( $retreat_status == 'Closed' ) ) {
			echo 'Sorry, the registration is closed for this event';
			add_filter('frm_continue_to_new', '__return_false', 50);
		}

	} elseif ( ( $form->id == 13 || $form->id == 17  ) && empty( $retreat ) ) {
		echo 'Sorry, this event does not exist';
		add_filter('frm_continue_to_new', '__return_false', 50);
	}

}

// automatically set registrationStatus to waiting list if max attendance is reached
add_filter('frm_validate_field_entry', 'waiting_list_condition', 10, 3);
function waiting_list_condition($errors, $posted_field, $posted_value){
	$registration_status_field_id = 201 ; // in registration form
	$retreat_id_field = 186; // in registration form
	$simple_registration_status_field_id = 248 ; // in registration form
	$simple_retreat_id_field = 249; // in registration form
	$form_id = 13; // registration form
	$simple_form_id = 17; // registration form
	$available_slots_field_id = 140; // in event form
	if ($form_id == $_POST['form_id']) {
		$retreat = $_POST['item_meta'][$retreat_id_field];
		if (  $registration_status_field_id == $posted_field->id ) {
			$available = FrmProEntriesController::get_field_value_shortcode(array('field_id' => $available_slots_field_id, 'entry' => $retreat));
			if ( $available <= 0) {
				$_POST['item_meta'][$posted_field->id] = 'Waiting list';
			}
		}
	} elseif ($simple_form_id == $_POST['form_id']) {
		$retreat = $_POST['item_meta'][$simple_retreat_id_field];
		if (  $simple_registration_status_field_id == $posted_field->id ) {
			$available = FrmProEntriesController::get_field_value_shortcode(array('field_id' => $available_slots_field_id, 'entry' => $retreat));
			if ( $available <= 0) {
				$_POST['item_meta'][$posted_field->id] = 'Waiting list';
			}
		}
	}
	return $errors;
}



add_action('frm_after_create_entry', 'decrease_available_slots', 30, 2);
function decrease_available_slots($entry_id, $form_id){

	$retreat_id_field = 186; // in registration form
	$simple_retreat_id_field = 249; // in registration form

	if ($form_id == 13){ //change 5 to the ID of your reservations form
	    global $wpdb, $frmdb;
	    $available_slots_field_id = 140; //in event form

	    $retreat_id = $_POST['item_meta'][$retreat_id_field];
	    $available = FrmProEntriesController::get_field_value_shortcode(array('field_id' => $available_slots_field_id, 'entry' => $retreat_id));

	    $wpdb->update( $frmdb->entry_metas, array( 'meta_value' => ( (int) $available-1 ) ), array( 'item_id' => $retreat_id, 'field_id' => $available_slots_field_id ) );

	}
	elseif  ($form_id == 17){ //change 5 to the ID of your reservations form
	    global $wpdb, $frmdb;
	    $available_slots_field_id = 140; //in event form

	    $retreat_id = $_POST['item_meta'][$simple_retreat_id_field];
	    $available = FrmProEntriesController::get_field_value_shortcode(array('field_id' => $available_slots_field_id, 'entry' => $retreat_id));

	    $wpdb->update( $frmdb->entry_metas, array( 'meta_value' => ( (int) $available-1 ) ), array( 'item_id' => $retreat_id, 'field_id' => $available_slots_field_id ) );

	}
}

add_filter('frm_validate_field_entry', 'copy_my_field', 10, 3);
function copy_my_field($errors, $posted_field, $posted_value){ // the counter for availability slots starts at max number of attendees
  if ( $posted_field->id == 140 ) { //change 25 to the ID of the field to change
    $_POST['item_meta'][$posted_field->id] = $_POST['item_meta'][128]; //Change 20 to the ID of the field to copy
  }
  return $errors;
}


add_action('frm_after_create_entry', 'cancel_registration', 30, 2);
add_action('frm_after_update_entry', 'cancel_registration', 10, 2);
function cancel_registration($entry_id, $form_id){
    if($form_id == 16){//Change 113 to the ID of the first form
        global $wpdb, $frmdb;
        $available_slots_field_id = 140; //in event form
        $registration_entry = $_POST['item_meta'][252]; //change 25 to the ID of the field in your first form
        $form_type = $_POST['item_meta'][253]; //change 25 to the ID of the field in your first form
        $retreat_id = $_POST['item_meta'][267]; //change 25 to the ID of the field in your first form
        if ("f" === $form_type) {
			$wpdb->update($frmdb->entry_metas, array('meta_value' => "Canceled"), array('item_id' => $registration_entry, 'field_id' => '201'));//Change 6422 to the ID of the field to be updated automatically in your second form
        }
        else if ("s" === $form_type) {
			$wpdb->update($frmdb->entry_metas, array('meta_value' => "Canceled"), array('item_id' => $registration_entry, 'field_id' => '248'));//Change 6422 to the ID of the field to be updated automatically in your second form
        }
        if (! empty( $retreat_id )) {
			$available = FrmProEntriesController::get_field_value_shortcode(array('field_id' => $available_slots_field_id, 'entry' => $retreat_id));
			$wpdb->update( $frmdb->entry_metas, array( 'meta_value' => ( (int) $available+1 ) ), array( 'item_id' => $retreat_id, 'field_id' => $available_slots_field_id ) );
        }


	}
}

?>
