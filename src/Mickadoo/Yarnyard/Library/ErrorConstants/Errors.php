<?php

namespace Mickadoo\Yarnyard\Library\ErrorConstants;

use Mickadoo\Yarnyard\Bundle\AuthBundle\Constants\AuthErrors;
use Mickadoo\Yarnyard\Bundle\UserBundle\Constants\RoleErrors;
use Mickadoo\Yarnyard\Bundle\UserBundle\Constants\UserErrors;

interface Errors extends
    AuthErrors,
    RoleErrors,
    UserErrors
{
    const ERROR_PERMISSION_NOT_ADMIN = 'You gotta be an admin for that';
    const ERROR_GLOBAL_REQUEST_NOT_SET = 'The request was not set';
    const ERROR_OBJECT_EXPECTED = 'Object expected but got something else';
}