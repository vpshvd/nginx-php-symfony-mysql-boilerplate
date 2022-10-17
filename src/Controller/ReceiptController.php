<?php declare(strict_types=1);

    namespace App\Controller;

    use App\Entity\Receipt;
    use App\Service\ErrorResponse;
    use App\Service\Upload\UploadInterface;
    use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
    use Doctrine\Persistence\ManagerRegistry;
    use Ramsey\Uuid\Uuid;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Constraints\Collection;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Type;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class ReceiptController extends AbstractController
    {
        #[Route('/', name: 'create', methods: "POST")]
        public function create(
            Request            $request,
            UploadInterface    $uploadManager,
            ManagerRegistry    $doctrine,
            ErrorResponse      $errorResponse
        ): JsonResponse
        {
            $requestJson = json_decode($request->getContent(), true);
            $errors = $errorResponse->validate($requestJson);
            $visual = @(string)$requestJson['visual'];
            if ($errors->count() || json_last_error()) {
                $response = new JsonResponse();
                $response->setContent((string)$errors);
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                return $response;
            }

            $repository = $doctrine->getRepository(Receipt::class);

            $added = false;
            $receipt = null;
            $uuidLength = 6;

            while ($added === false) {
                try {
                    $uuid = substr((string)Uuid::uuid4(), 0, $uuidLength);
                    $receipt = new Receipt();
                    $receipt->setUuid($uuid);
                    $repository->add($receipt, true);
                    $shortUrl = $this->getParameter('host.prefix') . $uuid;
                    $fullUrl = $uploadManager->upload($visual, $uuid);
                    $receipt->setVisual($visual)
                        ->setShortUrl($shortUrl)
                        ->setFullUrl($fullUrl);
                    $repository->add($receipt, true);
                    $added = true;
                } catch (UniqueConstraintViolationException $e) {
                    $doctrine->resetManager();
                }
            }
            $shortUrl = $receipt->getShortUrl();
            $fullUrl = $receipt->getFullUrl();
            $data = [
                'status' => 'successful',
                'short URL' => $shortUrl
            ];
            $response = new JsonResponse($data, Response::HTTP_CREATED);
            $response->headers->set('Location', $fullUrl);
            return $response;
        }

        #[Route('/{uuid}', name: 'get', methods: "GET")]
        public function getReceipt(string $uuid): RedirectResponse
        {
            $fullUrl = $this->getParameter('s3.url') . $uuid;
            $response = new RedirectResponse($fullUrl, Response::HTTP_TEMPORARY_REDIRECT);
            $response->headers->set('Location', $fullUrl);
            return $response;
        }
    }
