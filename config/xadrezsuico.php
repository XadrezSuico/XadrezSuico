<?php

return [
    'name' => env("IS_XADREZSUICO", false) ? "XadrezSuíço" : "RokadeManager",
    'name_mini' => env("IS_XADREZSUICO", false) ? "XSu" : "RMn",
    'class' => env("IS_XADREZSUICO", false) ? "success" : "purple",
    'color' => env("IS_XADREZSUICO", false) ? "green" : "purple",
    'company_user' => env("XADREZSUICO_COMPANY_NAME", false),
    'company_user_html' => env("XADREZSUICO_COMPANY_NAME",false) ? " <span style='font-size: 50%'>de ".env("XADREZSUICO_COMPANY_NAME", ""). "</span>" : "",
];
