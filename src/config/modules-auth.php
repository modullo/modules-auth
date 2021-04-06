<?php

return [
  'title' => 'Auth',
   'view' => [
     'company_name' => env('MODULLO_COMPANY_NAME','Modullo'),
      'allow_registration' => env('MODULLO_ALLOW_REGISTRATION',true),
      'company_logo' => 'https://logos.flamingtext.com/Word-Logos/test-design-sketch-name.png'
//      'company_logo' => env('MODULLO_COMPANY_LOGO')
   ]
];