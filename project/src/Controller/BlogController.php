<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController {

  private const POSTS = [
      [
          'id' => 1,
          'slug' => 'hello-world',
          'title' => 'Hello World!'
      ],
      [
          'id' => 2,
          'slug' => 'another-post',
          'title' => 'This is another post!'
      ],
      [
          'id' => 3,
          'slug' => 'last-example',
          'title' => 'This is the last example'
      ],
  ];

  /**
   * @var LoggerInterface
   */
  private $logger;

  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * @Route("/", name="blog_list")
   */
  public function list() {
    return new JsonResponse(self::POSTS);
  }

  /**
   * @Route("/{id}", name="blog_by_id", requirements={"id"="\d+"})
   * @param int $id
   *
   * @return JsonResponse
   */
  public function post(int $id): Response {

    $this->logger->debug("Id: " . $id);

    $key = array_search($id, array_column(self::POSTS, 'id'), true);

    if ($key === false) {
      return new Response('',Response::HTTP_NOT_FOUND);
    }

    $this->logger->debug(print_r(self::POSTS[$key], true));

    return new JsonResponse(self::POSTS[$key]);
  }

  /**
   * @Route("/{slug}", name="blog_by_slug")
   * @param string $slug
   *
   * @return JsonResponse
   */
  public function postBySlug(string $slug): Response {

    $key = array_search($slug, array_column(self::POSTS, 'slug'), true);

    if ($key === false) {
      return new Response('',Response::HTTP_NOT_FOUND);
    }

    return new JsonResponse(self::POSTS[$key]);
  }

}