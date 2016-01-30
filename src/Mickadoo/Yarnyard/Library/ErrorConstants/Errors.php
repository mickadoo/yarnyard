<?php

namespace Mickadoo\Yarnyard\Library\ErrorConstants;

use Mickadoo\Yarnyard\Bundle\AuthBundle\Constants\AuthErrors;
use YarnyardBundle\Constants\RoleErrors;
use YarnyardBundle\Constants\UserErrors;

interface Errors extends
    RoleErrors,
    UserErrors
{
    const ERROR_PERMISSION_NOT_ADMIN = 'You gotta be an admin for that';
    const ERROR_GLOBAL_REQUEST_NOT_SET = 'The request was not set';
    const ERROR_OBJECT_EXPECTED = 'Object expected but got something else';
}