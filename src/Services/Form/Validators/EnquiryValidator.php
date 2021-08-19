<?php

namespace ATD\CruiseFactory\Services\Form\Validators;

class EnquiryValidator {
	private array $messages = [];
	private array $sanitizedFields = [];
	private array $validFields = [
		'email_address' => [
			'filter'         => FILTER_VALIDATE_EMAIL,
			'invalidMessage' => 'Invalid e-mail address provided.',
			'required'       => true
		],
		'first_name'    => [
			'filter'         => FILTER_SANITIZE_STRING,
			'invalidMessage' => 'First name is not valid.',
			'required'       => true
		],
		'last_name'     => [
			'filter'         => FILTER_SANITIZE_STRING,
			'invalidMessage' => 'Last name is not valid.',
			'required'       => true
		],
		'num_adults'    => [
			'filter'         => FILTER_SANITIZE_NUMBER_INT,
			'invalidMessage' => 'Number of adults is not a valid number.',
			'required'       => true
		],
		'num_children'  => [
			'filter'         => FILTER_SANITIZE_NUMBER_INT,
			'invalidMessage' => 'Number of children is not a valid number.',
			'required'       => false
		],
		'message'       => [
			'filter'         => FILTER_SANITIZE_STRING,
			'invalidMessage' => 'Your message is not valid.',
			'required'       => false
		]
	];

	public function validate(): bool {
		foreach ( $this->validFields as $field => $details ) {
			if ( ! $value = filter_input( INPUT_POST, $field, $details['filter'] ) ) {
				if ( ! $details['required'] ) {
					$value = null;
				} else {
					$this->messages[] = $details['invalidMessage'];
					continue;
				}
			}

			$this->sanitizedFields[ $field ] = $value;
		}

		return ! ( count( $this->messages ) > 0 );
	}

	public function getMessages(): string {
		return '<li>' . implode( '</li><li>', $this->messages ) . '</li>';
	}

	public function getSanitizedFields(): array {
		return $this->sanitizedFields;
	}
}