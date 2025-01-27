<?php

namespace Midtrans;

class Notification
{
    private $response;

    public function __construct($input_source = "php://input")
    {
        $raw_notification = json_decode(file_get_contents($input_source), true);

        if (!isset($raw_notification['transaction_id'])) {
            throw new \Exception("transaction_id is missing in the notification.");
        }

        $status_response = Transaction::status($raw_notification['transaction_id']);
        $this->response = json_decode(json_encode($status_response));
    }

    public function __get($name)
    {
        if (isset($this->response->$name)) {
            return $this->response->$name;
        }
        return null;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
?>
