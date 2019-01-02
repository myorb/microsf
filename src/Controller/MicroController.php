<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 1/2/19
 * Time: 2:53 AM
 */
// src/Controller/MicroController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MicroController extends AbstractController
{
    /**
     * @Route("/random/{limit}")
     */
    public function randomNumber($limit)
    {
        $number = random_int(0, $limit);

        return $this->render('micro/random.html.twig', array(
            'number' => $number,
        ));
    }
}