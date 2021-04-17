<?php

return [
  'title' => 'Auth',
   'view' => [
     'company_name' => env('MODULLO_COMPANY_NAME','Modullo'),
     'allow_registration' => env('MODULLO_ALLOW_REGISTRATION',true),
     'company_logo' => env('MODULLO_COMPANY_LOGO'),
     'custom_form_fields' => true
   ]
];