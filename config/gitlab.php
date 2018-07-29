<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.01.18
 * Time: 11:47
 */

return [
    'api_key' => env('GITLAB_APIKEY'),
    'api_url' => env('GITLAB_URL'),

    'sprint_backlog_tags' => ['Текущий спринт'],
    'sprint_inwork_tags' => ['В разработке'],
    'sprint_intest_tags' => ['В тестировании'],


];