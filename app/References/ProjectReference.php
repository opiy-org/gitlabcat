<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 22:14
 */

namespace App\References;


class ProjectReference
{

    const RULES = [
        'name' => 'required|string|min:1|max:32',
        'gitlab_name' => 'required|string|unique:projects,gitlab_name',
        'channel' => 'required|string',
        'gitlab_id' => 'nullable|int',
    ];

}