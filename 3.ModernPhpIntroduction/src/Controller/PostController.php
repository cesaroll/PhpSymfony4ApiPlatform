<?php

namespace App\Controller;

use App\Service\Serializer;
use App\Annotations\Route;

/**
 * @Route(route="/posts")
 *
 */
class PostController {

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
            'Action' => 'Post',
            'Time' => time()
        ]);
    }

    /**
     * @route(route="/one")
     *
     * @return void
     */
    public function one() {
        return $this->serializer->serialize([
            'Action' => 'PostOne',
            'Time' => time()
        ]);
    }

}
