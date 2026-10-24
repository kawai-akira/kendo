<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月25日作成
 *
 * app/Customize/Controller/HelpController.php
 *
 *  
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends \Eccube\Controller\HelpController
{
    /**
     * HelpController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Q&a
     *
     * @Route("/help/faq", name="help_faq", methods={"GET"})
     * @Template("Help/faq.twig")
     */
    public function faq()
    {
        return [];
    }

    /**
     * 剣道具について
     *
     * @Route("/help/about_tools", name="help_about_tools", methods={"GET"})
     * @Template("Help/about_tools.twig")
     */
    public function about_tools(){

        return [];
    }   
   

    
   /**
     * Q&a
     *
     * @Route("/groupbuy", name="groupbuy", methods={"GET"})
     * @Template("Help/groupbuy.twig")
     */
    public function groupbuy()
    {
        return [];
    }

    }