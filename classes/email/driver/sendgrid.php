<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Email;

use Log;


class Email_Driver_Sendgrid extends \Email_Driver
{
    private  $to_arr = [];

    protected function _send()
    {
        Log::debug(print_r($this->to_arr, true), '$this->to_arr');
        $from = new \SendGrid\Email("slimov", "info@slimov.com");
        $to = new \SendGrid\Email($this->to_arr[0], $this->to_arr[0]);
        $content = new \SendGrid\Content("text/plain",  $this->body);

        $mail = new \SendGrid\Mail($from, $this->subject, $to, $content);

        // 複数宛先があった場合
        foreach ($this->to as $k => $toadd) {
            if ($k == 0) {
                continue;
            }
            $email2 = new \SendGrid\Email($toadd, $toadd);
            $mail->personalization[0]->addTo($email2);
        }

        $apiKey = \Config::get('define.sendgrid.apikey');
        $sg = new \SendGrid($apiKey);

        $response = $sg->client->mail()->send()->post($mail);

        Log::debug(print_r($response, true), '$response');
        return true;
    }

    public function to($email, $name = false)
    {
        $this->to_arr = $email;
        static::add_to_list('to', $email, $name);

        return $this;
    }
}
