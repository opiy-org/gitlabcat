<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 22:14
 */

namespace App\References;


class DomainReference
{

    const RULES = [
        'project_id' => 'required|exists:projects,id',
        'name' => 'required|string|min:3|max:32',
    ];

}