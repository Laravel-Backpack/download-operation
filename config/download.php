<?php

return [
    // the view to build the pdf from
    'view' => 'crud::show',
    // when using the default view `crud::show` you can define the content class to be used
    'contentClass' => 'col-md-12',

    
    // the format used to build the pdf. when `browsershot` config is used this is ignored and you should define the format yourself.
    'format' => 'A4',
    // the headers to send with the pdf download response
    'headers' => ['Content-Type' => 'application/pdf'],

    

    // an invokable class that build the Browsershot instance with custom settings like node path, headless, etc.
    // the class should have a `__invoke` method that accept an array of data and return the Browsershot result.
    // check docs at: https://github.com/Laravel-Backpack/download-operation?tab=readme-ov-file#configure-the-browsershot-instance
    'browsershot' => null,
];