<?php

namespace ATD\CruiseFactory\Services\Google;

use Exception;

class ReCaptcha {
	private ?string $secretKey;

	public function __construct( ?string $secretKey ) {
		$this->secretKey = $secretKey;
	}

	public function validate( ?string $userInput ): bool {
		try {
			$options = [
				'http' => [
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query( [
						'secret'   => $this->secretKey,
						'response' => $userInput,
						'remoteip' => $_SERVER['REMOTE_ADDR']
					] )
				]
			];

			$context = stream_context_create( $options );
			if ( $result = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify', false, $context ) ) {
				return json_decode( $result )->success;
			}
		} catch ( Exception $e ) {
		}

		return false;
	}
}