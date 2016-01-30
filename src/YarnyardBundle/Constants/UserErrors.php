<?php

namespace YarnyardBundle\Constants;

interface UserErrors
{
    const ERROR_USER_NOT_FOUND = 'Can\'t find that user';
    const ERROR_USER_NOT_SET = 'The user wasn\'t set';
    const ERROR_USER_EMAIL_CONTAINS_NON_ASCII = 'The email should all be ascii characters';
    const ERROR_USER_EMAIL_NOT_SET = 'Did you forget to set the email?';
    const ERROR_USER_EMAIL_INVALID = 'What kind of email is that??';
    const ERROR_USER_EMAIL_ALREADY_EXISTS = 'Sorry, that email is already taken';
    const ERROR_USER_USERNAME_NOT_SET = 'Where\'s the username?';
    const ERROR_USER_USERNAME_TOO_SHORT = 'Your username is too short..';
    const ERROR_USER_USERNAME_TOO_LONG = 'Now your username is too long..';
    const ERROR_USER_USERNAME_CONTAINS_NON_ASCII = 'Some sort of non-ascii character in your username';
    const ERROR_USER_USERNAME_ALREADY_EXISTS = 'Popular username.. already taken';
    const ERROR_USER_PASSWORD_NOT_SET = 'What, so your password should be ""??? Set it!';
    const ERROR_USER_PASSWORD_TOO_SHORT = 'Rookie mistake.. password too long';
    const ERROR_USER_PASSWORD_TOO_LONG = 'Maybe I should change this, but for now your password is too long';
    const ERROR_USER_PASSWORD_CONTAINS_NON_ASCII = 'I only accept ascii passwords. Is that wrong?';
}