<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 22:14
 */

namespace App\References;


class UserReference
{

    const RULES = [
        'name' => 'required|string|min:2|max:96',
        'gitlab_name' => 'required|string|max:96|unique:users,gitlab_name',
        'uid' => 'nullable|string|max:32',

        'gitlab_id' => 'nullable|integer',
        'api_key' => 'nullable|string',

        'rights' => 'integer|nullable',
    ];

    const GUEST_COMMANDS = [
        'reg',
        'help'
    ];

    const CODER_COMMANDS = [
        'issues',
        'instances',
        'domains',
        'help',
        'showmethecat'
    ];

    const ADMIN_COMMANDS = [
        'addinstance',
        'delinstance',

        'adddomain',
        'deldomain',

        'projects',
        'addproject',
        'delproject',

        'users',
        'adduser',
        'deluser',
    ];


}