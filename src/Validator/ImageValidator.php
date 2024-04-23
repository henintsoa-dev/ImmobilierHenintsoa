<?php

namespace App\Validator;

use App\Entity\Image;
use App\Entity\Property;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ImageValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /** @var Property $value **/
        if (null === $value) {
            return;
        }

        if (!($value instanceof Property)) {
            return;
        }

        $images = $value->getImages();

        $missingFile = false;
        
        foreach($images as $image) {
            if ($image->getName() == null && $image->getFile() == null) {
                $missingFile = true;
            }
        }

        if ($missingFile == true) {
            $this->context->buildViolation($constraint->message)
                    ->addViolation();
        }

    }
}
