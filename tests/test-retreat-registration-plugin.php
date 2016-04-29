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

        $this->assertTrue(false);
    }
}
