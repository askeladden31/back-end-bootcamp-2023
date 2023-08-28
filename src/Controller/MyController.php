<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Collection;
use App\Entity\Contributor;
use App\Repository\CollectionRepository;
use App\Repository\ContributorRepository;
use Doctrine\ORM\EntityManagerInterface;

class MyController extends AbstractController
{
    private CollectionRepository $collectionRepository;
    private EntityManagerInterface $em;

    public function __construct(CollectionRepository $collectionRepository, EntityManagerInterface $em)
    {
        $this->collectionRepository = $collectionRepository;
        $this->em = $em;
    }

    #[Route('/collections/{id}/contribute', name: 'contribute', methods: ["POST"])]
    public function contribute(Request $request, ValidatorInterface $validator, int $id): Response
    {
        $collection = $this->collectionRepository->find($id);
        $contributor = new Contributor();

        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'user_name' => [new Assert\NotBlank(), new Assert\Type('string')],
            'amount' => [new Assert\NotBlank(), new Assert\Type('numeric'), new Assert\Positive()],
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {

            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            return new JsonResponse(['errors' => $errors], 400);
        }

        $contributor->setUserName($data['user_name']);
        $contributor->setAmount($data['amount']);

        $collection->addContributor($contributor);

        $this->em->persist($contributor);
        $this->em->flush();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_CREATED);

        return $response;
    }

    #[Route('/collections/{id}', name: 'collection_details', methods: ["GET"])]
    public function collectionDetails(Request $request, int $id): Response
    {
        $collection = $this->collectionRepository->find($id);
        $contributors = $collection->getContributors();

        $data = [
            'collection' => $collection,
            'contributors' => $contributors->toArray(),
        ];

        // $response = new JsonResponse($data, JsonResponse::HTTP_OK, [
        //     'Content-Type' => 'application/json',
        // ]);

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    #[Route('/collections/', name: 'list_collections', methods: ["GET"])]
    public function listCollections(Request $request): Response
    {
        $nr = $request->query->get('target_not_reached');
        // /collections?target_not_reached=true
        // "Отримати список зборів, які мають суму внесків менше за цільову суму."

        $lte = $request->query->get('remaining_lte');
        // /collections?remaining_lte={amount}
        // "Реалізувати можливість фільтрування зборів за залишеною сумою до досягнення кінцевої суми." 
        // This requirement is quite ambiguously worded. After a long consideration, the interpretation 
        // I decided to use is "list only those collections, where the remaining sum is less or equal to 
        // a given {amount}", which is implemented here.

        $collections = null;

        if ($nr == 'true')
        {
            $collections = $this->collectionRepository->findWhereTargetNotReached();
        }
        else if ($lte != null)
        {
            $collections = $this->collectionRepository->findWhereRemainingAmountLessThanOrEqual((float)$lte);
        }
        else
        {
            $collections = $this->collectionRepository->findAll();
        }

        $response = new Response(json_encode($collections));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    #[Route('/collections/create', name: 'create_collection', methods: ["POST"])]
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'title' => [new Assert\NotBlank(), new Assert\Type('string')],
            'description' => [new Assert\NotBlank(), new Assert\Type('string')],
            'link' => [new Assert\NotBlank(), new Assert\Url()],
            'target_amount' => [new Assert\NotBlank(), new Assert\Type('numeric'), new Assert\Positive()],
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {

            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            return new JsonResponse(['errors' => $errors], 400);
        }

        $collection = new Collection();

        $collection->setTitle($data['title']);
        $collection->setDescription($data['description']);
        $collection->setTargetAmount($data['target_amount']);
        $collection->setLink($data['link']);

        $this->em->persist($collection);
        $this->em->flush();

        $newId = $collection->getId();

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->headers->set('Location', $this->generateUrl('collection_details', ['id' => $newId]));

        return $response;
    }

}

?>