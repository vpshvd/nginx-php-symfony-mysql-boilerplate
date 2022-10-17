<?php

    namespace App\Service;

    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Validator\Constraints\Collection;
    use Symfony\Component\Validator\Constraints\Json;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Type;
    use Symfony\Component\Validator\ConstraintViolationListInterface;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class ErrorResponse
    {
        private ValidatorInterface $validator;

        public function __construct(ValidatorInterface $validator)
        {
            $this->validator = $validator;
        }

        public function validate($requestJson): ConstraintViolationListInterface
        {
            $requestArrayValueRequirements = [
                new NotBlank(),
                new Type(['type' => 'string'])
            ];
            $constraints = new Collection(
                [
                    'link' => $requestArrayValueRequirements,
                    'order_num' => $requestArrayValueRequirements,
                    'order_site_num' => $requestArrayValueRequirements,
                    'visual' => $requestArrayValueRequirements,
                ]
            );
            $errorResponseArray = array();
            $errors = $this->validator->validate($requestJson, $constraints);
            $errorsCount = count($errors);
            for ($i = 0; $i < $errorsCount; $i++) {
                $errorMessage = $errors->get($i)->getMessage();
                $params = $errors->get($i)->getParameters();
                $error = array(
                    "id" => $errorMessage,
                    "description" => reset($params)
                );
                $errorResponseArray[] = $error;
            }
            var_dump(json_encode($errorResponseArray));
            return $errors;
        }
    }
