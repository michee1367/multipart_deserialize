<?php
 
namespace Mink67\MultiPartDeserialize\Contracts;

use Mink67\MultiPartDeserialize\Annotations\File;
use Mink67\MultiPartDeserialize\Commons\Normalizer\Context;

interface NormaliserFile {

    /**
     * @return array
     */
    public function normalize(array $data, File $file, Context $context);

}