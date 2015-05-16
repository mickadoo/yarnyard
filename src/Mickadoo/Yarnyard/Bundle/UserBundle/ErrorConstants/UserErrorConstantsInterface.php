<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\ErrorConstants;

use Mickadoo\Yarnyard\Library\ErrorConstants\ErrorConstantsInterface;

interface UserErrorConstantsInterface extends ErrorConstantsInterface
{

    const ERROR_USER_NOT_SET = 'ERROR_USER_NOT_SET';
    const ERROR_USER_EMAIL_NOT_SET = 'ERROR_USER_EMAIL_NOT_SET';

}