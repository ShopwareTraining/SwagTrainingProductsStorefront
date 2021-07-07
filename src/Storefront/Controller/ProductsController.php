<?php declare(strict_types=1);

namespace SwagTraining\ProductsStorefront\Storefront\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ProductsController extends StorefrontController
{
    private EntityRepositoryInterface $productRepository;

    /*
     * @param EntityRepositoryInterface $productRepository
     */
    public function __construct(EntityRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Since("6.4.0.0")
     * @Route("/swag-training/products-storefront", name="frontend.swag-training-products.products", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function getData(Request $request, Context $context): Response
    {
        $criteria = $this->createCriteria();

        $products = $this->productRepository->search($criteria, $context);

        return $this->renderStorefront(
            '@SwagTrainingProductsStorefront/storefront/page/content/products.html.twig',
            ['products' => $products]
        );
    }

    private function createCriteria():Criteria{
        $criteria = new Criteria();

        // the oldest
        $sorting = new FieldSorting('createdAt', FieldSorting::ASCENDING);
        $criteria->addSorting($sorting);

        // 5 products
        $criteria->setLimit(5);

        // starting with M
        $nameFilter = new PrefixFilter('name', 'M');
        $criteria->addFilter($nameFilter);

        return $criteria;
    }

}
