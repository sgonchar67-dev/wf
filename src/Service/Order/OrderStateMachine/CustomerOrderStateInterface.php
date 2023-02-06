<?php

namespace App\Service\Order\OrderStateMachine;


interface CustomerOrderStateInterface
{
    public function edit();
    public function checkout();
    public function place();
    public function seen();
    public function refuse();
    public function archive();
    public function payment($comment = '', $data = [], $documents = []);
    public function refund($comment = '', $data = [], $documents = []);
    public function notification($comment = '', $data = [], $documents = []);
}