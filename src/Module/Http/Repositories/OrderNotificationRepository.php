<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class OrderNotificationRepository extends CoreRepository
{

    public function __construct()
    {
        $this->setModel('RefinedDigital\ProductManager\Module\Models\OrderStatus');
    }
}
