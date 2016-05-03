<?php
 
/**
 * An example test case.
 */
class Retreat_Registration_For_Formidable_Forms_Test extends FrmUnitTest {
 

    /**
     * An example test.
     *
     * We just want to make sure that false is still false.
     */
    function test_false_is_false() {
 
        $this->assertFalse( false );
    }

    function test_two_fields_unique_unrelated_form_should_return_existing_errors() {
    	$errors = array (
    		'an_error' => 'oops',
    		);
    	$values = array(
    		'form_id' => 15,
    		) ;
    	$this->assertEquals( $errors, two_fields_unique($errors, $values) );
    }

    function test_two_fields_unique_relevant_full_form_unique_fields_should_return_existing_errors() {
    	$errors = array (
    		'an_error' => 'oops',
    		);
    	$values = array(
    		'form_id' => 13,
    		) ;
        $first_field_id = 141; // registrant email
        $second_field_id = 186; // retreat id

        $_POST['item_meta'][141] = 'test@mail.com';
        $_POST['item_meta'][186] = 100;


    	$this->assertEquals( $errors, two_fields_unique($errors, $values) );

    }

    function test_two_fields_unique_relevant_full_form_fields_not_unique_returns_new_error() {
        $this->my_import_xml();

        $errors = array (
            'an_error' => 'oops',
            );
        $values = array(
            'form_id' => 13,
            ) ;
        $registrant_email_field = 141;
        $retreat_id_field = 186;


        $event_form = $this->factory->form->get_object_by_id( 12 );
        $event_entry_data = $this->factory->field->generate_entry_array( $event_form );
        $event_entry_id = FrmEntry::create( $event_entry_data );

        $_POST['item_meta'][$registrant_email_field] = 'test@mail.com';
        $_POST['item_meta'][$retreat_id_field] = $event_entry_id;

        $registration_form = $this->factory->form->get_object_by_id( 13 );
        //var_dump($registration_form);
        $registration_entry_data = $this->factory->field->generate_entry_array( $registration_form );
        $registration_entry_id = FrmEntry::create( $registration_entry_data );
        $registration_entry_id2 = FrmEntry::create( $registration_entry_data );

        $this->assertTrue($registration_entry_id);
        $this->assertNotTrue($registration_entry_id2);
    }


    // helper functions

    static function my_install_data() {
        return array( dirname( __FILE__ ) . '/testdata.xml' );
    }

    function my_import_xml() {
        // install test data in older format
        add_filter( 'frm_default_templates_files', 'Retreat_Registration_For_Formidable_Forms_Test::my_install_data' );
        FrmXMLController::add_default_templates();

        $form = FrmForm::getOne( 'contact-db12' );
        $this->assertEquals( $form->form_key, 'contact-db12' );
    }

}
