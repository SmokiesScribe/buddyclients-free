<?php
use BuddyClients\Includes\Form;
use BuddyClients\Includes\FormField;
/**
 * Generates a new Form.
 * 
 * @since 1.0.21
 * 
 * @param   array       $args {
 *     An array of arguments to create the form.
 * 
 *     @type    string      $key                    The form key.
 *     @type    callable    $fields_callback        The callback to generate the form fields.
 *     @type    string      $submission_class       The class that handles the form submission.
 *     @type    bool        $submit_button          Optional. Whether to include a submit button.
 *     @type    string      $submit_text            Optional. The text of the submit button.
 *                                                  Defaults to 'Submit'.
 *     @type    string      $submit_classes         Optional. Classes to apply to the submit button.
 *     @type    array       $values                 Optional. A keyed array of values to populate the form fields.
 *     @type    int         $avatar                 Optional. Creates a user avatar above the form.
 *     @type    string      $form_classes           Optional. Classes to apply to the form.                    
 * }
 */
function buddyc_build_form( $args ) {
    $form = new Form( $args );
    return $form->build();
}

/**
 * Generates a new Form.
 * 
 * @since 1.0.21
 * 
 * @param   array       $args {
 *     An array of arguments to create the form.
 * 
 *     @type    string      $key                    The form key.
 *     @type    callable    $fields_callback        The callback to generate the form fields.
 *     @type    string      $submission_class       The class that handles the form submission.
 *     @type    bool        $submit_button          Optional. Whether to include a submit button.
 *     @type    string      $submit_text            Optional. The text of the submit button.
 *                                                  Defaults to 'Submit'.
 *     @type    string      $submit_classes         Optional. Classes to apply to the submit button.
 *     @type    array       $values                 Optional. A keyed array of values to populate the form fields.
 *     @type    int         $avatar                 Optional. Creates a user avatar above the form.
 *     @type    string      $form_classes           Optional. Classes to apply to the form.                    
 * }
 */
function buddyc_echo_form( $args ) {
    $form = new Form( $args );
    return $form->echo();
}

/**
 * Builds a new FormField from an array of args.
 * 
 * @since 1.0.21
 */
function buddyc_build_form_field( $args ) {
    $field = new FormField( $field_args );
    return $field->build();
}