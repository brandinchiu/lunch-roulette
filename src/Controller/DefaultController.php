<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 8:46 AM
 */

namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController
{
    public function index()
    {
        $version = shell_exec('git describe --abbrev=0 --tags');

        return new JsonResponse([
            'version' => ($version != null) ? trim($version) : null,
            'message' => 'Welcome to Spoonity\'s lunch-roulette Slack helper!'
        ]);
    }
}