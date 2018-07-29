<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 22:14
 */

namespace App\References;


class InstanceReference
{

    const RULES = [
        'project_id' => 'required|exists:projects,id',
        'name' => 'required|string|min:1|max:96',
        'url' => 'required|url|max:200',
    ];

}