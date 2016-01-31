<?php

namespace YarnyardBundle\Exception\Constants;

interface Errors extends
    RoleErrors,
    UserErrors
{
    const ERROR_PERMISSION_NOT_ADMIN = 'You gotta be an admin for that';
    const ERROR_GLOBAL_REQUEST_NOT_SET = 'The request was not set';
    const ERROR_OBJECT_EXPECTED = 'Object expected but got something else';
}