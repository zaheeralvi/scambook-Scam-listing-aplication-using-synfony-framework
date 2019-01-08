<?php

namespace App\Libraries;

use App\Controller\Apiv1Bundle\ConstantsConroller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Acl\Exception\Exception;
//use Timestampable\Fixture\Document\Book;
use App\Entity\Assets;
use App\Entity\AssetsIssuedToCustomer;
use App\Entity\Brands;
use App\Entity\Category;
use App\Entity\Colors;
use App\Entity\CouponBooks;
use App\Entity\CustomerCategories;
use App\Entity\Customers;
use App\Entity\CustomerTypes;
use App\Entity\DiscountPolicy;
use App\Entity\Discounts;
use App\Entity\DiscountType;
use App\Entity\DriverProductLoadOuts;
use App\Entity\User;
use App\Entity\Invoices;
use App\Entity\LoadOut;
use App\Entity\LoadOutAssets;
use App\Entity\LoadOutBooks;
use App\Entity\LoadOutItems;
use App\Entity\LoadOutSattlements;
use App\Entity\LoadOutSattlmentProducts;
use App\Entity\LoadOutSattlmentRequest;
use App\Entity\OrderBooks;
use App\Entity\OrderCouponsApplied;
use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\OrderStatus;
use App\Entity\Products;
use App\Entity\RequestProducts;
use App\Entity\Requests;
use App\Entity\RequestTypes;
use App\Entity\Routes;
use App\Entity\Transactions;
use App\Entity\UserRole;
use App\Entity\Vhiecal;
use App\Entity\CouponCodes;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ParserApi
{
    public function __construct(Container $container, EntityManager $em)
    {
        $this->em = $em;
        $this->container = $container;
    }

    //PARSE RESPONSES

    /**
     * @param User $user
     * @param Request $request
     * @return object
     */
    public function parseUserObjectByObjectForDriver(User $user, Request $request, $themeObj = false)
    {
        $moduleConfigs = [];
        $moduleConfigs['customer'] = $user->getMAppCustomer() ? 1 : 0;
        $moduleConfigs['createOrder'] = $user->getMAppOrder() ? 1 : 0;
        $moduleConfigs['journeyPlan'] = $user->getMAppJourneyPlan() ? 1 : 0;
        $moduleConfigs['assets'] = $user->getMAppAssets() ? 1 : 0;
        $moduleConfigs['submitRequest'] = $user->getMAppRequests() ? 1 : 0;
        $moduleConfigs['loadout'] = $user->getMAppLoadout() ? 1 : 0;
        $moduleConfigs['redeem'] = $user->getMAppRedeam() ? 1 : 0;
        $moduleConfigs['workFlow'] = $user->getMAppWorkflow() ? 1 : 0;
        $moduleConfigs['dayEnd'] = $user->getMAppDayEnd() ? 1 : 0;
        $moduleConfigs['syncStatus'] = $user->getMAppSync() ? 1 : 0;
        $moduleConfigs['modulesIndexs'] = [
            0, 1, 2, 3, 4, 5, 6, 7, 8, 9
        ];

        $moduleConfigs['colorPrimary'] = $themeObj ? $themeObj->getColorPrimary() : '';
        $moduleConfigs['colorPrimaryDark'] = $themeObj ? $themeObj->getColorPrimaryDark() : '';
        $moduleConfigs['colorAccent'] = $themeObj ? $themeObj->getColorAccent() : '';
        $moduleConfigs['colorTone1'] = $themeObj ? $themeObj->getColorTone1() : '';
        $moduleConfigs['colorTone2'] = $themeObj ? $themeObj->getColorTone2() : '';

        $userResponse = (object)[
            'id' => $user->getId(),
            'name' => $user->getFirstName(),
            'salesman_name' => $user->getSalesmanName(),
            'mobile_number' => $user->getContactNo(),
            'email' => $user->getEmail(),
            'location' => (object)[
                'locale' => $user->getLanguage(),
                'address' => $user->getAddress(),
                'city' => $user->getCity(),
                'state' => $user->getState(),
                'country' => $user->getState(),
                'postalCode' => $user->getPostalCode(),
            ],
            'latitude' => $user->getLatitude(),
            'longitude' => $user->getLongitude(),
            'locale' => $user->getLanguage(),
            'permissions' => [],
            'truck' => $user->getVhiecle() ? $this->parseVehicleObjectByObjectForDriver($user->getVhiecle(), $request) : (object)[],
            'log_time' => (int)$user->getLogTime(),
            'is_invoice_allowed' => $user->getIsInvoiceAllowed() ? 1 : 0,
            'is_delivery_note_allowed' => $user->getIsDeliveryNoteAllowed() ? 1 : 0,
            'moduleConfigs' => $moduleConfigs
        ];

        return $userResponse;
    }

    public function parseCompanyObj()
    {
        $company = $this->em->getRepository('App:Company')->findAll();
        $companyInfo = array();
        if (isset($company[0])) {
            $companyInfo['id'] = ($company[0]->getId()) ? $company[0]->getId() : '';
            $companyInfo['fullName'] = ($company[0]->getCoFullName()) ? $company[0]->getCoFullName() : '';
            $companyInfo['shortName'] = ($company[0]->getCoShortName()) ? $company[0]->getCoShortName() : '';
            $companyInfo['address'] = ($company[0]->getCoAddress()) ? $company[0]->getCoAddress() : '';
            $companyInfo['landLine1'] = ($company[0]->getCoLandline1()) ? $company[0]->getCoLandline1() : '';
            $companyInfo['landLine2'] = ($company[0]->getCoLandline2()) ? $company[0]->getCoLandline2() : '';
            $companyInfo['fax'] = ($company[0]->getCoFax()) ? $company[0]->getCoFax() : '';
            $companyInfo['email'] = ($company[0]->getCoEmail()) ? $company[0]->getCoEmail() : '';
            $companyInfo['website'] = ($company[0]->getCoWebsite()) ? $company[0]->getCoWebsite() : '';
            $companyInfo['state'] = ($company[0]->getCoState()) ? $company[0]->getCoState() : '';
            $companyInfo['city'] = ($company[0]->getCoCity()) ? $company[0]->getCoCity() : '';
            $companyInfo['country'] = ($company[0]->getCoCountry()) ? $company[0]->getCoCountry() : '';
            $companyInfo['trnId'] = ($company[0]->getCoTrnId()) ? $company[0]->getCoTrnId() : '';
            $companyInfo['createdOn'] = $company[0]->getCreatedOn() ? $this->parseDate($company[0]->getCreatedOn()) : (object)[];
            $companyInfo['coId'] = ($company[0]->getCoId()) ? $company[0]->getCoId() : '';
            $companyInfo['footer'] = ($company[0]->getCoFooter()) ? $company[0]->getCoFooter() : '';
            $companyInfo['logoUrl'] = ($company[0]->getCoLogoUrl()) ? $company[0]->getCoLogoUrl() : '';
            $companyInfo['companyLogo'] = ($company[0]->getCoLogoUrl()) ? $company[0]->getCoLogoUrl() : '';
            $companyInfo['colorPrimary'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getColorPrimary()) ? $company[0]->getAppTheme()->getColorPrimary() : '';
            $companyInfo['colorPrimaryDark'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getColorPrimaryDark()) ? $company[0]->getAppTheme()->getColorPrimaryDark() : '';
            $companyInfo['colorAccent'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getColorAccent()) ? $company[0]->getAppTheme()->getColorAccent() : '';
            $companyInfo['buttonsBackgroundColour'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getButtonsBackgroundColour()) ? $company[0]->getAppTheme()->getButtonsBackgroundColour() : '';
            $companyInfo['menuBackgroundColour'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getMenuBackgroundColour()) ? $company[0]->getAppTheme()->getMenuBackgroundColour() : '';
            $companyInfo['menuTextColour'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getMenuTextColour()) ? $company[0]->getAppTheme()->getMenuTextColour() : '';
            $companyInfo['iconColour'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getIconColour()) ? $company[0]->getAppTheme()->getIconColour() : '';
            $companyInfo['primaryTextColour'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getPrimaryTextColour()) ? $company[0]->getAppTheme()->getPrimaryTextColour() : '';
            $companyInfo['secondaryTextColour'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getSecondaryTextColour()) ? $company[0]->getAppTheme()->getSecondaryTextColour() : '';
            $companyInfo['is_invoice_allowed'] = $company[0]->getIsInvoiceAllowed() ? 1 : 0;
            $companyInfo['is_delivery_note_allowed'] = $company[0]->getIsDeliveryNoteAllowed() ? 1 : 0;
            $companyInfo['secondaryTextColour'] = ($company[0]->getAppTheme() and $company[0]->getAppTheme()->getSecondaryTextColour()) ? $company[0]->getAppTheme()->getSecondaryTextColour() : '';
        }
        return $companyInfo;
    }

    public function parseUserObjectByObjectForCustomer(User $user, Request $request, $themeObj = false)
    {
        $moduleConfigs = [];
//        $moduleConfigs['customer'] = $user->getMAppCustomer() ? 1 : 0;
//        $moduleConfigs['createOrder'] = $user->getMAppOrder() ? 1 : 0;
//        $moduleConfigs['journeyPlan'] = $user->getMAppJourneyPlan() ? 1 : 0;
//        $moduleConfigs['assets'] = $user->getMAppAssets() ? 1 : 0;
//        $moduleConfigs['submitRequest'] = $user->getMAppRequests() ? 1 : 0;
//        $moduleConfigs['loadout'] = $user->getMAppLoadout() ? 1 : 0;
//        $moduleConfigs['redeem'] = $user->getMAppRedeam() ? 1 : 0;
//        $moduleConfigs['workFlow'] = $user->getMAppWorkflow() ? 1 : 0;
//        $moduleConfigs['dayEnd'] = $user->getMAppDayEnd() ? 1 : 0;
//        $moduleConfigs['syncStatus'] = $user->getMAppSync() ? 1 : 0;
////        $moduleConfigs['modulesIndexs'] = [
////            0, 1, 2, 3, 4, 5, 6, 7, 8, 9
////        ];
//
        $moduleConfigs['colorPrimary'] = $themeObj ? $themeObj->getColorPrimary() : '';
        $moduleConfigs['colorPrimaryDark'] = $themeObj ? $themeObj->getColorPrimaryDark() : '';
        $moduleConfigs['colorAccent'] = $themeObj ? $themeObj->getColorAccent() : '';
        $moduleConfigs['colorTone1'] = $themeObj ? $themeObj->getColorTone1() : '';
        $moduleConfigs['colorTone2'] = $themeObj ? $themeObj->getColorTone2() : '';

        $userResponse = (object)[
            'id' => $user->getId(),
            'name' => $user->getFirstName(),
            'salesman_name' => $user->getSalesmanName(),
            'mobile_number' => $user->getContactNo(),
            'email' => $user->getEmail(),
            'location' => (object)[
                'locale' => $user->getLanguage(),
                'address' => $user->getAddress(),
                'city' => $user->getCity(),
                'state' => $user->getState(),
                'country' => $user->getState(),
                'postalCode' => $user->getPostalCode(),
            ],
            'latitude' => $user->getLatitude(),
            'longitude' => $user->getLongitude(),
            'locale' => $user->getLanguage(),
            'permissions' => [],
            'truck' => $user->getVhiecle() ? $this->parseVehicleObjectByObjectForDriver($user->getVhiecle(), $request) : (object)[],
            'log_time' => (int)$user->getLogTime(),
            'moduleConfigs' => $moduleConfigs
        ];

        return $userResponse;
    }

    public function parseUserObjectByObjectForUser(User $user, Request $request)
    {

        $userResponse = (object)[
            'id' => $user->getId(),
            'name' => $user->getFirstName(),
            'location' => (object)[
                'locale' => $user->getLanguage(),
                'address' => $user->getAddress(),
                'city' => $user->getCity(),
                'state' => $user->getState(),
                'country' => $user->getState(),
                'postalCode' => $user->getPostalCode(),
            ],
            'latitude' => $user->getLatitude(),
            'longitude' => $user->getLongitude(),
            'truck' => $user->getVhiecle() ? $this->parseVehicleObjectByObjectForDriver($user->getVhiecle(), $request) : (object)[],
            'salesman' => $user->getSalesmanName() ? $user->getSalesmanName() : '',
            'route' => ($user->getUserRoute()) ? $user->getUserRoute()->getName() : '',
        ];

        return $userResponse;
    }

    public function parseUsersArrayByObjectForDriver($users, Request $request)
    {

        $userResponse = [];
        foreach ($users as $user) {
            $userResponse[] = $this->parseUserObjectByObjectForDriver($user, $request);
        }

        return $userResponse;
    }

    public function parseUsersArrayByObjectForUser($users, Request $request)
    {

        $userResponse = [];
        foreach ($users as $user) {
            $userResponse[] = $this->parseUserObjectByObjectForUser($user, $request);
        }

        return $userResponse;
    }

    public function parseVehicleObjectByObjectForDriver(Vhiecal $vehicle, $request)
    {
        $userResponse = (object)[
            'id' => $vehicle->getId(),
            'number' => $vehicle->getNumber(),
            'name' => $vehicle->getName(),
            'modal' => $vehicle->getModal(),
            'capacity' => $vehicle->getCapacity()
        ];
        return $userResponse;
    }

    public function parseDate(\DateTime $dateTime)
    {
        $userResponse = (object)[
            'date' => $dateTime->format('d-m-Y h:i:s'),
            'timezone' => $dateTime->getTimezone(),
            'timestamp' => $dateTime->getTimestamp()
        ];
        return $userResponse;
    }

    public function parseVehicleArrayByObjectForDriver($vehicles, Request $request)
    {

        $userResponse = [];
        foreach ($vehicles as $vehicle) {
            $userResponse[] = $this->parseVehicleObjectByObjectForDriver($vehicle, $request);
        }

        return $userResponse;
    }

    public function parseRequestTypeObjectByObjectForDriver(RequestTypes $reqTypes, $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $reqTypes->getId(),
            ConstantsConroller::NAME => $reqTypes->getName()

        ];
        return $userResponse;
    }

    /**
     * @param Products $product
     * @param Request $request
     * @return object
     */
    public function parseProductsObjectForDriver(Products $product, Request $request, $loadOutItem = false)
    {

        $inStock = $product->getInStock();

        if ($loadOutItem) {
            $inStock = ($loadOutItem->getQuantity() - $loadOutItem->getDeliveredQuantity());
        }

        $userResponse = (object)[
            'id' => $product->getId(),
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'inStock' => $inStock,
            'available' => ($product->getInStock() - $product->getOnHold()), // need to fix it according loadOut
            'basePrice' => round($product->getBasePrice(),2),
            'tempValue' => $product->getTempValue(),
            'extraValue' => $product->getExtraValue(),
            'brand' => $this->parseProductBrandsObjectByObjectForDriver($product->getBrand(), $request),
            'category' => $this->parseProductCategoryObjectByObjectForDriver($product->getCategory(), $request),
            'image' => $this->getProductImageObj($product),
            'barCode' => $product->getBarCode() ? $product->getBarCode() : '',
            'image_url' => $product->getImageUrl(),
            'taxPercent' => $product->getTax()
        ];

        return $userResponse;
    }

    public function parseAssetsObjectForDriver(Assets $asset, Request $request)
    {

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $asset->getId(),
            ConstantsConroller::NAME => $asset->getName(),
            'unitPrice' => $asset->getUnitPrice(),
            'inStock' => $asset->getInStock(),
            'onHold' => $asset->getOnHold(),
            'barCode' => $asset->getBarCode() ? $asset->getBarCode() : '',
            'asset_tax_percentage' => (double)$asset->getAssetTaxPercentage(),
            'asset_tax' => (double)$asset->getAssetTaxPrice(),
            'asset_after_tax' => (double)$asset->getAssetPriceAfterTax(),
        ];

        return $userResponse;
    }


    /**
     * @param $products
     * @param Request $request
     * @return array
     */
    public function parseProductsArrayOfObjectsForDriver($products, Request $request)
    {

        $userResponse = [];
        foreach ($products as $product) {
            if ($product != null) {
                $userResponse[] = $this->parseProductsObjectForDriver($product, $request);
            }
        }

        return $userResponse;
    }

    public function parseAssetsArrayOfObjectsForDriver($assets, Request $request)
    {

        $userResponse = [];
        foreach ($assets as $asset) {
            if ($asset != null) {
                $userResponse[] = $this->parseAssetsObjectForDriver($asset, $request);
            }
        }

        return $userResponse;
    }

    /**
     * @param Brands $brands
     * @param Request $request
     * @return object
     */

    public function parseProductBrandsObjectByObjectForDriver(Brands $brands, Request $request)
    {

        $userResponse = (object)[
            'id' => $brands->getId(),
            'name' => $brands->getName(),
        ];

        return $userResponse;

    }


    /**
     * @param $brands
     * @param Request $request
     * @return array
     */

    public function parseProductBrandsArrayOfObjectsForDriver($brands, Request $request)
    {

        $userResponse = [];
        foreach ($brands as $brand) {
            $userResponse[] = $this->parseProductBrandsObjectByObjectForDriver($brand, $request);
        }

        return $userResponse;
    }

    /**
     * @param Category $category
     * @param Request $request
     * @return object
     */

    public function parseProductCategoryObjectByObjectForDriver(Category $category, Request $request)
    {

        $userResponse = (object)[
            'id' => $category->getId(),
            'name' => $category->getName(),
        ];

        return $userResponse;

    }

    /**
     * @param $categories
     * @param Request $request
     * @return array
     */

    public function parseProductCategoryArrayOfObjectsForDriver($categories, Request $request)
    {

        $userResponse = [];
        foreach ($categories as $category) {
            $userResponse[] = $this->parseProductCategoryObjectByObjectForDriver($category, $request);
        }

        return $userResponse;
    }


    /**
     * @param Customers $customer
     * @param Request $request
     * @return object
     */
    public function parseCustomerObjectByObjectForDriver(Customers $customer, Request $request, $redeemedTotal = false)
    {

        $customerTimming = [];
        $customerDays = [];

        if ($customer->getMorning() == 1) {
            $customerTimming[] = 1;
        }
        if ($customer->getAfternoon() == 1) {
            $customerTimming[] = 2;
        }
        if ($customer->getEvening() == 1) {
            $customerTimming[] = 3;
        }
        if ($customer->getMonday() == 1) {
            $customerDays[] = 1;
        }
        if ($customer->getTuesday() == 1) {
            $customerDays[] = 2;
        }
        if ($customer->getWednesday() == 1) {
            $customerDays[] = 3;
        }
        if ($customer->getThursday() == 1) {
            $customerDays[] = 4;
        }
        if ($customer->getFriday() == 1) {
            $customerDays[] = 5;
        }
        if ($customer->getSaturday() == 1) {
            $customerDays[] = 6;
        }
        if ($customer->getSunday() == 1) {
            $customerDays[] = 7;
        }


        $em = $this->em;
//        $em = $this->em->getEntityManager();
        $qb = $em->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as AMOUNT')
            ->from('App:Transactions', 't')
            ->where('t.customer = :customer');
//                ->groupBy('t.customer');
        $qb->setParameter('customer', $customer);

        $transaction = $qb->getQuery()->getResult();

        $em = $this->em;
//        $em = $this->em->getEntityManager();
        $qb = $em->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as AMOUNT')
            ->from('App:Invoices', 't')
            ->where('t.customer = :customer')
            ->groupBy('t.customer');
        $qb->setParameter('customer', $customer);

        $invoice = $qb->getQuery()->getResult();

        $customerAssets = $this->em->getRepository('App:AssetsIssuedToCustomer')->findBy(['customer' => $customer]);

//        $debitLogs = $this->em->getRepository('App:CustomerDebitLogs')->findBy(['customer' => $customer]);
        $totalOutstanding = $customer->getBalance();

        /* old way balance calculation
        $totalOutstanding = 0;

         * if ($debitLogs) {
            foreach ($debitLogs as $debitLog) {
                if ($debitLog->getType() == 1) {
                    $totalOutstanding = round(($totalOutstanding + $debitLog->getAmountAfterTax()),2);
                } else if ($debitLog->getType() == 2) {
                    $totalOutstanding = round(($totalOutstanding - ($debitLog->getAmount() * -1)),2);
                }
            }
        }*/

        // asd per Fawad bhai instruction (Aug 06 017)
//        $totalOutstanding = $totalOutstanding - $customer->getCreditLevel();
        //$avlAssetCustQty = ($customer->getQuantity() -  $customer->getDeliverQuantity());

        if (isset($transaction[0]['AMOUNT'])) {
            $sumTransactions = $transaction[0]['AMOUNT'] == NULL ? 0 : $transaction[0]['AMOUNT'];
        } else {
            $sumTransactions = 0;
        }
        if (isset($invoice[0]['AMOUNT'])) {
            $sumInvoices = $invoice[0]['AMOUNT'] == NULL ? 0 : $invoice[0]['AMOUNT'];
        } else {
            $sumInvoices = 0;
        }
        $balance = $sumInvoices - $sumTransactions;
        $creditLevel = $customer->getCreditLevel();
        /*if ($redeemedTotal == false) {
            $em = $this->em->getEntityManager();
            $qb = $em->createQueryBuilder('t');
            $qb->select('Sum(cc.worthOffAfterTax) as totalWorthOff')
                ->from('App:CouponCodes', 'cc')
//                ->innerJoin('cc.book', 'b')
//                ->where('b.isSold=:isSold')
                ->where('cc.customer=:customer')
                ->andWhere('cc.isRedeamed=1');
//            ->groupBy('t.customer');
            $qb->setParameter('customer', $customer->getId());
//            $qb->setParameter('isSold', 1);

            $redeemedTotal = $qb->getQuery()->getResult()[0]['totalWorthOff'];
        }*/
//        $balance = $balance + $redeemedTotal;
        $balance = $totalOutstanding;
        /*if ($redeemedTotal) {
            $balance = $totalOutstanding + $redeemedTotal;
        }*/

        $creditLimit = 0;
        if ($creditLevel >= 0) {
            $creditLimit = $creditLevel - $balance;
        }

        $em = $this->em;
        $customerAttachments = $em->getRepository('App:Attachments')->findBy(array('customer' => $customer->getId()));
        $customerAttachmentsUrls = [];
        foreach ($customerAttachments as $row) {
            if ($row and $row->getPath()) {
                $customerAttachmentsUrls[] = $row->getPath();
            }
        }

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $customer->getId(),
            //ConstantsConroller::SEQUENCE_ID => $customer->getId(),
            ConstantsConroller::MID => $customer->getMid(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_NAME => $customer->getName(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_ADDRESS => $customer->getAddress(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CONTACT_PERSON_NAME => $customer->getContactPersonName(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CONTACT_PERSON_CONTACT => $customer->getContactPersonContact(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CONTACT_PERSON_EMAIL => $customer->getContactPersonEmail(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CREDIT_LEVEL => $creditLevel,
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_DEBIT_LEVEL => $customer->getDebitLevel(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LAST_PURCHASE => $customer->getLastPurchase(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LATITUDE => $customer->getLatitude(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LONGITUDE => $customer->getLongitude(),
            ConstantsConroller::CUSTOMER_PARAMETER_CREDIT_LIMIT => $creditLimit,
            ConstantsConroller::CUSTOMER_PARAMETER_LAST_PURCHASE => $customer->getLastPurchase() ? $customer->getLastPurchase() : 0,
            ConstantsConroller::ASSETS_ISSUED => $customerAssets ? $this->parseAssetsIssuedToCustomerObjects($customerAssets, $request) : [],
            ConstantsConroller::DEPOSIT => 0,
            ConstantsConroller::PUCHAZES_HISTORY => [],
            ConstantsConroller::CUSTOMER_PET_OPTION_INDEX => $customer->getPetOption(),
            ConstantsConroller::CUSTOMER_5GALLON_OPTION_INDEX => $customer->getgallonOption(),
            ConstantsConroller::Customer_TYPE => $customer->getCustomerType() ? $this->parseCustomerTypesObjectByObjectForDriver($customer->getCustomerType(), $request) : (object)[],
            ConstantsConroller::CUSTOMER_CATEGORY => $customer->getCustomerCategory() ? $this->parseCustomerCategoryObjectByObjectForDriver($customer->getCustomerCategory(), $request) : (object)[],
            ConstantsConroller::ROUTE => $customer->getRoute() ? $this->parseRouteObjectByObjectForDriver($customer->getRoute(), $request) : (object)[],
            //ConstantsConroller::COLOR => '#00FF00',
            ConstantsConroller::COLOR => $customer->getColor() ? $this->parseColorObjectByObjectForDriver($customer->getColor(), $request) : (object)[],
            ConstantsConroller::SPECIAL_PRICES => $this->getSpecialPrices($customer),
            ConstantsConroller::CUSTOMER_TIMINGS => implode(',', $customerTimming),
            ConstantsConroller::CUSTOMER_DAYS => implode(',', $customerDays),
            'paid' => $sumTransactions,
            'total' => round($sumInvoices,2),
            'balance' => $balance,
            'quantity' => $customer->getQuantity() ? $customer->getQuantity() : 0,
            'deliverQuantity' => $customer->getDeliverQuantity() ? $customer->getDeliverQuantity() : 0,
            //'AvlAssetCustQty' => $customer->getDeliverQuantity() ? $customer->getDeliverQuantity() : 0,
            'AvlAssetCustQty' => $customer->getDeliverQuantityDay() ? $customer->getDeliverQuantityDay() : 0,
            'address_category' => $customer->getAddressCategory() ? $customer->getAddressCategory() : 0, //0 is Residential , 1 is Commercial and 2 is Camp
            'contact_person_landline' => $customer->getContactPersonLandline() ? $customer->getContactPersonLandline() : '',
            'area' => $customer->getArea() ? $customer->getArea() : '',
            'isdeposit' => $customer->getIsDeposit() ? $customer->getIsDeposit() : 0,
            'is_approved' => $customer->getIsApproved(),
            'vat_id' => $customer->getVatRegistrationNo(),
            'attachment_urls' => $customerAttachmentsUrls,
            'emirate_state_province' => ($customer->getEmiratesProvinceState()) ? $customer->getEmiratesProvinceState()->getName() : '',
            'discount_approved' => $customer->getDiscountApproved() ? 1 : 0,
            'not_buying' => $customer->getNotBuying() ? 1 : 0,
            'shifted_in_area' => $customer->getShiftedInArea() ? 1 : 0,
            'remarks' => $customer->getRouteChangeRemarks() ? $customer->getRouteChangeRemarks() : ''
        ];

        return $userResponse;
    }

    public function parseCustomerObjectByObjectForCustomer(Customers $customer, Request $request, $redeemedTotal = false)
    {
        $customerTimming = [];
        $customerDays = [];

        if ($customer->getMorning() == 1) {
            $customerTimming[] = 1;
        }
        if ($customer->getAfternoon() == 1) {
            $customerTimming[] = 2;
        }
        if ($customer->getEvening() == 1) {
            $customerTimming[] = 3;
        }
        if ($customer->getMonday() == 1) {
            $customerDays[] = 1;
        }
        if ($customer->getTuesday() == 1) {
            $customerDays[] = 2;
        }
        if ($customer->getWednesday() == 1) {
            $customerDays[] = 3;
        }
        if ($customer->getThursday() == 1) {
            $customerDays[] = 4;
        }
        if ($customer->getFriday() == 1) {
            $customerDays[] = 5;
        }
        if ($customer->getSaturday() == 1) {
            $customerDays[] = 6;
        }
        if ($customer->getSunday() == 1) {
            $customerDays[] = 7;
        }


        $em = $this->em;
//        $em = $this->em->getEntityManager();
        $qb = $em->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as AMOUNT')
            ->from('App:Transactions', 't')
            ->where('t.customer = :customer');
//                ->groupBy('t.customer');
        $qb->setParameter('customer', $customer);

        $transaction = $qb->getQuery()->getResult();

        $em = $this->em;
//        $em = $this->em->getEntityManager();
        $qb = $em->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as AMOUNT')
            ->from('App:Invoices', 't')
            ->where('t.customer = :customer')
            ->groupBy('t.customer');
        $qb->setParameter('customer', $customer);

        $invoice = $qb->getQuery()->getResult();

        $customerAssets = $this->em->getRepository('App:AssetsIssuedToCustomer')->findBy(['customer' => $customer]);

        $totalOutstanding = $customer->getBalance();

        if (isset($transaction[0]['AMOUNT'])) {
            $sumTransactions = $transaction[0]['AMOUNT'] == NULL ? 0 : $transaction[0]['AMOUNT'];
        } else {
            $sumTransactions = 0;
        }
        if (isset($invoice[0]['AMOUNT'])) {
            $sumInvoices = $invoice[0]['AMOUNT'] == NULL ? 0 : $invoice[0]['AMOUNT'];
        } else {
            $sumInvoices = 0;
        }
        $balance = $sumInvoices - $sumTransactions;
        $creditLevel = $customer->getCreditLevel();
        $balance = $totalOutstanding;

        $creditLimit = 0;
        if ($creditLevel >= 0) {
            $creditLimit = $creditLevel - $balance;
        }

        $em = $this->em;
        $customerAttachments = $em->getRepository('App:Attachments')->findBy(array('customer' => $customer->getId()));
        $customerAttachmentsUrls = [];
        foreach ($customerAttachments as $row) {
            if ($row and $row->getPath()) {
                $customerAttachmentsUrls[] = $row->getPath();
            }
        }

        $qrEncodedTxt = base64_encode($customer->getId() . '_' . $customer->getUsername());

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $customer->getId(),
            //ConstantsConroller::SEQUENCE_ID => $customer->getId(),
            ConstantsConroller::MID => $customer->getMid(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_NAME => $customer->getName(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_ADDRESS => $customer->getAddress(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CONTACT_PERSON_NAME => $customer->getContactPersonName(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CONTACT_PERSON_CONTACT => $customer->getContactPersonContact(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CONTACT_PERSON_EMAIL => $customer->getContactPersonEmail(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_CREDIT_LEVEL => $creditLevel,
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_DEBIT_LEVEL => $customer->getDebitLevel(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LAST_PURCHASE => $customer->getLastPurchase(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LATITUDE => $customer->getLatitude(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LONGITUDE => $customer->getLongitude(),
            ConstantsConroller::CUSTOMER_PARAMETER_CREDIT_LIMIT => $creditLimit,
            ConstantsConroller::CUSTOMER_PARAMETER_LAST_PURCHASE => $customer->getLastPurchase() ? $customer->getLastPurchase() : 0,
            ConstantsConroller::ASSETS_ISSUED => $customerAssets ? $this->parseAssetsIssuedToCustomerObjects($customerAssets, $request) : [],
            ConstantsConroller::DEPOSIT => 0,
            ConstantsConroller::PUCHAZES_HISTORY => [],
            ConstantsConroller::CUSTOMER_PET_OPTION_INDEX => $customer->getPetOption(),
            ConstantsConroller::CUSTOMER_5GALLON_OPTION_INDEX => $customer->getgallonOption(),
            ConstantsConroller::Customer_TYPE => $customer->getCustomerType() ? $this->parseCustomerTypesObjectByObjectForDriver($customer->getCustomerType(), $request) : (object)[],
            ConstantsConroller::CUSTOMER_CATEGORY => $customer->getCustomerCategory() ? $this->parseCustomerCategoryObjectByObjectForDriver($customer->getCustomerCategory(), $request) : (object)[],
            ConstantsConroller::ROUTE => $customer->getRoute() ? $this->parseRouteObjectByObjectForDriver($customer->getRoute(), $request) : (object)[],
            //ConstantsConroller::COLOR => '#00FF00',
            ConstantsConroller::COLOR => $customer->getColor() ? $this->parseColorObjectByObjectForDriver($customer->getColor(), $request) : (object)[],
            ConstantsConroller::SPECIAL_PRICES => $this->getSpecialPrices($customer),
            ConstantsConroller::CUSTOMER_TIMINGS => implode(',', $customerTimming),
            ConstantsConroller::CUSTOMER_DAYS => implode(',', $customerDays),
            'paid' => $sumTransactions,
            'total' => $sumInvoices,
            'balance' => $balance,
            'quantity' => $customer->getQuantity() ? $customer->getQuantity() : 0,
            'deliverQuantity' => $customer->getDeliverQuantity() ? $customer->getDeliverQuantity() : 0,
            //'AvlAssetCustQty' => $customer->getDeliverQuantity() ? $customer->getDeliverQuantity() : 0,
            'AvlAssetCustQty' => $customer->getDeliverQuantityDay() ? $customer->getDeliverQuantityDay() : 0,
            'address_category' => $customer->getAddressCategory() ? $customer->getAddressCategory() : 0, //0 is Residential , 1 is Commercial and 2 is Camp
            'contact_person_landline' => $customer->getContactPersonLandline() ? $customer->getContactPersonLandline() : '',
            'area' => $customer->getArea() ? $customer->getArea() : '',
            'isdeposit' => $customer->getIsDeposit() ? $customer->getIsDeposit() : 0,
            'is_approved' => $customer->getIsApproved(),
            'vat_id' => $customer->getVatRegistrationNo(),
            'attachment_urls' => $customerAttachmentsUrls,
            'qr_code_link' => $this->container->get('router')->generate('qr_code_generate', array('text' => $qrEncodedTxt, 'extension' => 'png'), UrlGenerator::ABSOLUTE_URL),
            'emirate_state_province' => ($customer->getEmiratesProvinceState()) ? $customer->getEmiratesProvinceState()->getName() : '',
            'discount_approved' => $customer->getDiscountApproved() ? 1 : 0,
            'not_buying' => $customer->getNotBuying() ? 1 : 0,
            'shifted_in_area' => $customer->getShiftedInArea() ? 1 : 0,
            'remarks' => $customer->getRouteChangeRemarks() ? $customer->getRouteChangeRemarks() : ''
        ];

        return $userResponse;
    }

    public function parseCustomerObjectByObjectForUser(Customers $customer, Request $request)
    {

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $customer->getId(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_NAME => $customer->getName(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LATITUDE => $customer->getLatitude(),
            ConstantsConroller::CUSTOMER_INPUT_PARAMETER_LONGITUDE => $customer->getLongitude(),
            ConstantsConroller::Customer_TYPE => $customer->getCustomerType() ? $this->parseCustomerTypesObjectByObjectForDriver($customer->getCustomerType(), $request) : (object)[],
            ConstantsConroller::CUSTOMER_CATEGORY => $customer->getCustomerCategory() ? $this->parseCustomerCategoryObjectByObjectForDriver($customer->getCustomerCategory(), $request) : (object)[],
            ConstantsConroller::ROUTE => $customer->getRoute() ? $this->parseRouteObjectByObjectForDriver($customer->getRoute(), $request) : (object)[],
            ConstantsConroller::COLOR => $customer->getColor() ? $this->parseColorObjectByObjectForDriver($customer->getColor(), $request) : (object)[],
            'is_approved' => $customer->getIsApproved()
        ];

        return $userResponse;
    }

    public function getSpecialPrices($customer)
    {

        $array = $this->em->getRepository('App:CustomerProductPrices')->findBy(['customer' => $customer]);
        $return = [];
        foreach ($array as $val) {
            $return[] = (object)[
                'id' => $val->getProduct()->getId(),
                'price' => round($val->getPrice(),2)
            ];
        }

        return $return;

    }

    /**
     * @param $customers
     * @param Request $request
     * @return array
     */
    public function parseCustomersArrayOfObjectsForDriver($customers, Request $request)
    {
        /*$customerIds = [];
        foreach ($customers as $rw) {
            $customerIds[] = $rw->getId();
        }
        $em = $this->em->getEntityManager();
        $qb = $em->createQueryBuilder('t');
        $qb->select('Sum(cc.worthOffAfterTax) as totalWorthOff,IDENTITY(cc.customer) as customerId')
            ->from('App:CouponCodes', 'cc')
//            ->innerJoin('cc.book', 'b')
//            ->where('b.isSold=:isSold')
            ->andWhere('cc.isRedeamed=1')
            ->andWhere('cc.customer IN (:customers)')
            ->groupBy('cc.customer');
//        $qb->setParameter('isSold', 1);
        $qb->setParameter('customers', $customerIds);

        $redeemedTotalCustomers = $qb->getQuery()->getResult();
        $redeemedTotalCustomersResults = [];
        foreach ($redeemedTotalCustomers as $row) {
            $redeemedTotalCustomersResults[$row['customerId']] = $row['totalWorthOff'];
        }*/
        $userResponse = [];
        foreach ($customers as $customer) {
            /*$redeemedTotal = 0;
            if (isset($redeemedTotalCustomersResults[$customer->getId()])) {
                $redeemedTotal = $redeemedTotalCustomersResults[$customer->getId()];
            }*/
//            $userResponse[] = $this->parseCustomerObjectByObjectForDriver($customer, $request, $redeemedTotal);
            $userResponse[] = $this->parseCustomerObjectByObjectForDriver($customer, $request, false);
        }

        return $userResponse;
    }

    public function parseCustomersArrayOfObjectsForUser($customers, Request $request)
    {

        $userResponse = [];
        foreach ($customers as $customer) {
            $userResponse[] = $this->parseCustomerObjectByObjectForUser($customer, $request);
        }

        return $userResponse;
    }

    public function parseOrderStatus(OrderStatus $orderstatus)
    {

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $orderstatus->getId(),
            ConstantsConroller::ORDER_STATUS_NAME => $orderstatus->getName()

        ];

        return $userResponse;

    }


    public function parseOrderObjectByObjectForDriver(Orders $orders, Request $request)
    {

        $orderItems = $this->em->getRepository('App:OrderItems')->findBy(['order' => $orders]);
        $order_items = $this->parseOrderItemsArrayOfObjectForDriver($orderItems, $request);

        $total_amount = 0;
        $total_discount_amount = 0;
        $after_discount = 0;
        $temp_value = 0;
        $extra_value = 0;
        $quantity = 0;
        $order_total_amount_tax = 0;
        $order_total_amount_with_tax = 0;

        if (!empty($orderItems)) {
            foreach ($orderItems as $item) {
                if ($item->getOrder() && $item->getOrder()->getId() == $orders->getId()) {
                    $total_amount += $item->getTotalAmount();
                    $total_discount_amount += $item->getDiscountAmount();
                    $after_discount += $item->getAmountAfterDiscount();
                    $temp_value += $item->getTempValue();
                    $extra_value += $item->getExtraValue();
                    $quantity += $item->getQuantity();
                    $order_total_amount_tax += $item->getTaxAmount();
                    $order_total_amount_with_tax += $item->getAmountAfterTax();
                }
            }
        }

        $transactions = $this->em->getRepository('App:Transactions')->findBy(['order' => $orders]);
        $transactionsArray = [];
        $receavedPayment = 0;
        $paymentType = 0;
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $transactionsArray[] = $this->parseTransaction2Action($transaction, $request);
                $receavedPayment += $transaction->getAmount();
                $paymentType = $transaction->getType();
            }
        }


        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $orders->getId(),
            ConstantsConroller::ORDER_CUSTOMER => $this->parseCustomerObjectByObjectForDriver($orders->getCustomer(), $request),
            ConstantsConroller::ORDER_STATUS => $this->parseOrderStatus($orders->getOrderStatus()),
            ConstantsConroller::ORDER_DELIVER_BY => $this->parseUserObjectByObjectForDriver($orders->getDeliverBy(), $request),
            ConstantsConroller::ORDER_DELIVER_ON => $orders->getDiliverOn() ? $this->parseDate($orders->getDiliverOn()) : (object)[],
            ConstantsConroller::ORDER_CREATED_ON => $orders->getCreatedOn() ? $this->parseDate($orders->getCreatedOn()) : (object)[],
            ConstantsConroller::ORDER_CREATED_BY => $this->parseUserObjectByObjectForDriver($orders->getCreatedBy(), $request),
            ConstantsConroller::ORDER_TOTAL_AMOUNT => $total_amount,
            ConstantsConroller::ORDER_TOTAL_DISCOUNT => $total_discount_amount,
            ConstantsConroller::ORDER_TOTAL_AFTER_DISCOUNT => $after_discount,
            ConstantsConroller::ORDER_TOTAL_TEMP_DEPOSIT => $temp_value,
            ConstantsConroller::ORDER_TOTAL_EXTRA_DEPOSIT => $extra_value,
            ConstantsConroller::ORDER_QUANTITY => $quantity,
            'received_payment' => $receavedPayment,
            'payment_type' => $paymentType,
            'is_recursive' => $orders->getIsRecursive(),
            'total_tax' => $order_total_amount_tax,
            'after_tax' => $order_total_amount_with_tax,
            'transactions' => $transactionsArray,
            'order_items' => $order_items,
        ];

        return $userResponse;
    }

    public function parseOrderObjectByObjectForCustomer(Orders $orders, Request $request)
    {

        $orderItems = $this->em->getRepository('App:OrderItems')->findBy(['order' => $orders]);
        $order_items = $this->parseOrderItemsArrayOfObjectForDriver($orderItems, $request);

        $total_amount = 0;
        $total_discount_amount = 0;
        $after_discount = 0;
        $temp_value = 0;
        $extra_value = 0;
        $quantity = 0;
        $order_total_amount_tax = 0;
        $order_total_amount_with_tax = 0;

        if (!empty($orderItems)) {
            foreach ($orderItems as $item) {
                if ($item->getOrder() && $item->getOrder()->getId() == $orders->getId()) {
                    $total_amount += $item->getTotalAmount();
                    $total_discount_amount += $item->getDiscountAmount();
                    $after_discount += $item->getAmountAfterDiscount();
                    $temp_value += $item->getTempValue();
                    $extra_value += $item->getExtraValue();
                    $quantity += $item->getQuantity();
                    $order_total_amount_tax += $item->getTaxAmount();
                    $order_total_amount_with_tax += $item->getAmountAfterTax();
                }
            }
        }

        $transactions = $this->em->getRepository('App:Transactions')->findBy(['order' => $orders]);
//        $transactionsArray = [];
        $receavedPayment = 0;
        $paymentType = 0;
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
//                $transactionsArray[] = $this->parseTransaction2Action($transaction, $request);
                $receavedPayment += $transaction->getAmount();
                $paymentType = $transaction->getType();
            }
        }


        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $orders->getId(),
            ConstantsConroller::ORDER_CUSTOMER => $this->parseCustomerObjectByObjectForDriver($orders->getCustomer(), $request),
            ConstantsConroller::ORDER_STATUS => $this->parseOrderStatus($orders->getOrderStatus()),
            ConstantsConroller::ORDER_DELIVER_BY => $orders->getDeliverBy() ? $this->parseUserObjectByObjectForCustomer($orders->getDeliverBy(), $request) : null,
//            ConstantsConroller::ORDER_DELIVER_ON => $orders->getDiliverOn() ? $this->parseDate($orders->getDiliverOn()) : (object)[],
//            ConstantsConroller::ORDER_CREATED_ON => $orders->getCreatedOn() ? $this->parseDate($orders->getCreatedOn()) : (object)[],
//            ConstantsConroller::ORDER_CREATED_BY => $this->parseUserObjectByObjectForDriver($orders->getCreatedBy(), $request),
            ConstantsConroller::ORDER_TOTAL_AMOUNT => $total_amount,
            ConstantsConroller::ORDER_TOTAL_DISCOUNT => $total_discount_amount,
            ConstantsConroller::ORDER_TOTAL_AFTER_DISCOUNT => $after_discount,
            ConstantsConroller::ORDER_TOTAL_TEMP_DEPOSIT => $temp_value,
            ConstantsConroller::ORDER_TOTAL_EXTRA_DEPOSIT => $extra_value,
            ConstantsConroller::ORDER_QUANTITY => $quantity,
            'received_payment' => $receavedPayment,
            'payment_type' => $paymentType,
            'is_recursive' => $orders->getIsRecursive(),
            'total_tax' => $order_total_amount_tax,
            'after_tax' => $order_total_amount_with_tax,
//            'transactions' => $transactionsArray,
            'order_items' => $order_items,
        ];

        return $userResponse;
    }

    public function parseOrderObjectByArrayForDriver($orders, Request $request)
    {

        $userResponse = [];
        foreach ($orders as $order) {
            $userResponse[] = $this->parseOrderObjectByObjectForDriver($order, $request);
        }

        return $userResponse;
    }

    public function parseOrderObjectByArrayForCustomer($orders, Request $request)
    {

        $userResponse = [];
        foreach ($orders as $order) {
            $userResponse[] = $this->parseOrderObjectByObjectForCustomer($order, $request);
        }

        return $userResponse;
    }

    public function parseOrderDetailObjectByObjectsForDriver($order, $orderItems, $request, $seqNum = -1, $loadOutId = -1)
    {
        $total_tax = 0;
        $total_amount = 0;
        $total_discount_amount = 0;
        $after_discount = 0;
        $temp_value = 0;
        $extra_value = 0;
        $quantity = 0;
        $toOrederItems = [];
        if (!empty($orderItems)) {
            foreach ($orderItems as $item) {
                if ($item->getOrder() && $item->getOrder()->getId() == $order->getId()) {
                    $total_amount += $item->getTotalAmount();
                    $total_discount_amount += $item->getDiscountAmount();
                    $after_discount += $item->getAmountAfterDiscount();
                    $temp_value += $item->getTempValue();
                    $extra_value += $item->getExtraValue();
                    $quantity += $item->getQuantity();
                    $total_tax += ($item->getProduct()->getTax() / 100) * $item->getTotalAmount();
                    $toOrederItems[] = $item;
                }
            }
        }

        $orderBooks = $this->em->getRepository('App:OrderBooks')->findBy(['order' => $order]);
        $books = [];
        if (!empty($orderBooks)) {
            foreach ($orderBooks as $oB) {
                $books[] = $this->parseBookObjects($oB->getBook(), $request);
            }
        }

        $transactions = $this->em->getRepository('App:Transactions')->findBy(['order' => $order]);
        $transactionsArray = [];
        $receavedPayment = 0;
        $receavedPaymentTax = 0;
        $receavedPaymentWithoutTax = 0;

        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $transactionsArray[] = $this->parseTransaction2Action($transaction, $request);
                $receavedPayment += $transaction->getAmount();
                $receavedPaymentTax += $transaction->getTaxAmount();
                $receavedPaymentWithoutTax += $transaction->getAmountWithoutTax();
            }
        }
        $couponApplied = $this->em->getRepository('App:OrderCouponsApplied')->findBy(['order' => $order]);
        $couponCodesList = [];
        $couponDiscount = 0;
        if (!empty($couponApplied)) {
            foreach ($couponApplied as $cp) {
                $couponCodesList[] = $cp->getCodestring();
                $couponDiscount += $cp->getCode()->getBook()->getWorthOff();
            }
        }
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $order->getId(),
            ConstantsConroller::MID => $order->getMid(),
            ConstantsConroller::SEQUENCE_NUMBER => $seqNum,
            ConstantsConroller::lOADOUT_ID => $loadOutId,
            ConstantsConroller::ORDER_CUSTOMER => $this->parseCustomerObjectByObjectForDriver($order->getCustomer(), $request),
            ConstantsConroller::ORDER_STATUS => $this->parseOrderStatus($order->getOrderStatus()),
            ConstantsConroller::ORDER_DELIVER_BY => $this->parseUserObjectByObjectForDriver($order->getDeliverBy(), $request),
//            ConstantsConroller::ORDER_DELIVER_ON => $order->getDiliverOn() ? $this->parseDate($order->getDiliverOn()) : (object)[],
            ConstantsConroller::ORDER_DELIVER_ON => (object)[
                'date' => $order->getDiliverOn()->format('d-m-Y h:i:s a'),
                'timezone' => $order->getDiliverOn()->getTimezone(),
                'timestamp' => $order->getDiliverOn()->getTimestamp()
            ],
            'deliver_on_24h' => $order->getDiliverOn(),
            ConstantsConroller::ORDER_CREATED_ON => $order->getCreatedOn() ? $this->parseDate($order->getCreatedOn()) : (object)[],
            ConstantsConroller::ORDER_CREATED_BY => $this->parseUserObjectByObjectForDriver($order->getCreatedBy(), $request),
            ConstantsConroller::ORDER_ITEMS => $this->parseOrderItemsObjectByArrayForDriver($toOrederItems, $request),
            ConstantsConroller::ORDER_BOOKS => $books,
            ConstantsConroller::TOTAL_AMOUNT => round($total_amount, 2),
            ConstantsConroller::TOTAL_DISCOUNT => round($total_discount_amount, 2),
            ConstantsConroller::TOTAL_AFTER_DISCOUNT => round($after_discount, 2),
            ConstantsConroller::TOTAL_TEMP_DEPOSIT => $temp_value,
            ConstantsConroller::TOTAL_EXTRA_DEPOSIT => $extra_value,
            ConstantsConroller::QUANTITY => $quantity,
            'before_tax' => (float)round($total_amount, 2),
            'after_tax' => (float)round($total_amount + $total_tax, 2),
            'total_tax' => (float)round($total_tax, 2),
//            'before_tax'=>
            'is_recursive' => $order->getIsRecursive(),
            'transactions' => $transactionsArray,
            //'received_payment' => $receavedPayment,
            'order_coupon_payment_with_tax' => $receavedPayment,
            'order_coupon_payment_tax_amount' => $receavedPaymentTax,
            'order_coupon_payment_without_tax' => $receavedPaymentWithoutTax,
            'coupons' => $couponCodesList,
            'coupon_discounts' => $couponDiscount,
            'amount' => $request->get('amount', 0),
            'received_payment' => $request->get('amount', 0),
            'type' => $request->get('type', 1),
            'check' => $request->get('check', ''),
            'skip_reason' => $order->getSkipReason(),
            'signature' => $order->getSignature()?$order->getSignature():''
        ];

        return $userResponse;
    }

    public function parseOrderDetailObjectByObjectsForConsumerCustomer($order, $orderItems, $request, $seqNum = -1, $loadOutId = -1)
    {
        $total_tax = 0;
        $total_amount = 0;
        $total_discount_amount = 0;
        $after_discount = 0;
        $temp_value = 0;
        $extra_value = 0;
        $quantity = 0;
        $toOrederItems = [];
        if (!empty($orderItems)) {
            foreach ($orderItems as $item) {
                if ($item->getOrder() && $item->getOrder()->getId() == $order->getId()) {
                    $total_amount += $item->getTotalAmount();
                    $total_discount_amount += $item->getDiscountAmount();
                    $after_discount += $item->getAmountAfterDiscount();
                    $temp_value += $item->getTempValue();
                    $extra_value += $item->getExtraValue();
                    $quantity += $item->getQuantity();
                    $total_tax += ($item->getProduct()->getTax() / 100) * $item->getTotalAmount();
                    $toOrederItems[] = $item;
                }
            }
        }

        $orderBooks = $this->em->getRepository('App:OrderBooks')->findBy(['order' => $order]);
        $books = [];
        if (!empty($orderBooks)) {
            foreach ($orderBooks as $oB) {
                $books[] = $this->parseBookObjects($oB->getBook(), $request);
            }
        }

        $transactions = $this->em->getRepository('App:Transactions')->findBy(['order' => $order]);
        $transactionsArray = [];
        $receavedPayment = 0;
        $receavedPaymentTax = 0;
        $receavedPaymentWithoutTax = 0;

        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $transactionsArray[] = $this->parseTransaction2Action($transaction, $request);
                $receavedPayment += $transaction->getAmount();
                $receavedPaymentTax += $transaction->getTaxAmount();
                $receavedPaymentWithoutTax += $transaction->getAmountWithoutTax();
            }
        }
        $couponApplied = $this->em->getRepository('App:OrderCouponsApplied')->findBy(['order' => $order]);
        $couponCodesList = [];
        $couponDiscount = 0;
        if (!empty($couponApplied)) {
            foreach ($couponApplied as $cp) {
                $couponCodesList[] = $cp->getCodestring();
                $couponDiscount += $cp->getCode()->getBook()->getWorthOff();
            }
        }
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $order->getId(),
            ConstantsConroller::MID => $order->getMid(),
            ConstantsConroller::SEQUENCE_NUMBER => $seqNum,
            ConstantsConroller::lOADOUT_ID => $loadOutId,
            ConstantsConroller::ORDER_CUSTOMER => $this->parseCustomerObjectByObjectForDriver($order->getCustomer(), $request),
            ConstantsConroller::ORDER_STATUS => $this->parseOrderStatus($order->getOrderStatus()),
//            ConstantsConroller::ORDER_DELIVER_BY => $this->parseUserObjectByObjectForDriver($order->getDeliverBy(), $request),
//            ConstantsConroller::ORDER_DELIVER_ON => $order->getDiliverOn() ? $this->parseDate($order->getDiliverOn()) : (object)[],
            ConstantsConroller::ORDER_DELIVER_ON => (object)[
                'date' => $order->getDiliverOn()->format('d-m-Y h:i:s a'),
                'timezone' => $order->getDiliverOn()->getTimezone(),
                'timestamp' => $order->getDiliverOn()->getTimestamp()
            ],
            'deliver_on_24h' => $order->getDiliverOn(),
            ConstantsConroller::ORDER_CREATED_ON => $order->getCreatedOn() ? $this->parseDate($order->getCreatedOn()) : (object)[],
//            ConstantsConroller::ORDER_CREATED_BY => $this->parseUserObjectByObjectForDriver($order->getCreatedBy(), $request),
            ConstantsConroller::ORDER_ITEMS => $this->parseOrderItemsObjectByArrayForCustomer($toOrederItems, $request),
            ConstantsConroller::ORDER_BOOKS => $books,
            ConstantsConroller::TOTAL_AMOUNT => $total_amount,
            ConstantsConroller::TOTAL_DISCOUNT => $total_discount_amount,
            ConstantsConroller::TOTAL_AFTER_DISCOUNT => $after_discount,
            ConstantsConroller::TOTAL_TEMP_DEPOSIT => $temp_value,
            ConstantsConroller::TOTAL_EXTRA_DEPOSIT => $extra_value,
            ConstantsConroller::QUANTITY => $quantity,
            'before_tax' => (float)round($total_amount, 2),
            'after_tax' => (float)round($total_amount + $total_tax, 2),
            'total_tax' => (float)round($total_tax, 2),
//            'before_tax'=>
            'is_recursive' => $order->getIsRecursive(),
            'transactions' => $transactionsArray,
            //'received_payment' => $receavedPayment,
            'order_coupon_payment_with_tax' => $receavedPayment,
            'order_coupon_payment_tax_amount' => $receavedPaymentTax,
            'order_coupon_payment_without_tax' => $receavedPaymentWithoutTax,
            'coupons' => $couponCodesList,
            'coupon_discounts' => $couponDiscount,
            'amount' => $request->get('amount', 0),
            'received_payment' => $request->get('amount', 0),
            'type' => $request->get('type', 1),
            'check' => $request->get('check', '')
        ];

        return $userResponse;
    }

    public function parseOrdersDetailObjectByArrayForDriver($orders, $orderItems, Request $request)
    {

        $userResponse = [];
        foreach ($orders as $order) {
            $userResponse[] = $this->parseOrderDetailObjectByObjectsForDriver($order, $orderItems, $request);
        }

        return $userResponse;
    }

    public function parseOrderItemsObjectByArrayForDriver($orderItems, $request)
    {
        $userResponse = [];
        foreach ($orderItems as $order) {
            $userResponse[] = $this->parseOrderItemObjectByObjectForDriver($order, $request);
        }
        return $userResponse;
    }

    public function parseOrderItemsObjectByArrayForCustomer($orderItems, $request)
    {
        $userResponse = [];
        foreach ($orderItems as $order) {
            $userResponse[] = $this->parseOrderItemObjectByObjectForCustomer($order, $request);
        }
        return $userResponse;
    }

    public function parseOrderItemObjectByObjectForDriver(OrderItems $orderItems, $request)
    {

        $order = $orderItems->getOrder();
        $loadOut = $order->getLoadOut();
        $product = $orderItems->getProduct();
        $loadOutItem = $this->em->getRepository('App:LoadOutItems')->findOneBy(['loadOut' => $loadOut, 'product' => $product]);

        $itemTax = ($orderItems->getProduct()->getTax() / 100) * $orderItems->getTotalAmount();

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $orderItems->getId(),
            ConstantsConroller::MID => $orderItems->getMid(),
            ConstantsConroller::ORDER_ITEM_CREATED_BY => $this->parseUserObjectByObjectForDriver($orderItems->getCreatedBy(), $request),
            ConstantsConroller::ORDER_ITEM_CREATED_ON => $orderItems->getCreatedOn() ? $this->parseDate($orderItems->getCreatedOn()) : (object)[],
            ConstantsConroller::ORDER_ITEM_PRODUCT => $this->parseProductsObjectForDriver($orderItems->getProduct(), $request, $loadOutItem),
            ConstantsConroller::ORDER_ITEM_PRODUCT_QUANTITY => $orderItems->getQuantity(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_UNIT_PRICE => round($orderItems->getUnitPrice(),2),
            ConstantsConroller::ORDER_ITEM_PRODUCT_TOTAL_AMOUNT => $orderItems->getTotalAmount(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_DISCOUNT => $orderItems->getDiscount() ? $this->parseDiscountObjectByObject($orderItems->getDiscount(), $request) : (object)[],
            ConstantsConroller::ORDER_ITEM_PRODUCT_DISCOUNTED_AMOUNT => $orderItems->getDiscountAmount(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_AFTER_DISCOUNT => $orderItems->getAmountAfterDiscount(),
            ConstantsConroller::IS_SPECIAL_PRICE => $orderItems->getHasSpecialPrice() ? 1 : 0,
            ConstantsConroller::IS_TEMP => $orderItems->getIsTemp(),
            ConstantsConroller::IS_EXTRA => $orderItems->getIsExtra(),
            ConstantsConroller::NO_OF_DAYS => $orderItems->getNoOfDays(),
            ConstantsConroller::IS_REGULAR => $orderItems->getIsRegular() ? $orderItems->getIsRegular() : 1,
            ConstantsConroller::DEPOSIT => $orderItems->getDepositAmount() ? $orderItems->getDepositAmount() : 0,
            //ConstantsConroller::EXTRA_VALUE => $orderItems->getExtraValue(),
            'extra_deposit' => $orderItems->getExtraValue(),
            'after_tax' => (float)round($orderItems->getTotalAmount() + $itemTax, 2),
            'tax' => (float)round($itemTax, 2),
            ConstantsConroller::TEMP_VALUE => $orderItems->getTempValue(),

        ];

        return $userResponse;
    }

    public function parseOrderItemObjectByObjectForCustomer(OrderItems $orderItems, $request)
    {

        $order = $orderItems->getOrder();
        $loadOut = $order->getLoadOut();
        $product = $orderItems->getProduct();
        $loadOutItem = $this->em->getRepository('App:LoadOutItems')->findOneBy(['loadOut' => $loadOut, 'product' => $product]);

        $itemTax = ($orderItems->getProduct()->getTax() / 100) * $orderItems->getTotalAmount();

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $orderItems->getId(),
            ConstantsConroller::MID => $orderItems->getMid(),
//            ConstantsConroller::ORDER_ITEM_CREATED_BY => $this->parseUserObjectByObjectForDriver($orderItems->getCreatedBy(), $request),
            ConstantsConroller::ORDER_ITEM_CREATED_ON => $orderItems->getCreatedOn() ? $this->parseDate($orderItems->getCreatedOn()) : (object)[],
            ConstantsConroller::ORDER_ITEM_PRODUCT => $this->parseProductsObjectForDriver($orderItems->getProduct(), $request, $loadOutItem),
            ConstantsConroller::ORDER_ITEM_PRODUCT_QUANTITY => $orderItems->getQuantity(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_UNIT_PRICE => $orderItems->getUnitPrice(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_TOTAL_AMOUNT => $orderItems->getTotalAmount(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_DISCOUNT => $orderItems->getDiscount() ? $this->parseDiscountObjectByObject($orderItems->getDiscount(), $request) : (object)[],
            ConstantsConroller::ORDER_ITEM_PRODUCT_DISCOUNTED_AMOUNT => $orderItems->getDiscountAmount(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_AFTER_DISCOUNT => $orderItems->getAmountAfterDiscount(),
            ConstantsConroller::IS_SPECIAL_PRICE => $orderItems->getHasSpecialPrice() ? 1 : 0,
            ConstantsConroller::IS_TEMP => $orderItems->getIsTemp(),
            ConstantsConroller::IS_EXTRA => $orderItems->getIsExtra(),
            ConstantsConroller::NO_OF_DAYS => $orderItems->getNoOfDays(),
            ConstantsConroller::IS_REGULAR => $orderItems->getIsRegular() ? $orderItems->getIsRegular() : 1,
            ConstantsConroller::DEPOSIT => $orderItems->getDepositAmount() ? $orderItems->getDepositAmount() : 0,
            //ConstantsConroller::EXTRA_VALUE => $orderItems->getExtraValue(),
            'extra_deposit' => $orderItems->getExtraValue(),
            'after_tax' => (float)round($orderItems->getTotalAmount() + $itemTax, 2),
            'tax' => (float)round($itemTax, 2),
            ConstantsConroller::TEMP_VALUE => $orderItems->getTempValue(),

        ];

        return $userResponse;
    }


    public function parseDiscountArrayofObjects($array, $request)
    {
        $userResponse = [];
        foreach ($array as $requestType) {
            $userResponse[] = $this->parseDiscountObjectByObject($requestType, $request);
        }

        return $userResponse;
    }

    public function parseDiscountObjectByObject(Discounts $discounts, $request)
    {
        $userResponse = (object)[
            'id' => $discounts->getId(),
            'name' => $discounts->getName(),
            'discount' => '',
            'promotion_code' => '',
            'route' => '',
            'van' => '',
            'applicable' => '',
            'terms_and_conditions' => '',
            'amount' => $discounts->getDiscountAmount(),
            'policy' => $this->parseDiscountPolicyObjectByObject($discounts->getDiscountPolicy()),
            'discount_type' => $this->parseDiscountTypeObjectByObject($discounts->getDiscountType()),
            'is_active' => $discounts->getIsActive(),
            'product' => $this->parseProductsObjectForDriver($discounts->getProduct(), $request),
            'max_quantity' => $discounts->getQuantityMax(),
            'min_quantity' => $discounts->getQuantityMin(),
            ConstantsConroller::ROUTE => $discounts->getRoute() ? $this->parseRouteObjectByObjectForDriver($discounts->getRoute(), $request) : (object)[],
        ];

        return $userResponse;
    }

    public function parseDiscountPolicyObjectByObject(DiscountPolicy $object)
    {
        $userResponse = (object)[
            'id' => $object->getId(),
            'name' => $object->getName()
        ];
        return $userResponse;
    }

    public function parseDiscountTypeObjectByObject(DiscountType $object)
    {
        $userResponse = (object)[
            'id' => $object->getId(),
            'name' => $object->getName()
        ];
        return $userResponse;
    }

    public function parseRequestTypesArrayOfObjectsForDriver($requestTypes, $request)
    {
        $userResponse = [];
        foreach ($requestTypes as $requestType) {
            $userResponse[] = $this->parseRequestTypesObjectByObjectsForDriver($requestType, $request);
        }

        return $userResponse;
    }

    public function parseRequestTypesObjectByObjectsForDriver(RequestTypes $requestType, $request)
    {

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $requestType->getId(),
            ConstantsConroller::NAME => $requestType->getName(),
        ];
        return $userResponse;
    }

    public function parseRequestObjectByObjectForDriver(Requests $req, $requestItems = [], $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
            ConstantsConroller::REQUEST_TYPE => $this->parseRequestTypesObjectByObjectsForDriver($req->getRequestType(), $request),
            ConstantsConroller::STATUS => $req->getStatus(),
            ConstantsConroller::CUSTOMER => $req->getForCustomer() ? $this->parseCustomerObjectByObjectForDriver($req->getForCustomer(), $request) : (object)[],
            ConstantsConroller::CREATED_BY => $req->getFromUser() ? $this->parseUserObjectByObjectForDriver($req->getFromUser(), $request) : (object)[],
            ConstantsConroller::CREATED_ON => $req->getCreatedOn() ? $this->parseDate($req->getCreatedOn()) : (object)[],

            ConstantsConroller::USER => $req->getForUser() ? $this->parseUserObjectByObjectForDriver($req->getForUser(), $request) : (object)[],
            ConstantsConroller::LEAVE_DAYS => $req->getLeaveDays(),
            ConstantsConroller::LEAVE_REASON => $req->getLeaveReason(),
            ConstantsConroller::LEAVE_START_DATE => $req->getLeaveStartDate(),

            ConstantsConroller::DISCOUNT => $req->getDiscount() ? $this->parseDiscountObjectByObject($req->getDiscount(), $request) : (object)[],

            ConstantsConroller::DISCOUNT_AMOUNT => $req->getLeaveDays(),

            ConstantsConroller::VEHICLE => $req->getVehicle() ? $this->parseVehicleObjectByObjectForDriver($req->getVehicle(), $request) : (object)[],

            ConstantsConroller::MAINTINANCE_REASON => $req->getMaintinanceRequestReason(),

            ConstantsConroller::FEUL_AMOUNT => $req->getFuelAmount(),
            ConstantsConroller::FEUL_GRADE => $req->getFuelGrades(),
            ConstantsConroller::FEUL_LITTERS => $req->getFuelLitters(),
            ConstantsConroller::REQUEST_ITEMS => !empty($requestItems) ? $this->parseRequestItemsByArrayOfObjectsForDriver($requestItems, $request) : [],
            ConstantsConroller::LOAD_OUT_TOTAL_QUANTITY => !empty($requestItems) ? $this->getTotalRequestItemsByArrayOfObjectsForDriver($requestItems, $request) : 0,
            ConstantsConroller::LOAD_OUT_TOTAL_BASE_PRICE => !empty($requestItems) ? $this->getTotalBasePriceRequestItemsByArrayOfObjectsForDriver($requestItems, $request) : 0,
            ConstantsConroller::REEQUEST_ISSUED_ASSETS => $req->getAssetsIssuedToCustomer() ? $this->parseRequestAssetsObjectByObjectsForDriver($req->getAssetsIssuedToCustomer(), $request) : (object)[],

        ];
        return $userResponse;
    }

    public function parseRequestAssetsObjectByObjectsForDriver(AssetsIssuedToCustomer $assetsIssuedToCustomer, $request)
    {

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $assetsIssuedToCustomer->getId(),
            ConstantsConroller::NAME => $assetsIssuedToCustomer->getAsset()->getName(),
            ConstantsConroller::QUANTITY => $assetsIssuedToCustomer->getQuantity(),
            ConstantsConroller::STATUS => $assetsIssuedToCustomer->getStatusText(),
            ConstantsConroller::DEPOSIT => $assetsIssuedToCustomer->getDepositAmount(),
            ConstantsConroller::NO_OF_DAYS => $assetsIssuedToCustomer->getNoOfDays(),
            ConstantsConroller::TYPE => $assetsIssuedToCustomer->getIsApprovedText(),
            ConstantsConroller::QUANTITY => $assetsIssuedToCustomer->getType(),
        ];
        return $userResponse;
    }


    public function getTotalRequestItemsByArrayOfObjectsForDriver($requestItems, $request)
    {
        $userResponse = 0;
        foreach ($requestItems as $requestItem) {
            $userResponse += $requestItem->getQuantity();
        }

        return $userResponse;
    }

    public function getTotalBasePriceRequestItemsByArrayOfObjectsForDriver($requestItems, $request)
    {
        $userResponse = 0;
        foreach ($requestItems as $requestItem) {

            $userResponse += (($requestItem->getQuantity()) * ($requestItem->getProduct()->getBasePrice()));
        }

        return $userResponse;
    }

    public function parseLoadOutPlan(LoadOut $loadOut, $loadOutItems, $request, $q = 0, $p = 0)
    {

        $loadOutBooks = $this->em->getRepository('App:LoadOutBooks')->findBy(['loadOut' => $loadOut]);
        $booksArray = [];
        if (!empty($loadOutBooks)) {
            foreach ($loadOutBooks as $lb) {
                if ($lb->getBook()->getIsSold() == 0) {
                    $booksArray[] = $this->parseBookObjects($lb->getBook(), $request);
                }
            }
        }

        $loadOutAssets = $this->em->getRepository('App:LoadOutAssets')->findBy(['loadOut' => $loadOut]);
        $loadOutAssetsArray = [];

        if (!empty($loadOutAssets)) {
            foreach ($loadOutAssets as $la) {
                $loadOutAssetsArray[] = $this->parseAssetObjectForDriver($la->getAsset(), $la->getQuantity(), $loadOut);
            }
        }

//        if(!empty($loadOutAssets)) {
//            foreach ($loadOutAssets as $la) {
//                $loadOutAssetsArray[] = [
//                    'asset' => $this->parseAssetObject($la->getAsset()),
//                    'quantity' => $la->getQuantity()
//                ];
//            }
//        }

//        ? $this->parseDate($loadOut->getCreatedOn()) : (object)[]
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $loadOut->getId(),
            ConstantsConroller::CREATED_ON => $loadOut->getCreatedOn() ? $this->parseDate($loadOut->getCreatedOn()) : (object)[],
            ConstantsConroller::DRIVER => $this->parseUserObjectByObjectForDriver($loadOut->getDriver(), $request),
            ConstantsConroller::ITEMS => $this->parseLoadOutItemObjectByArrayForDriver($loadOutItems, $request),
            ConstantsConroller::STATUS => $loadOut->getStatus(),
            ConstantsConroller::LOAD_OUT_TOTAL_QUANTITY => $q,
            ConstantsConroller::LOAD_OUT_TOTAL_BASE_PRICE => $p,
            'books' => $booksArray,
            'assets' => $loadOutAssetsArray
        ];

        return $userResponse;

    }

    public function parseAssetObjectForDriver(Assets $asset, $quantity = 0, LoadOut $loadOut)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $asset->getId(),
            ConstantsConroller::NAME => $asset->getName(),
            ConstantsConroller::QUANTITY => $quantity,
            'asset_serials' => $this->parseAssetSerialsToAssetObjectForDriver($asset, $loadOut),
        ];
        return $userResponse;
    }

    public function parseAssetSerialsToAssetObjectForDriver(Assets $asset, LoadOut $loadOut)
    {
        $userResponse = [];
        if ($asset->getId() == 2) {
            $loadOutAssetSerials = $this->em->getRepository('App:LoadOutAssetSerials')->findBy(['asset' => $asset, 'loadOut' => $loadOut]);
            if ($loadOutAssetSerials) {
                foreach ($loadOutAssetSerials as $loadOutAssetSerial) {
                    $assetSerial = $this->em->getRepository('App:AssetSerials')->findOneBy(['id' => $loadOutAssetSerial->getAssetSerial()->getId(), 'isSale' => 0]);
                    if ($assetSerial) {
                        $userResponse[] = $this->parseAssetSerialObject($assetSerial);
                    }
                }
            }
        }
        return $userResponse;
    }

    public function parseAssetObject(Assets $asset, $quantity = 0)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $asset->getId(),
            ConstantsConroller::NAME => $asset->getName(),
            ConstantsConroller::QUANTITY => $quantity,
            'asset_serials' => $this->parseAssetSerialsToAssetObjects($asset),
        ];
        return $userResponse;
    }

    public function parseAssetSerialsToAssetObjects(Assets $asset)
    {
        $userResponse = [];
        if ($asset->getId() == 2) {
            $assetSerials = $this->em->getRepository('App:AssetSerials')->findBy(['asset' => $asset, 'isSale' => 0]);
            if ($assetSerials) {
                foreach ($assetSerials as $assetSerial) {
                    $userResponse[] = $this->parseAssetSerialObject($assetSerial);
                }
            }

        }
        return $userResponse;
    }

    public function parseAssetSerialObject($assetSerial)
    {

        $userResponse = (Object)[
            'asset_id' => $assetSerial->getAsset()->getId(),
            'serial_id' => $assetSerial->getId(),
            'serial_no' => $assetSerial->getSerialNo(),
            'is_sale' => $assetSerial->getIsSale(),
            'serial_type' => $assetSerial->getSerialTypeText(),
            'sale_date' => $assetSerial->getSaleDate() ? $assetSerial->getSaleDate() : '',
            'customer_id' => $assetSerial->getCustomer() ? $assetSerial->getCustomer()->getId() : '',
            'unit_price' => $assetSerial->getUnitPrice() ? (double)$assetSerial->getUnitPrice() : 0,
            'tax_percentage' => $assetSerial->getTaxPercent() ? (double)$assetSerial->getTaxPercent() : 0,
            'tax_amount' => !empty($assetSerial->getTaxAmount()) ? (double)$assetSerial->getTaxAmount() : 0,
            'unit_price_with_tax' => $assetSerial->getUnitPriceWithTax() ? (double)$assetSerial->getUnitPriceWithTax() : 0,

        ];
        return $userResponse;
    }


    public function parseLoadOutItemObjectByArrayForDriver($loadOutItems, $request)
    {
        $userResponse = [];
        foreach ($loadOutItems as $loadOutItem) {
            $userResponse[] = $this->parseLoadOutItemObjectByObjectForDriver($loadOutItem, $request);
        }

        return $userResponse;
    }

    public function parseLoadOutItemObjectByObjectForDriver(LoadOutItems $loadOutItems, $request)
    {

        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $loadOutItems->getId(),
            ConstantsConroller::CREATED_ON => $loadOutItems->getCreatedOn() ? $this->parseDate($loadOutItems->getCreatedOn()) : (object)[],
//            ConstantsConroller::CREATED_ON => $loadOutItems->getCreatedOn(),
            ConstantsConroller::PRODUCT => $this->parseProductsObjectForDriver($loadOutItems->getProduct(), $request, $loadOutItems),
            ConstantsConroller::QUANTITY => $loadOutItems->getQuantity(),
            ConstantsConroller::DRIVER => $this->parseUserObjectByObjectForDriver($loadOutItems->getDriver(), $request),
            ConstantsConroller::STATUS => 0,
            ConstantsConroller::TYPE => 'PRODUCT'
        ];

        return $userResponse;
    }

    public function parseCustomerTypesArrayOfObjectsForDriver($customerTypes, $request)
    {
        $userResponse = [];
        foreach ($customerTypes as $customerType) {
            $userResponse[] = $this->parseCustomerTypesObjectByObjectForDriver($customerType, $request);
        }

        return $userResponse;
    }

    public function parseCustomerTypesObjectByObjectForDriver(CustomerTypes $customerType, $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $customerType->getId(),
            ConstantsConroller::NAME => $customerType->getName()
        ];

        return $userResponse;
    }

    public function parseCustomerCategoryArrayOfObjectsForDriver($customerCategories, $request)
    {
        $userResponse = [];
        foreach ($customerCategories as $customerCategory) {
            $userResponse[] = $this->parseCustomerCategoryObjectByObjectForDriver($customerCategory, $request);
        }

        return $userResponse;
    }

    public function parseCustomerCategoryObjectByObjectForDriver(CustomerCategories $customerCategory, $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $customerCategory->getId(),
            ConstantsConroller::NAME => $customerCategory->getName()
        ];

        return $userResponse;
    }

    public function parseColorsArrayOfObjectsForDriver($colors, $request)
    {
        $userResponse = [];
        foreach ($colors as $color) {
            $userResponse[] = $this->parseColorObjectByObjectForDriver($color, $request);
        }

        return $userResponse;
    }

    public function parseColorObjectByObjectForDriver(Colors $color, $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $color->getId(),
            ConstantsConroller::NAME => $color->getName(),
            ConstantsConroller::HEXCODE => $color->getHexCode()
        ];

        return $userResponse;
    }

    public function parseRoutesArrayOfObjectsForDriver($routes, $request)
    {
        $userResponse = [];
        foreach ($routes as $route) {
            $userResponse[] = $this->parseRouteObjectByObjectForDriver($route, $request);
        }

        return $userResponse;
    }

    public function parseRouteObjectByObjectForDriver(Routes $route, $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $route->getId(),
            ConstantsConroller::NAME => $route->getName(),
        ];

        return $userResponse;
    }

    public function parseRequestArrayofObjectsForDriver($arrayOfRequests, $items = [], $request)
    {
        $userResponse = [];
        foreach ($arrayOfRequests as $requestObj) {
            $itemTopas = [];
            foreach ($items as $item) {
                if ($item->getRequest()->getId() == $requestObj->getId()) {
                    $itemTopas[] = $item;
                }
            }
            $userResponse[] = $this->parseRequestObjectByObjectForDriver($requestObj, $itemTopas, $request);
        }

        return $userResponse;
    }

    public function parseUserRole(UserRole $role, $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $role->getId(),
            ConstantsConroller::NAME => $role->getRoleType()
        ];

        return $userResponse;
    }

    public function parseRequestObjectbyObjectsForDriver(Requests $object, $request)
    {
        $userResponse = (object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $object->getId(),
            ConstantsConroller::FROM_USER => $this->parseUserObjectByObjectForDriver($object->getFromUser(), $request),
            ConstantsConroller::FOR_CUSTOMER => $object->getForCustomer() ? $this->parseCustomerObjectByObjectForDriver($object->getForCustomer(), $request) : (object)[],
            ConstantsConroller::FOR_USER => $object->getForUser() ? $this->parseUserObjectByObjectForDriver($object->getForUser(), $request) : (object)[],
            ConstantsConroller::REQUEST_TYPE => $this->parseRequestTypeObjectByObjectForDriver($object->getRequestType(), $request),
            ConstantsConroller::TO_ROLE => $object->getToRole() ? $this->parseUserRole($object->getToRole(), $request) : (object)[],
            ConstantsConroller::STATUS => $object->getStatus(),
            ConstantsConroller::CREATED_ON => $object->getCreatedOn() ? $this->parseDate($object->getCreatedOn()) : (object)[],
            ConstantsConroller::MODIFIED_ON => $object->getModifiedOn() ? $this->parseDate($object->getModifiedOn()) : (object)[]

        ];

        return $userResponse;
    }

    public function getProductImageObj($product)
    {

        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => 0,
            ConstantsConroller::IMAGE_PATH => $product->getImageUrl()
        ];

        return $userResponse;
    }

    public function getDefaultImage()
    {

        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => 0,
            ConstantsConroller::IMAGE_PATH => 'http://52.221.44.150/ahlan/web/bottle 1440.png'
        ];

        return $userResponse;
    }

    public function parseSettlementProductsArrayofObjectsForDriver($arrayOfRequests, $request)
    {
        $userResponse = [];
        foreach ($arrayOfRequests as $requestObj) {
            $userResponse[] = $this->parseSettlementProductsObjectsForDriver($requestObj, $request);
        }

        return $userResponse;
    }

    public function parseSettlementProductsObjectsForDriver(LoadOutSattlmentRequest $item, $request)
    {

        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $item->getId(),
            ConstantsConroller::DRIVERS_INDEX => $item->getDriver() ? $this->parseUserObjectByObjectForDriver($item->getDriver(), $request) : (object)[],
            ConstantsConroller::PRODUCT => $item->getProduct() ? $this->parseProductsObjectForDriver($item->getProduct(), $request) : (object)[],
            ConstantsConroller::REQUIRE_ON => $item->getRequiredOn() ? $this->parseDate($item->getRequiredOn()) : (object)[],
            ConstantsConroller::QUANTITY => $item->getQuantity(),
        ];

        return $userResponse;
    }

    public function parseRequestItemsByArrayOfObjectsForDriver($requestItems, $request)
    {
        $userResponse = [];
        foreach ($requestItems as $requestItem) {
            $userResponse[] = $this->parseRequestItemsByObjectForDriver($requestItem, $request);
        }

        return $userResponse;
    }

    public function parseRequestItemsByObjectForDriver(RequestProducts $item, $request)
    {
        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $item->getId(),
            ConstantsConroller::ORDER_ITEM_PRODUCT => $item->getProduct() ? $this->parseProductsObjectForDriver($item->getProduct(), $request) : (object)[],
            ConstantsConroller::QUANTITY => $item->getQuantity()
        ];
        return $userResponse;
    }

    public function parseSettlementWithItems(LoadOutSattlements $sattlements, $itemsArray, $request)
    {

        $itemsObjs = [];
        foreach ($itemsArray as $item) {

            $itemsObjs[] = (object)[
                ConstantsConroller::DEFAULT_ID_INDEX => $item->getId(),
                ConstantsConroller::CREATED_ON => $item->getCreatedOn() ? $this->parseDate($item->getCreatedOn()) : (object)[],
                ConstantsConroller::PRODUCT => $item->getProduct() ? $this->parseProductsObjectForDriver($item->getProduct(), $request) : (object)[],
                ConstantsConroller::QUANTITY => $item->getQuantity()
            ];

        }

        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $sattlements->getId(),
            ConstantsConroller::CREATED_ON => $sattlements->getCreatedOn() ? $this->parseDate($sattlements->getCreatedOn()) : (object)[],
            ConstantsConroller::DRIVER => $sattlements->getDriver() ? $this->parseUserObjectByObjectForDriver($sattlements->getDriver(), $request) : (object)[],
            ConstantsConroller::REQUIRE_ON => $sattlements->getRequiredOn() ? $this->parseDate($sattlements->getRequiredOn()) : (object)[],
            ConstantsConroller::STATUS_ID => $sattlements->getStatus(),
            ConstantsConroller::STATUS => $sattlements->getStatusText(),
            ConstantsConroller::ITEMS => $itemsObjs,
        ];
        return $userResponse;

    }

    public function parseBooksArrayOfObjects($books, Request $request)
    {
        $userResponse = [];
        foreach ($books as $book) {
            if ($book != null) {
                $userResponse[] = $this->parseBookObjects($book, $request);
            }
        }

        return $userResponse;
    }


    public function parseBookObjects(CouponBooks $book, $request)
    {

        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $book->getId(),
            ConstantsConroller::NAME => $book->getName(),
            ConstantsConroller::ORDER_ITEM_PRODUCT_UNIT_PRICE => $book->getPrice(),
            ConstantsConroller::COUNT => $book->getCouponBookType()->getCounts(),
            ConstantsConroller::PUBLIC_ID => $book->getPublicId(),
            'tax_percent' => (double)$book->getTaxPercent(),
            'price_tax' => (double)$book->getPriceTax(),
            'price_after_tax' => (double)$book->getPriceAfterTax(),
            'worth_off' => (double)$book->getWorthOff(),
            'worth_off_tax' => (double)$book->getWorthOffTax(),
            'worth_off_after_tax' => (double)$book->getWorthOffAfterTax(),

        ];
        return $userResponse;

    }

    public function parseAssetsIssuedToCustomerObjects($assetsToCustomerAray, Request $request)
    {
        $userResponse = [];
        if ($assetsToCustomerAray) {
            foreach ($assetsToCustomerAray as $asset) {
                $userResponse[] = $this->parseAssetIssuedObject($asset, $request);
            }
        }
        return $userResponse;
    }

    public function parseAssetIssuedObject(AssetsIssuedToCustomer $asset, $request)
    {
        $userResponse = [];
        if ($asset) {
            $userResponse = (Object)[
                ConstantsConroller::DEFAULT_ID_INDEX => $asset->getId(),
                'name' => (string)$asset->getAsset(),
                ConstantsConroller::ASSET_ID => $asset->getAsset()->getId(),
                ConstantsConroller::QUANTITY => $asset->getQuantity(),
                ConstantsConroller::ORDER_DELIVER_ON => $userResponse = (object)[
                    'date' => $asset->getDeliveryDate()->format('d-m-Y h:i:s a'),
                    'timezone' => $asset->getDeliveryDate()->getTimezone(),
                    'timestamp' => $asset->getDeliveryDate()->getTimestamp()
                ],
                'deliver_on_24h' => $userResponse = $asset->getDeliveryDate(),
//            $this->parseDate($asset->getDeliveryDate()),
                ConstantsConroller::DEPOSIT => $asset->getDepositAmount(),
                ConstantsConroller::ORDER_STATUS => $asset->getStatus(),
                ConstantsConroller::TYPE => $asset->getType(),
                'deposit_amount_tax' => (double)$asset->getTaxPrice(),
                'deposit_amount_with_tax' => (double)$asset->getPriceAfterTax(),
            ];
        }
        return $userResponse;

    }

    public function parseInvoicesAndTransacton($invoices, $transactions, $debitLogs, $request)
    {
        $em = $this->em;
        $transactionArray = [];
        if ($debitLogs) {
            $balance = 0;
            $orderIds = [];
            foreach ($debitLogs as $debitLog) {
                $orderIds[] = $debitLog->getOrder()->getId();
            }
            $qb = $em->createQueryBuilder('t');
            $qb->select('IDENTITY(oi.order) as orderId,p.id as product_id,oi.quantity as product_quantity')
                ->from('App:OrderItems', 'oi')
                ->innerJoin('oi.product', 'p')
                ->where('oi.order IN (:order)');
            $qb->setParameter('order', $orderIds);
            $orderProducts = $qb->getQuery()->getResult();
            $orderProductsResults = [];

            foreach ($orderProducts as $row) {
                $orderProductsResults[$row['orderId']][] = $row;
            }

            foreach ($debitLogs as $debitLog) {
//                if ($debitLog->getOrder()->getorderItems()[0]) {

                if ($debitLog->getType() == 1) {
                    $balance = $balance + $debitLog->getAmount() + $debitLog->getTaxAmount();
                } else if ($debitLog->getType() == 2) {
                    $balance = $balance - ($debitLog->getAmount() * -1);
                }

                try {
                    $orderInvoices = $debitLog->getOrder()->getOrderInvoice();
                    $orderInvoiceId = isset($orderInvoices) ? $debitLog->getOrder()->getOrderInvoice()->getId() : 0;
                } catch (\Exception $exp) {
                    $orderInvoiceId = 0;
                }
                $orderId = $debitLog->getOrder()->getId();
                //if($debitLog->getType() == 2){
                $transactionArray[] = (object)[
                    'transactiontype' => $debitLog->getType(),
                    'orderid' => $debitLog->getOrder()->getId(),
                    'order' => isset($orderProductsResults[$orderId]) ? $orderProductsResults[$orderId] : array(),
                    'amount' => $debitLog->getAmount(),
                    'amount_tax' => (double)$debitLog->getTaxAmount(),
                    'amount_with_tax' => (double)$debitLog->getAmountAfterTax(),
                    'amount_without_tax_transaction' => (double)$debitLog->getAmountWithoutTax(),
                    'balance' => round($balance, 3),
//                    'invoice_id' => isset($debitLog->getOrder()->getOrderInvoices()[0]) ? $debitLog->getOrder()->getOrderInvoices()[0]->getId() : 0,
                    'invoice_id' => $orderInvoiceId,
                    //'itemid' => $debitLog->getOrder()->getorderItems()[0]->getId(),
                    //'productname' => (string)$debitLog->getOrder()->getorderItems()[0]->getProduct(),
                    //'unitprice' => (Float)$debitLog->getOrder()->getorderItems()[0]->getProduct()->getBasePrice(),
                    //'quantity' => (Float)$debitLog->getOrder()->getorderItems()[0]->getQuantity(),
                    //'totalPrice' => (Float)$debitLog->getOrder()->getorderItems()[0]->getTotalAmount(),
                    //'discountAmount' => (Float)$debitLog->getOrder()->getorderItems()[0]->getDiscountAmount(),
                    //'amountAfterDiscount' => (Float)$debitLog->getOrder()->getorderItems()[0]->getAmountAfterDiscount(),
                    'created_Date' => $debitLog->getCreatedOn()->format('Y-m-d H:i:s'),
                    'customerId' => $debitLog->getCustomer()->getId()
                ];
                //}
//
//                }
            }
        }


//        if($transactions){
//            foreach($transactions as $transaction){
//
//                $transactionArray[] = (object) [
//                  'transactionid' => $transaction->getInvoice()->getId(),
//                  'orderid' => $transaction->getOrder()->getId(),
//                  'amount' => $transaction->getInvoice()->getAmount(),
//                  'itemid' => $transaction->getOrder()->getorderItems()[0]?$transaction->getOrder()->getorderItems()[0]->getId():'',
//                  'productname' => $transaction->getOrder()->getorderItems()[0]?(string)$transaction->getOrder()->getorderItems()[0]->getProduct():'',
//                  'unitprice' => $transaction->getOrder()->getorderItems()[0]?(Float)$transaction->getOrder()->getorderItems()[0]->getProduct()->getBasePrice():0,
//                  'quantity' => $transaction->getOrder()->getorderItems()[0]?(Float)$transaction->getOrder()->getorderItems()[0]->getQuantity():0,
//                  'totalPrice' => $transaction->getOrder()->getorderItems()[0]?(Float)$transaction->getOrder()->getorderItems()[0]->getTotalAmount():0,
//                  'created_Date' => $transaction->getCreatedOn()->format('Y-m-d H:i:s'),
//                  'customerId' => $transaction->getCustomer()->getId()
//                ];
//            }
//        }


        //$userResponse = [];

        //foreach($invoices as $invoice){
        //  $userResponse[] = $this->parseInvoiceAction($invoice,$transactionArray[$invoice->getId()], $request);
        //}
        //return $userResponse;
        return $transactionArray;
    }


    public function parseInvoiceAction(Invoices $invoice, $transactions, $request)
    {
        $transactionObjects = [];

        foreach ($transactions as $trans) {
            $transactionObjects[] = $this->parseTransactionAction($trans, $request);
        }

        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $invoice->getId(),
            ConstantsConroller::AMOUNT => $invoice->getAmount(),
            ConstantsConroller::CREATED_ON => $invoice->getCreatedOn() ? $this->parseDate($invoice->getCreatedOn()) : (object)[],
            ConstantsConroller::ORDER => $invoice->getOrder() ? $this->parseOrderObjectByObjectForDriver($invoice->getOrder(), $request) : (object)[],
            ConstantsConroller::TRANSACTIONS => $transactionObjects

        ];
        return $userResponse;
    }

    public function parseTransactionAction(Transactions $transaction, $request)
    {
        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $transaction->getId(),
            ConstantsConroller::ORDER => $transaction->getOrder() ? $this->parseOrderObjectByObjectForDriver($transaction->getOrder(), $request) : (object)[],
            ConstantsConroller::AMOUNT => $transaction->getAmount(),
            ConstantsConroller::CHECK_NUMBER => $transaction->getCheckNumber(),
            ConstantsConroller::CUSTOMER => $transaction->getCustomer() ? $this->parseCustomerObjectByObjectForDriver($transaction->getCustomer(), $request) : (object)[],
            ConstantsConroller::CREATED_ON => $transaction->getCreatedOn() ? $this->parseDate($transaction->getCreatedOn()) : (object)[],
            ConstantsConroller::CREATED_BY => $transaction->getReceavedBy() ? $this->parseUserObjectByObjectForDriver($transaction->getReceavedBy(), $request) : (object)[]

        ];
        return $userResponse;
    }

    public function parseTransaction2Action(Transactions $transaction, $request)
    {
        $userResponse = (Object)[
            'invoice_id' => $transaction->getInvoice()->getId(),
            ConstantsConroller::DEFAULT_ID_INDEX => $transaction->getId(),
            ConstantsConroller::AMOUNT => round($transaction->getAmount(),2),
            ConstantsConroller::CHECK_NUMBER => $transaction->getCheckNumber(),
            ConstantsConroller::PAYMENT_TYPE => $transaction->getType(),
            ConstantsConroller::CUSTOMER => $transaction->getCustomer() ? $this->parseCustomerObjectByObjectForDriver($transaction->getCustomer(), $request) : (object)[],
            ConstantsConroller::CREATED_ON => $transaction->getCreatedOn() ? $this->parseDate($transaction->getCreatedOn()) : (object)[],
            ConstantsConroller::CREATED_BY => $transaction->getReceavedBy() ? $this->parseUserObjectByObjectForDriver($transaction->getReceavedBy(), $request) : (object)[]

        ];
        return $userResponse;
    }


    //RESPONSE PARSING


    public $_RESPONSE = [];

    /**
     * @param $status
     * @param $data
     * @param string $message
     * @param array $messages
     */
    public function buildResponse($data, $status = '204', $message = '', $messages = [], $token = '')
    {
        $array = [
            'status' => $status,
            'msg' => $message,
            'msgs' => $messages,
            'data' => $data
        ];
        if ($token != '') {
            $array['access_token'] = $token;
        }
        $response = new Response(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode($status);
        $this->_RESPONSE = $response;
    }

    /**
     * @return array
     */
    public function generateResponse()
    {
        return $this->_RESPONSE;
    }


    /**
     * EXTRA FUNCTIONS
     */

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @return string
     */
    public function genAccessToken()
    {
        $num = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        return sha1($num);
    }

    public function loginError()
    {
        $this->buildResponse(
            [],
            ConstantsConroller::UNAUTHORIZED,
            ConstantsConroller::MSG_INVALID_ACCESS,
            [ConstantsConroller::MSG_INVALID_ACCESS]
        );
    }

    public function invalidRecivedpayment()
    {
        $this->buildResponse(
            [],
            ConstantsConroller::PAYMENT_REQUIRED,
            ConstantsConroller::RECIVED_PAYMENT_IS_LESS_THEN,
            [ConstantsConroller::RECIVED_PAYMENT_IS_LESS_THEN]
        );
    }

    public function parseAssetsDetailObjectByObjectsForCustomer($customerAssets, $request)
    {

        //1-Empties
        //2-Cooler Dispenser
        $customer = $customerAssets[0]->getCustomer();

        $total_empties_quantity = 0;
        $total_empties_deposit_amount = 0;

        $total_cooler_quantity = 0;
        $total_cooler_deposit_amount = 0;

        $total_cup_quantity = 0;
        $total_cup_deposit_amount = 0;

        $total_plastic_quantity = 0;
        $total_plastic_deposit_amount = 0;

        $asset_dynamic_history = array();

        $assetSerials = $this->em->getRepository('App:AssetSerials')->findBy(['customer' => $customer]);
        $assetSerialsResponse = [];

        if ($assetSerials) {
            foreach ($assetSerials as $assetSerial) {
                $assetSerialsResponse[] = $this->parseAssetSerialObject($assetSerial);
            }
        }

        if (!empty($customerAssets)) {
            foreach ($customerAssets as $item) {

                /* for dynamic assets */

                $asset_dynamic_history[$item->getAsset()->getId()]['assets_name'] = $item->getAsset()->getName();
                $asset_dynamic_history[$item->getAsset()->getId()]['assets_id'] = $item->getAsset()->getId();
                if (!isset($asset_dynamic_history[$item->getAsset()->getId()]['quantity'])) {
                    $quantity = 0;
                } else {
                    $quantity = $asset_dynamic_history[$item->getAsset()->getId()]['quantity'];
                }
                if (!isset($asset_dynamic_history[$item->getAsset()->getId()]['deposit_amount'])) {
                    $deposit_amount = 0;
                } else {
                    $deposit_amount = $asset_dynamic_history[$item->getAsset()->getId()]['deposit_amount'];
                }
                $asset_dynamic_history[$item->getAsset()->getId()]['quantity'] = $quantity + $item->getQuantity();
                $asset_dynamic_history[$item->getAsset()->getId()]['deposit_amount'] = $deposit_amount + $item->getDepositAmount();
                if ($item->getAsset()->getId() == 2) {
                    $asset_dynamic_history[$item->getAsset()->getId()]['assets_serials'] = $assetSerialsResponse;
                }

                /* */

                if ($item->getAsset()->getId() == 1) {
                    $total_empties_quantity += $item->getQuantity();
                    $total_empties_deposit_amount += $item->getDepositAmount();
                } else if ($item->getAsset()->getId() == 2) {
                    $total_cooler_quantity += $item->getQuantity();
                    $total_cooler_deposit_amount += $item->getDepositAmount();
                } else if ($item->getAsset()->getId() == 3) {
                    $total_cup_quantity += $item->getQuantity();
                    $total_cup_deposit_amount += $item->getDepositAmount();
                } else if ($item->getAsset()->getId() == 4) {
                    $total_plastic_quantity += $item->getQuantity();
                    $total_plastic_deposit_amount += $item->getDepositAmount();
                }
            }
        }

        $parseAssetDynamicHistory = [];
        foreach ($asset_dynamic_history as $value) {
            $parseAssetDynamicHistory[] = $value;
        }

        $userResponse = (object)[
            'empties' => array('quantity' => $total_empties_quantity, 'deposit_amount' => $total_empties_deposit_amount),
            'cooler_dispenser' => array('quantity' => $total_cooler_quantity, 'deposit_amount' => $total_cooler_deposit_amount),
            'cup_holder' => array('quantity' => $total_cup_quantity, 'deposit_amount' => $total_cup_deposit_amount),
            'plastic_dispenser' => array('quantity' => $total_plastic_quantity, 'deposit_amount' => $total_plastic_deposit_amount),
            'asset_serials' => $assetSerialsResponse,
            'assets_dynamic_history' => $parseAssetDynamicHistory
        ];

        return $userResponse;
    }

    public function parseCouponCodesToCustomerObjects($couponCodes, Request $request)
    {
        $userResponse = [];
        if ($couponCodes) {
            foreach ($couponCodes as $codes) {
                $userResponse[] = $this->parseCouponCodeObject($codes, $request);
            }
        }
        return $userResponse;
    }

    public function parseCouponCodeObject(CouponCodes $codes, $request)
    {


        $orderBooks = $this->em->getRepository('App:OrderBooks')->findOneBy(['book' => $codes->getBook()]);

        $customer_name = '';
        if ($codes->getIsRedeamed() == 1) {
            $customer_name = $codes->getCustomer()->getName();
        } elseif ($codes->getBook()->getIsSold() == 1 and $codes->getBook()->getCustomer()) {
            $customer_name = $codes->getBook()->getCustomer()->getName();
        }
        $userResponse = (Object)[
            ConstantsConroller::DEFAULT_ID_INDEX => $codes->getId(),
            'book_id' => $codes->getBook()->getId(),
            'book_name' => $codes->getBook()->getName(),
            'book_worth_off' => $codes->getBook()->getWorthOff(),
            'book_worth_off_tax' => (double)$codes->getBook()->getWorthOffTax(),
            'book_worth_off_with_tax' => (double)$codes->getBook()->getWorthOffAfterTax(),
            'order_id' => $orderBooks ? $orderBooks->getOrder()->getId() : '',
            ConstantsConroller::DEFAULT_LEAFLET_CODE => $codes->getCode(),
            'is_redeamed' => $codes->getIsRedeamed(),
            'redeemed_on' => $codes->getRedeamedOn() ? $this->parseDate($codes->getRedeamedOn()) : (object)[],
            'customer_name' => $customer_name,
        ];
        return $userResponse;

    }

    public function parseLoadOutArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseLoadOutItemsArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                    'quantity' => $req->getQuantity(),
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseLoadOutAssetsArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                    'quantity' => $req->getQuantity(),
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseLoadOutBooksArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $couponCodes = $this->em->getRepository('App:CouponCodes')->findBy(['book' => $req->getBook()]);

                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                    'book_id' => $req->getBook()->getPublicId(),
                    'book_name' => $req->getBook()->getName(),
                    'book_worth_off' => $req->getBook()->getWorthOff(),
                    'coupon_codes' => $this->parseCouponCodesToCustomerObjects($couponCodes, $request),
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseOrdersBooksArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = $this->parseOrderObjectByObjectForDriver($req, $request);
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseOrderItemsArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = $this->parseOrderItemObjectByObjectForDriver($req, $request);
                $retResponse[] = $userResponse;
            }
        }

        return $retResponse;
    }

    public function parseOrderBooksArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseInvoicesArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseTransactionsArrayOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                    ConstantsConroller::AMOUNT => $req->getAmount(),
                    ConstantsConroller::CHECK_NUMBER => $req->getCheckNumber(),
                    ConstantsConroller::PAYMENT_TYPE => $req->getType(),
                    //ConstantsConroller::CUSTOMER => $req->getCustomer() ? $this->parseCustomerObjectByObjectForDriver($req->getCustomer(),$request) : (object)[],
                    ConstantsConroller::CREATED_ON => $req->getCreatedOn() ? $this->parseDate($req->getCreatedOn()) : (object)[],
                    ConstantsConroller::CREATED_BY => $req->getReceavedBy() ? $this->parseUserObjectByObjectForDriver($req->getReceavedBy(), $request) : (object)[]
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parseAssetsIssuedTocustomersOfObjectForDriver($reqData, $request)
    {
        $retResponse = [];

        if ($reqData) {
            foreach ($reqData as $req) {
                $userResponse = (object)[
                    ConstantsConroller::DEFAULT_ID_INDEX => $req->getId(),
                    'asset_name' => $req->getAsset()->getName(),
                    'assetbaseprice' => (double)$req->getAsset()->getUnitPrice(),
                    'assetbasepricewithtax' => (double)$req->getAsset()->getAssetPriceAfterTax(),
                    'assetbasepricetax' => (double)$req->getAsset()->getAssetTaxPrice(),
                    'assetamount' => (double)$req->getDepositAmount(),
                    'assetamounttax' => (double)$req->getTaxPrice(),
                    'assetamountwithtax' => (double)$req->getPriceAfterTax(),
                    'customer_name' => $req->getCustomer()->getName(),
                    'quantity' => $req->getQuantity(),
                    'status' => $req->getStatus(),
                    'type' => $req->getType(),
                    'deposit_amount' => $req->getDepositAmount(),
                    'is_temp' => $req->getisTemp(),
                    'no_of_days' => $req->getNoOfDays(),
                    ConstantsConroller::CREATED_ON => $req->getCreatedOn() ? $this->parseDate($req->getCreatedOn()) : (object)[],
                    'delivery_date' => $req->getDeliveryDate() ? $this->parseDate($req->getDeliveryDate()) : (object)[],
                ];
                $retResponse[] = $userResponse;
            }
        }
        return $retResponse;
    }

    public function parsePromotionConsumer($promotions = array(), $request)
    {
        $result = array();
        foreach ($promotions as $promo) {
            $result[] = array(
                'id' => $promo->getId(),
                'title' => $promo->getTitle(),
                'description' => $promo->getDescription(),
                'image' => ($promo->getImage()) ? $promo->getImage()->getPath() : '',
                'product' => ($promo->getProduct()) ? $this->parseProductsObjectForDriver($promo->getProduct(), $request) : [],
                'promotion_price' => ($promo->getPromotionPrice()) ? $promo->getPromotionPrice() : 0,
                'time_in_milli_seconds' => ($promo->getTimeInSeconds()) ? $promo->getTimeInSeconds() * 1000 : 0
            );
        }
        return $result;
    }
}
