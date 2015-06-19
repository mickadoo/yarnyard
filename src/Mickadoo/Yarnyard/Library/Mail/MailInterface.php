<?php


namespace Mickadoo\Yarnyard\Library\Mail;


interface MailInterface {

    /**
     * @return string
     */
    public function getMailSubject();

    /**
     * @return string
     */
    public function getMailBody();

}