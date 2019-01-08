<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class AppExtension extends \Twig_Extension
{
    private $container;

    function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('getParameter', array($this, 'getParameterFilter')),
            new \Twig_SimpleFilter('url_decode', array($this, 'urlDecodeFilter')),
            new \Twig_SimpleFilter('getCompanyFilter', array($this, 'getCompanyFilter')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getCompany', array($this, 'getCompanyFunction')),
            new \Twig_SimpleFunction('getDashBoardProductReport', array($this, 'getDashBoardProductReportFunction')),
            new \Twig_SimpleFunction('jsonDecode', array($this, 'jsonDecode')),
            new \Twig_SimpleFunction('calculateDistance', array($this, 'calculateDistanceFunction')),
            new \Twig_SimpleFunction('convertUnderscoreStrToSpace', array($this, 'convertUnderscoreStrToSpace'))
        );
    }

    public function jsonDecode($param)
    {
        return json_decode($param, true);
    }

    public function convertUnderscoreStrToSpace($param)
    {
        $str = '';

        $info = explode('_', $param);
        if ($info) {
            foreach ($info as $row) {
                $str .= ucwords($row)." ";
            }
        } else {
            $str = $param;
        }
        return rtrim($str);
    }

    public function getParameterFilter($param)
    {
        return $this->container->getParameter($param);
    }

    public function urlDecodeFilter($param)
    {
        return urldecode($param);
    }

    public function calculateDistanceFunction($lat1, $lon1, $lat2, $lon2, $unit='K')
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return round(($miles * 1.609344),2);
        } else if ($unit == "N") {
            return round(($miles * 0.8684),2);
        } else {
            return round($miles,2);
        }

    }

    public function getCompanyFunction()
    {
        return $this->container->get('doctrine.orm.entity_manager')->getRepository('App:Company')->find(1);
    }

    public function getDashBoardProductReportFunction($date_from,$date_to)
    {
        $date_from = new \DateTime($date_from);
        $date_to = new \DateTime($date_to);
//        var_dump($date_from);
//        var_dump($date_to);die;
        $allProducts = $this->container->get('doctrine.orm.entity_manager')->getRepository('App:Products')->findAll();

        $products = [];

        foreach ($allProducts as $p) {
            $products[$p->getId()] = [
                'name' => $p->getName(),
                'id' => $p->getId(),
                'Cash' => 0,
                'sold_charged'=>0,
                'sold' => 0,
                'Company Credit' => 0,
                'Coupon Book' => 0,
                'Personal Credit' => 0,
                'Redeem Coupon Book' => 0,
            ];
        }

        $em = $this->container->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();
        $qb->select('o')
            ->from('App:Orders', 'o')
            ->orderBy("o.id", 'DESC');

        if ($date_from && $date_to) {
            $qb->andwhere('DATE(o.createdOn) >= DATE(:start) AND DATE(o.createdOn) <= DATE(:end)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        $orders = $qb->getQuery()->getResult();
        $orderItems = $em->getRepository('App:OrderItems')->findBy(['order' => $orders]);
        /* calculate disc amount against product and customer category */
        $qb = $em->createQueryBuilder();
        $qb->select("identity(oi.product) as product_id,SUM(oi.amountAfterDiscount) as total_disc_amount,cc.name,concat(d.username,' | ',d.salesmanName,' | ',d.firstName) as driver")
            ->from('App:OrderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('o.deliverBy', 'd')
            ->innerJoin('o.customer', 'c')
            ->innerJoin('c.customerCategory', 'cc')
            ->groupBy("oi.product")
            ->addGroupBy('cc.name')
            ->addGroupBy('o.deliverBy')
            ->where('cc.id <> 5');

        if ($date_from && $date_to) {
            $qb->andwhere('DATE(oi.createdOn) >= DATE(:start) AND DATE(oi.createdOn) <= DATE(:end)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        $discTotalsAgainstTerm = $qb->getQuery()->getResult();
        /* */

        /* calculate transaction amount by products */
        $qb = $em->createQueryBuilder();
        $qb->select("identity(oi.product) as product_id,SUM(t.amount) as total_trans_amount,cc.name,concat(d.username,' | ',d.salesmanName,' | ',d.firstName) as driver")
            ->from('App:OrderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('App:Transactions', 't','WITH','t.order=o.id')
            ->innerJoin('o.deliverBy', 'd')
            ->innerJoin('o.customer', 'c')
            ->innerJoin('c.customerCategory', 'cc')
            ->groupBy("oi.product")
            ->addGroupBy('cc.name')
            ->addGroupBy('o.deliverBy')
            ->where('cc.id <> 5');

        if ($date_from && $date_to) {
            $qb->andwhere('DATE(oi.createdOn) >= DATE(:start) AND DATE(oi.createdOn) <= DATE(:end)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        $transTotalsAgainstTerm = $qb->getQuery()->getResult();

        /* calculate disc amount against product and customer category sale coupon */
        $qb = $em->createQueryBuilder();
        $qb->select("'1' as product_id,SUM(b.price) as total_disc_amount,'Coupon Book' as name,concat(d.username,' | ',d.salesmanName,' | ',d.firstName) as driver")
            ->from('App:OrderBooks', 'ob')
            ->innerJoin('ob.book', 'b')
            ->innerJoin('ob.order', 'o')
            ->innerJoin('o.deliverBy', 'd')
            ->innerJoin('o.customer', 'c')
            ->innerJoin('c.customerCategory', 'cc')
            ->addGroupBy('o.deliverBy');

        if ($date_from && $date_to) {
            $qb->andwhere('DATE(b.createdOn) >= DATE(:start) AND DATE(b.createdOn) <= DATE(:end)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        $discTotalsAgainstCoupon = $qb->getQuery()->getResult();
        /* */


        /* calculate disc amount against product and customer category redeem coupon */
        $qb = $em->createQueryBuilder();
        $qb->select("'1' as product_id,SUM(cc.worthOff) as total_disc_amount,'Redeem Coupon Book' as name,concat(d.username,' | ',d.salesmanName,' | ',d.firstName) as driver")
            ->from('App:CouponCodes', 'cc')
            ->innerJoin('cc.book', 'b')
            ->innerJoin('cc.driver', 'd')
            ->innerJoin('cc.customer', 'c')
            ->innerJoin('c.customerCategory', 'cuc')
            ->addGroupBy('cc.driver');

        if ($date_from && $date_to) {
            $qb->andwhere('DATE(cc.createdOn) >= DATE(:start) AND DATE(cc.createdOn) <= DATE(:end)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        $discTotalsAgainstRedeemCoupon = $qb->getQuery()->getResult();
        /* */

        foreach ($discTotalsAgainstTerm as $key => $value) {
            if (isset($products[$value['product_id']][$value['name']])) {
                $products[$value['product_id']][$value['name']] += $value['total_disc_amount'];
            }
            if (isset($products[$value['product_id']][$value['driver']][$value['name']])) {
                $products[$value['product_id']]['driver'][$value['driver']][$value['name']] += $value['total_disc_amount'];
            } else {
                $products[$value['product_id']]['driver'][$value['driver']][$value['name']] = $value['total_disc_amount'];
            }
            if (isset($products[$value['product_id']][$value['driver']]['Transaction'][$value['name']])) {
                $products[$value['product_id']]['driver'][$value['driver']]['Transaction'][$value['name']] += $value['total_disc_amount'];
            } else {
                $products[$value['product_id']]['driver'][$value['driver']]['Transaction'][$value['name']] = $value['total_disc_amount'];
            }
        }

        foreach ($transTotalsAgainstTerm as $key => $value) {
            if (isset($products[$value['product_id']]['Transaction'][$value['name']])) {
                $products[$value['product_id']]['Transaction'][$value['name']] += $value['total_trans_amount'];
            }else{
                $products[$value['product_id']]['Transaction'][$value['name']] = $value['total_trans_amount'];
            }
            if (isset($products[$value['product_id']][$value['driver']]['Transaction'][$value['name']])) {
                $products[$value['product_id']]['driver'][$value['driver']]['Transaction'][$value['name']] += $value['total_trans_amount'];
            } else {
                $products[$value['product_id']]['driver'][$value['driver']]['Transaction'][$value['name']] = $value['total_trans_amount'];
            }
        }
        foreach ($discTotalsAgainstRedeemCoupon as $key => $value) {
            $products[$value['product_id']][$value['name']] += $value['total_disc_amount'];
            if (isset($products[$value['product_id']][$value['driver']][$value['name']])) {
                $products[$value['product_id']]['driver'][$value['driver']][$value['name']] += $value['total_disc_amount'];
            } else {
                $products[$value['product_id']]['driver'][$value['driver']][$value['name']] = $value['total_disc_amount'];
            }
        }

        foreach ($discTotalsAgainstCoupon as $key => $value) {
            $products[$value['product_id']]["Coupon Book"] += $value['total_disc_amount'];
            if (isset($products[$value['product_id']][$value['driver']]["Coupon Book"])) {
                $products[$value['product_id']]['driver'][$value['driver']]["Coupon Book"] += $value['total_disc_amount'];
            } else {
                $products[$value['product_id']]['driver'][$value['driver']]["Coupon Book"] = $value['total_disc_amount'];
            }
        }

        foreach ($orderItems as $array) {

            if ($array->getOrder()->getOrderStatus() and $array->getOrder()->getOrderStatus()->getId() == 3) {
                $products[$array->getProduct()->getId()]['sold'] = $products[$array->getProduct()->getId()]['sold'] + $array->getQuantity();
                $products[$array->getProduct()->getId()]['sold_charged'] = $products[$array->getProduct()->getId()]['sold_charged'] + ($array->getAmountAfterDiscount());
            } else {
//                $products[$array->getProduct()->getId()]['not_delivered'] = $products[$array->getProduct()->getId()]['not_delivered'] + $array->getQuantity();
//                $products[$array->getProduct()->getId()]['not_delivered_charged'] = $products[$array->getProduct()->getId()]['not_delivered_charged'] + ($array->getAmountAfterDiscount());
            }
        }
        return $products;
//        var_dump($products);die;
//        return $this->container->get('doctrine.orm.entity_manager')->getRepository('App:Company')->find(1);
    }
}