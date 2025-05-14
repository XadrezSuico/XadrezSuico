<?php

return [
    'company_user' => env("XADREZSUICO_COMPANY_NAME", false),
    'company_user_html' => env("XADREZSUICO_COMPANY_NAME",false) ? " <span style='font-size: 50%'>de ".env("XADREZSUICO_COMPANY_NAME", ""). "</span>" : "",
];
