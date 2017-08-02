<?php
if (!class_exists('GNA_WHM_Cipher')) {
	class GNA_WHM_Cipher {
		private $securekey;
		private $iv_size;

		public function __construct($textkey) {
			$this->iv_size = mcrypt_get_iv_size(
				MCRYPT_RIJNDAEL_128,
				MCRYPT_MODE_CBC
			);
			$this->securekey = hash(
				'sha256',
				$textkey,
				TRUE
			);
		}

		public function encrypt($input) {
			$iv = mcrypt_create_iv($this->iv_size);
			return base64_encode(
				$iv . mcrypt_encrypt(
					MCRYPT_RIJNDAEL_128,
					$this->securekey,
					$input,
					MCRYPT_MODE_CBC,
					$iv
				)
			);
		}

		public function decrypt($input) {
			$input = base64_decode($input);
			$iv = substr(
				$input,
				0,
				$this->iv_size
			);
			$cipher = substr(
				$input,
				$this->iv_size
			);
			return trim(
				mcrypt_decrypt(
					MCRYPT_RIJNDAEL_128,
					$this->securekey,
					$cipher,
					MCRYPT_MODE_CBC,
					$iv
				)
			);
		}
	}
}
