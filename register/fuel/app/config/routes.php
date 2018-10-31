<?php
return array(
	'_root_'  => 'root/index',  // The default route
	'detail'  => 'root/detail',
	'qr'  => 'root/qr',
	
	'qrimage/(:id)' => 'api/qrimage/$1',
	'api/get_patient_detail/(:patient_id)' => 'api/get_patient_detail/$1',
);
