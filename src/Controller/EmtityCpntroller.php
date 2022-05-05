<?php

namespace Mink67\MultiPartDeserialize\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmtityCpntroller extends AbstractController
{
    public function __invoke($data)
    {
        return $data;
    }
}
