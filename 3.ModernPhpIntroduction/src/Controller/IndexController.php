<?php

namespace App\Controller;

use App\Service\Serializer;

/**
 * @Route(route="/")
 */
class IndexController {

    private $serializer;

    public function __construct(Serializer $serializer) {
        $this->serializer = $serializer;
    }

    /**
     * @route(route="/")
     *
     * @return void
     */
    public function index() {
        return $this->serializer->serialize([
            'Action' => 'Index',
            'Time' => time()
        ]);
    }
}
