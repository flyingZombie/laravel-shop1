<?php 

return [

	'alipay' => [
		'app_id' => '2016091900550166',
		'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAt7yqO5fAa9aSYV/LH2qJdlt+GwY1+n1N8Mt3fuu4U5j4MOQp8+CUrw45WcxunoM0qCvtB1DwH+9by14yFPaVxe7qMf510jBCdfa3JUGn/Aw24LvdbDoFDsBqDFCCG7KCa2EGVoTtdO4B4HsmaKa4ZDW3n7LyOtol0aYnW+hRTuc6wECg3VesCgRmxfXld14AAr2EfGRiu+47ljhdHFctQd3IhWtgtD4H23LRaCOucyt6pVEaedQhQAtq7yFkQ3ZSNJbLlL2oXeHmWfc2RRDddbe7cgY0OEl+7R42UoBlqKsI4tSmLZGXnKtEUje+6MZK8rTFag7RxrqircCscCqRqwIDAQAB',
		'private_key' => 'MIIEpQIBAAKCAQEAn5+Gc35dU79g5L8C+eE5ADcS9VyvRJmAPQeusxw3e6maQGXIhsBCyeMVL2glzwD+emc8gGIVtZta1fi4r7/m8I0NmBCZNauqxnJriIBNbobnmErMbYGT+Vi2PIhpR0Exn/gtFWV0hAPDXR8X2ogMp2qHvl4/58NyKkHYzIiaulF9LtrQuIA1UwR6lNW6E5MtklaPDLtswMTaKNpoJiB10nUymfcVpPlDHDDlsORGINZb7HmvudMB1SkWxrFXUxMQjmSWVbNrfwL6krwL2HB+KCCXD0lSyT4meqCnThZomcVatTvpOvTXtO9unlW34FoprgcxwVfPGFAT9KPfwmEJ6QIDAQABAoIBADmrC4scQyFnidz6eVvHl61ROGM2ugeBoYfrB52j+xONQbI0U48DVN3VUakP6mDwDgAw3fXP+eHjn5ygQDEWlpufiiK/FGsEey0410q4d8EPsgoeM974KnqyJxyVd9uLXR9bLQaG4eJz7ZfuDNMhxdiSLzqCmHnl7ZAAvA+g5q2bAxq1zDPMXXXo3Y2ETuNYL0bYL7EfxdWYjd6HC/c0S7T92JKy4/2tXu73V+rzR8A+nQqvYVdTqfH9Dr90h/mCCzib/fowLMKqJuklGzeLveXKt9Jc/UqokngXA/JFG2OrqOHa+qC77ey903V3nuNetzAoF+5uS5fYawggGbHCzYECgYEAy2o3nW9xwZJvSPsEBwYHUkSz/NqHCGVr+ya/KFtcJxVX8z3fnDbclyXpVL4Wyd2q5Du6oZJ/4vKs51lcgRzxhHCIkQpkDtr+Ip6fjqXs6BD1Cp0VVnCbajolFwnaU8ipHy8Vk18n1chHhgUmxq4WBeGZWauS0SXZIYQoHfS3TtECgYEAyOM3JPPqSx38VkLFkxiKoM8d4NGlmJNfJeGVlWv8A0yNsCWBvWOsoRpbV8rGGpnhKcOIh3gtmXSRhUJqMm2Y6q8IRMJ9tBSeL77JEbTVazxu285/dV7rRdy2pBJFYitpv/e47rlsQMS1I/nHTbdeT2JC4CJq1DXpMJqCSh/1v5kCgYEAxkQDbTpgwHIAcjvoEwh3PmKkpIJDN5XTh/qeO7HAwn91OCtItrRqkhBruyOEhsG+fbMSF8X5TLWIff6FwXr6lUIyelyMZkZhilDd6GYl4ZQVo4C0eYpMkV+XIzuBSES/Qxvbhccrxp3tyN1gjCrGYHkhxVsMQTsIxjhP+dK6kOECgYEAsROgY0FisE5BRSRclloWsMOBOdBzIPge8fxUZ78lCR9s8e1N3MzEucnK5KtITuB/mtnSOl/UCiW3tHijtcnn9k7NghbSyW83NSz+fk4hpgJCUh1HOwWtbAqvMBcu8+cCs4XAjSYDZ2A9r/WYGkhdqKob+wg7lM3kvMQlvwNHE1kCgYEAu51N6/6AzaKeGexHKJSHmnVR/chXKTrml9IqyBrSD2RFztlVV28SwMEhPQtw3q2rSXxQOGTOmdF9UKM3/yOc0xjNOlRO1Dy2jgfN4LB2YD6JEOtK+ZVeNrBiSUkrRjQbOHVyCxtCK7knJGb0TflhkNWggzaOWdiiQYLWr0uplco=',
		'log' => [
			'file' => storage_path('logs/alipay.log'),
		],
	],

	'wechat' => [
		'app_id' => '',
		'mch_id' => '',
		'key' => '',
		'cert_client' => '',
		'cert_key' => '',
		'log' => [
			'file' => storage_path('logs/wechat_pay.log'),
		],
	],
];


 ?>