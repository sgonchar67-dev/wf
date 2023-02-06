<?php

namespace App\Service\Order\OrderStateMachine;

interface SupplierOrderStateInterface
{
    public function edit();
    public function place();
    public function seen();
    public function refuse();
    public function archive();
    public function complete();
    public function payment($comment = '', $data = [], $documents = []);
    public function shipment($comment = '', $data = [], $documents = []);
    public function refund($comment = '', $data = [], $documents = []);
    public function notification($comment = '', $data = [], $documents = []);

}