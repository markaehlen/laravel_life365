<?php

use Symfony\Component\Yaml\Yaml;

/*
|--------------------------------------------------------------------------
| Site setings
|--------------------------------------------------------------------------
|
*/

if (file_exists(storage_path('app') . "/settings.yml")) {
    $settings = Yaml::parse(file_get_contents(storage_path('app') . "/settings.yml"));
    if (!empty($settings)) {
        return $settings;
    }
}

return [
    "app_name" => "Life-365™",
    "business_email" => "l8admin@l8admin.com",
    "business_phone" => "(91) 1234567890",
    "business_address" => "Jhalana institutional area",
    "from_email" => "noreply@l8admin.com",
    "email_from_name" => "Life-365™",
    "facebook_url" => null,
    "twitter_url" => null,
    "linkedin_url" => null,
    "youtube_url" => null,
];
