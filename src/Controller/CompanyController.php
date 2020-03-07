<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @Route("/siren/{siren}", name="get_siren_information")
     * @param int $siren
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function getSirenInformationAction(int $siren)
    {
        $company = $this->companyRepository->findLastUpdatedCompanyFromSiren($siren);

        if (null === $company) {
            throw new NotFoundHttpException(sprintf('The SIREN number "%d" does not exist.', $siren));
        }

        return new JsonResponse(
            [
                'siren' => $company->getSiren(),
                'company_name' => $company->getName(),
                'last_update' => $company->getDateMAJ(),
            ],
            200
        );
    }
}
