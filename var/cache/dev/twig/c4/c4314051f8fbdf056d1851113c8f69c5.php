<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* meta.twig */
class __TwigTemplate_665c55155e17aa982014697edc072537 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "meta.twig"));

        $__eccube__gblobal = $this->env->getGlobals();
        $__eccube__eventDispatcher = $__eccube__gblobal['event_dispatcher'];
        $__eccube__source = $this->env->getLoader()->getSourceContext($this->getTemplateName())->getCode();
        $__eccube__event = new \Eccube\Event\TemplateEvent($this->getTemplateName(), $__eccube__source, $context);
        $__eccube__eventDispatcher->dispatch($__eccube__event, $this->getTemplateName());
        $context = $__eccube__event->getParameters();
        if ($__eccube__event->getSource() !== $__eccube__source) {
            $__eccube__newTemplate = $this->env->createTemplate($__eccube__event->getSource());
            $__eccube__newTemplate->display($__eccube__event->getParameters());
            return;
        }

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "meta.twig"));

        // line 1
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 1, $this->source); })()), "request", [], "any", false, false, true, 1), "get", ["_route"], "method", false, false, true, 1) == "product_detail")) {
            // line 2
            echo "    ";
            $context["meta_og_type"] = "og:product";
            // line 3
            echo "    ";
            $context["meta_description"] = _twig_default_filter(((twig_get_attribute($this->env, $this->source, ($context["Product"] ?? null), "description_list", [], "any", true, true, true, 3)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["Product"] ?? null), "description_list", [], "any", false, false, true, 3), 3, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 3, $this->source); })()), "description_detail", [], "any", false, false, true, 3), 3, $this->source))) : (twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 3, $this->source); })()), "description_detail", [], "any", false, false, true, 3))), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Page"]) || array_key_exists("Page", $context) ? $context["Page"] : (function () { throw new RuntimeError('Variable "Page" does not exist.', 3, $this->source); })()), "description", [], "any", false, false, true, 3), 3, $this->source));
            // line 4
            echo "    ";
            $context["meta_canonical"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl("product_detail", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 4, $this->source); })()), "id", [], "any", false, false, true, 4)]);
            // line 5
            echo "    <meta property=\"og:title\" content=\"";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 5, $this->source); })()), "name", [], "any", false, false, true, 5), 5, $this->source), "html", null, true);
            echo "\" />
    <meta property=\"og:image\" content=\"";
            // line 6
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\HttpFoundationExtension']->generateAbsoluteUrl($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl($this->extensions['Eccube\Twig\Extension\EccubeExtension']->getNoImageProduct($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 6, $this->source); })()), "main_list_image", [], "any", false, false, true, 6), 6, $this->source)), "save_image")), "html", null, true);
            echo "\" />
    <meta property=\"product:price:amount\" content=\"";
            // line 7
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 7, $this->source); })()), "getPrice02IncTaxMin", [], "any", false, false, true, 7), 7, $this->source), "html", null, true);
            echo "\"/>
    <meta property=\"product:price:currency\" content=\"";
            // line 8
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["eccube_config"]) || array_key_exists("eccube_config", $context) ? $context["eccube_config"] : (function () { throw new RuntimeError('Variable "eccube_config" does not exist.', 8, $this->source); })()), "currency", [], "any", false, false, true, 8), 8, $this->source), "html", null, true);
            echo "\"/>
    <meta property=\"product:product_link\" content=\"";
            // line 9
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl("product_detail", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 9, $this->source); })()), "id", [], "any", false, false, true, 9)]), "html", null, true);
            echo "\"/>
    <meta property=\"product:retailer_title\" content=\"";
            // line 10
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["BaseInfo"]) || array_key_exists("BaseInfo", $context) ? $context["BaseInfo"] : (function () { throw new RuntimeError('Variable "BaseInfo" does not exist.', 10, $this->source); })()), "shop_name", [], "any", false, false, true, 10), 10, $this->source), "html", null, true);
            echo "\"/>
    ";
            // line 11
            if ( !twig_get_attribute($this->env, $this->source, (isset($context["Product"]) || array_key_exists("Product", $context) ? $context["Product"] : (function () { throw new RuntimeError('Variable "Product" does not exist.', 11, $this->source); })()), "stock_find", [], "any", false, false, true, 11)) {
                // line 12
                echo "        <meta name=\"robots\" content=\"noindex\">
    ";
            }
        } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 14
(isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 14, $this->source); })()), "request", [], "any", false, false, true, 14), "get", ["_route"], "method", false, false, true, 14) == "product_list")) {
            // line 15
            echo "    ";
            $context["meta_canonical"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl("product_list", ["category_id" => ((twig_get_attribute($this->env, $this->source, ($context["Category"] ?? null), "id", [], "any", true, true, true, 15)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["Category"] ?? null), "id", [], "any", false, false, true, 15), 15, $this->source), null)) : (null))]);
            // line 16
            echo "    ";
            if ((twig_length_filter($this->env, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 16, $this->source); })())) == 0)) {
                // line 17
                echo "        <meta name=\"robots\" content=\"noindex\">
    ";
            }
        } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 19
(isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 19, $this->source); })()), "request", [], "any", false, false, true, 19), "get", ["_route"], "method", false, false, true, 19) == "homepage")) {
            // line 20
            echo "    ";
            $context["meta_og_type"] = "website";
            // line 21
            echo "    ";
            $context["meta_canonical"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl("homepage");
        } elseif (((        // line 22
array_key_exists("Page", $context) && (twig_get_attribute($this->env, $this->source, (isset($context["Page"]) || array_key_exists("Page", $context) ? $context["Page"] : (function () { throw new RuntimeError('Variable "Page" does not exist.', 22, $this->source); })()), "edit_type", [], "any", false, false, true, 22) == 0)) && twig_get_attribute($this->env, $this->source, ($context["Page"] ?? null), "url", [], "any", true, true, true, 22))) {
            // line 23
            echo "    ";
            $context["meta_canonical"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["eccube_config"]) || array_key_exists("eccube_config", $context) ? $context["eccube_config"] : (function () { throw new RuntimeError('Variable "eccube_config" does not exist.', 23, $this->source); })()), "eccube_user_data_route", [], "any", false, false, true, 23), 23, $this->source), ["route" => twig_get_attribute($this->env, $this->source, (isset($context["Page"]) || array_key_exists("Page", $context) ? $context["Page"] : (function () { throw new RuntimeError('Variable "Page" does not exist.', 23, $this->source); })()), "url", [], "any", false, false, true, 23)]);
        }
        // line 25
        echo "
<meta property=\"og:type\" content=\"";
        // line 26
        echo twig_escape_filter($this->env, ((array_key_exists("meta_og_type", $context)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed((isset($context["meta_og_type"]) || array_key_exists("meta_og_type", $context) ? $context["meta_og_type"] : (function () { throw new RuntimeError('Variable "meta_og_type" does not exist.', 26, $this->source); })()), 26, $this->source), "article")) : ("article")), "html", null, true);
        echo "\"/>
<meta property=\"og:site_name\" content=\"";
        // line 27
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["BaseInfo"]) || array_key_exists("BaseInfo", $context) ? $context["BaseInfo"] : (function () { throw new RuntimeError('Variable "BaseInfo" does not exist.', 27, $this->source); })()), "shop_name", [], "any", false, false, true, 27), 27, $this->source), "html", null, true);
        echo "\"/>
";
        // line 28
        $context["meta_description"] = ((array_key_exists("meta_description", $context)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed((isset($context["meta_description"]) || array_key_exists("meta_description", $context) ? $context["meta_description"] : (function () { throw new RuntimeError('Variable "meta_description" does not exist.', 28, $this->source); })()), 28, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Page"]) || array_key_exists("Page", $context) ? $context["Page"] : (function () { throw new RuntimeError('Variable "Page" does not exist.', 28, $this->source); })()), "description", [], "any", false, false, true, 28), 28, $this->source))) : (twig_get_attribute($this->env, $this->source, (isset($context["Page"]) || array_key_exists("Page", $context) ? $context["Page"] : (function () { throw new RuntimeError('Variable "Page" does not exist.', 28, $this->source); })()), "description", [], "any", false, false, true, 28)));
        // line 29
        if ((isset($context["meta_description"]) || array_key_exists("meta_description", $context) ? $context["meta_description"] : (function () { throw new RuntimeError('Variable "meta_description" does not exist.', 29, $this->source); })())) {
            // line 30
            echo "    <meta name=\"description\" content=\"";
            echo twig_escape_filter($this->env, twig_slice($this->env, twig_striptags($this->sandbox->ensureToStringAllowed((isset($context["meta_description"]) || array_key_exists("meta_description", $context) ? $context["meta_description"] : (function () { throw new RuntimeError('Variable "meta_description" does not exist.', 30, $this->source); })()), 30, $this->source)), 0, 120), "html", null, true);
            echo "\">
    <meta property=\"og:description\" content=\"";
            // line 31
            echo twig_escape_filter($this->env, twig_slice($this->env, twig_striptags($this->sandbox->ensureToStringAllowed((isset($context["meta_description"]) || array_key_exists("meta_description", $context) ? $context["meta_description"] : (function () { throw new RuntimeError('Variable "meta_description" does not exist.', 31, $this->source); })()), 31, $this->source)), 0, 120), "html", null, true);
            echo "\"/>
";
        }
        // line 33
        if (((array_key_exists("meta_canonical", $context)) ? (_twig_default_filter((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 33, $this->source); })()))) : (""))) {
            // line 34
            echo "    ";
            // line 35
            echo "    ";
            // line 36
            echo "    ";
            if ((array_key_exists("Category", $context) && (isset($context["Category"]) || array_key_exists("Category", $context) ? $context["Category"] : (function () { throw new RuntimeError('Variable "Category" does not exist.', 36, $this->source); })()))) {
                // line 37
                echo "        ";
                if (((array_key_exists("pagination", $context) && twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "paginationData", [], "any", true, true, true, 37)) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 37, $this->source); })()), "paginationData", [], "any", false, false, true, 37), "pageCount", [], "any", false, false, true, 37) > 1))) {
                    // line 38
                    echo "            ";
                    // line 39
                    echo "            ";
                    if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 39, $this->source); })()), "paginationData", [], "any", false, false, true, 39), "current", [], "any", false, false, true, 39) == 1)) {
                        // line 40
                        echo "            <meta property=\"og:url\" content=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 40, $this->source); })()), 40, $this->source), "html", null, true);
                        echo "\"/>
            <link rel=\"next\" href=\"";
                        // line 41
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 41, $this->source); })()), 41, $this->source), "html", null, true);
                        echo "&pageno=2\" title=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Category"]) || array_key_exists("Category", $context) ? $context["Category"] : (function () { throw new RuntimeError('Variable "Category" does not exist.', 41, $this->source); })()), "name", [], "any", false, false, true, 41), 41, $this->source), "html", null, true);
                        echo " ";
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("%pageno%ページ目", ["%pageno%" => 2]), "html", null, true);
                        echo "\">
            <link rel=\"canonical\" href=\"";
                        // line 42
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 42, $this->source); })()), 42, $this->source), "html", null, true);
                        echo "\" />
            ";
                        // line 44
                        echo "            ";
                    } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 44, $this->source); })()), "paginationData", [], "any", false, false, true, 44), "last", [], "any", false, false, true, 44) == twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 44, $this->source); })()), "paginationData", [], "any", false, false, true, 44), "current", [], "any", false, false, true, 44))) {
                        // line 45
                        echo "            <meta property=\"og:url\" content=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 45, $this->source); })()), 45, $this->source), "html", null, true);
                        echo "&pageno=";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 45, $this->source); })()), "paginationData", [], "any", false, false, true, 45), "last", [], "any", false, false, true, 45), 45, $this->source), "html", null, true);
                        echo "\"/>
            <link rel=\"prev\" href=\"";
                        // line 46
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 46, $this->source); })()), 46, $this->source), "html", null, true);
                        echo "&pageno=";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 46, $this->source); })()), "paginationData", [], "any", false, false, true, 46), "previous", [], "any", false, false, true, 46), 46, $this->source), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Category"]) || array_key_exists("Category", $context) ? $context["Category"] : (function () { throw new RuntimeError('Variable "Category" does not exist.', 46, $this->source); })()), "name", [], "any", false, false, true, 46), 46, $this->source), "html", null, true);
                        echo " ";
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("%pageno%ページ目", ["%pageno%" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 46, $this->source); })()), "paginationData", [], "any", false, false, true, 46), "previous", [], "any", false, false, true, 46)]), "html", null, true);
                        echo "\">
            <link rel=\"canonical\" href=\"";
                        // line 47
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 47, $this->source); })()), 47, $this->source), "html", null, true);
                        echo "&pageno=";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 47, $this->source); })()), "paginationData", [], "any", false, false, true, 47), "last", [], "any", false, false, true, 47), 47, $this->source), "html", null, true);
                        echo "\" />
            ";
                        // line 49
                        echo "            ";
                    } else {
                        // line 50
                        echo "            <meta property=\"og:url\" content=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 50, $this->source); })()), 50, $this->source), "html", null, true);
                        echo "&pageno=";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 50, $this->source); })()), "paginationData", [], "any", false, false, true, 50), "current", [], "any", false, false, true, 50), 50, $this->source), "html", null, true);
                        echo "\"/>
            <link rel=\"next\" href=\"";
                        // line 51
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 51, $this->source); })()), 51, $this->source), "html", null, true);
                        echo "&pageno=";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 51, $this->source); })()), "paginationData", [], "any", false, false, true, 51), "next", [], "any", false, false, true, 51), 51, $this->source), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Category"]) || array_key_exists("Category", $context) ? $context["Category"] : (function () { throw new RuntimeError('Variable "Category" does not exist.', 51, $this->source); })()), "name", [], "any", false, false, true, 51), 51, $this->source), "html", null, true);
                        echo " ";
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("%pageno%ページ目", ["%pageno%" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 51, $this->source); })()), "paginationData", [], "any", false, false, true, 51), "next", [], "any", false, false, true, 51)]), "html", null, true);
                        echo "\">
            <link rel=\"prev\" href=\"";
                        // line 52
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 52, $this->source); })()), 52, $this->source), "html", null, true);
                        echo "&pageno=";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 52, $this->source); })()), "paginationData", [], "any", false, false, true, 52), "previous", [], "any", false, false, true, 52), 52, $this->source), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["Category"]) || array_key_exists("Category", $context) ? $context["Category"] : (function () { throw new RuntimeError('Variable "Category" does not exist.', 52, $this->source); })()), "name", [], "any", false, false, true, 52), 52, $this->source), "html", null, true);
                        echo " ";
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("%pageno%ページ目", ["%pageno%" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 52, $this->source); })()), "paginationData", [], "any", false, false, true, 52), "previous", [], "any", false, false, true, 52)]), "html", null, true);
                        echo "\">
            <link rel=\"canonical\" href=\"";
                        // line 53
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 53, $this->source); })()), 53, $this->source), "html", null, true);
                        echo "&pageno=";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["pagination"]) || array_key_exists("pagination", $context) ? $context["pagination"] : (function () { throw new RuntimeError('Variable "pagination" does not exist.', 53, $this->source); })()), "paginationData", [], "any", false, false, true, 53), "current", [], "any", false, false, true, 53), 53, $this->source), "html", null, true);
                        echo "\" />
            ";
                    }
                    // line 55
                    echo "        ";
                } else {
                    // line 56
                    echo "        ";
                    // line 57
                    echo "        <meta property=\"og:url\" content=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 57, $this->source); })()), 57, $this->source), "html", null, true);
                    echo "\"/>
        <link rel=\"canonical\" href=\"";
                    // line 58
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 58, $this->source); })()), 58, $this->source), "html", null, true);
                    echo "\" />
        ";
                }
                // line 60
                echo "    ";
                // line 61
                echo "    ";
            } else {
                // line 62
                echo "    <meta property=\"og:url\" content=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 62, $this->source); })()), 62, $this->source), "html", null, true);
                echo "\"/>
    <link rel=\"canonical\" href=\"";
                // line 63
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["meta_canonical"]) || array_key_exists("meta_canonical", $context) ? $context["meta_canonical"] : (function () { throw new RuntimeError('Variable "meta_canonical" does not exist.', 63, $this->source); })()), 63, $this->source), "html", null, true);
                echo "\" />
    ";
            }
        }
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "meta.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  272 => 63,  267 => 62,  264 => 61,  262 => 60,  257 => 58,  252 => 57,  250 => 56,  247 => 55,  240 => 53,  230 => 52,  220 => 51,  213 => 50,  210 => 49,  204 => 47,  194 => 46,  187 => 45,  184 => 44,  180 => 42,  172 => 41,  167 => 40,  164 => 39,  162 => 38,  159 => 37,  156 => 36,  154 => 35,  152 => 34,  150 => 33,  145 => 31,  140 => 30,  138 => 29,  136 => 28,  132 => 27,  128 => 26,  125 => 25,  121 => 23,  119 => 22,  116 => 21,  113 => 20,  111 => 19,  107 => 17,  104 => 16,  101 => 15,  99 => 14,  95 => 12,  93 => 11,  89 => 10,  85 => 9,  81 => 8,  77 => 7,  73 => 6,  68 => 5,  65 => 4,  62 => 3,  59 => 2,  57 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% if app.request.get('_route') == 'product_detail' %}
    {% set meta_og_type = \"og:product\" %}
    {% set meta_description = Product.description_list | default(Product.description_detail) | default(Page.description) %}
    {% set meta_canonical = url('product_detail', {'id': Product.id}) %}
    <meta property=\"og:title\" content=\"{{ Product.name }}\" />
    <meta property=\"og:image\" content=\"{{ absolute_url(asset(Product.main_list_image|no_image_product, 'save_image')) }}\" />
    <meta property=\"product:price:amount\" content=\"{{ Product.getPrice02IncTaxMin }}\"/>
    <meta property=\"product:price:currency\" content=\"{{ eccube_config.currency }}\"/>
    <meta property=\"product:product_link\" content=\"{{ url('product_detail', {'id': Product.id}) }}\"/>
    <meta property=\"product:retailer_title\" content=\"{{ BaseInfo.shop_name }}\"/>
    {% if not Product.stock_find %}
        <meta name=\"robots\" content=\"noindex\">
    {% endif %}
{% elseif app.request.get('_route') == 'product_list' %}
    {% set meta_canonical = url('product_list', {'category_id': Category.id|default(null)}) %}
    {% if pagination|length == 0 %}
        <meta name=\"robots\" content=\"noindex\">
    {% endif %}
{% elseif app.request.get('_route') == 'homepage' %}
    {% set meta_og_type = 'website' %}
    {% set meta_canonical = url('homepage') %}
{% elseif Page is defined and Page.edit_type == 0 and Page.url is defined %}
    {% set meta_canonical = url(eccube_config.eccube_user_data_route, {'route': Page.url}) %}
{% endif %}

<meta property=\"og:type\" content=\"{{ meta_og_type|default('article') }}\"/>
<meta property=\"og:site_name\" content=\"{{ BaseInfo.shop_name }}\"/>
{% set meta_description = meta_description | default(Page.description) %}
{% if meta_description %}
    <meta name=\"description\" content=\"{{ meta_description|striptags|slice(0,120) }}\">
    <meta property=\"og:description\" content=\"{{ meta_description|striptags|slice(0,120) }}\"/>
{% endif %}
{% if meta_canonical|default() %}
    {# canonical url #}
    {# Product list #}
    {% if Category is defined and Category %}
        {% if pagination is defined and pagination.paginationData is defined and pagination.paginationData.pageCount > 1 %}
            {# 最初のページ #}
            {% if pagination.paginationData.current == 1  %}
            <meta property=\"og:url\" content=\"{{ meta_canonical }}\"/>
            <link rel=\"next\" href=\"{{ meta_canonical }}&pageno=2\" title=\"{{ Category.name }} {{ '%pageno%ページ目'|trans({ '%pageno%': 2 }) }}\">
            <link rel=\"canonical\" href=\"{{ meta_canonical }}\" />
            {# 最後のページ #}
            {% elseif pagination.paginationData.last == pagination.paginationData.current %}
            <meta property=\"og:url\" content=\"{{ meta_canonical }}&pageno={{ pagination.paginationData.last }}\"/>
            <link rel=\"prev\" href=\"{{ meta_canonical }}&pageno={{ pagination.paginationData.previous }}\" title=\"{{ Category.name }} {{ '%pageno%ページ目'|trans({ '%pageno%': pagination.paginationData.previous }) }}\">
            <link rel=\"canonical\" href=\"{{ meta_canonical }}&pageno={{ pagination.paginationData.last }}\" />
            {# 途中のページ #}
            {% else %}
            <meta property=\"og:url\" content=\"{{ meta_canonical }}&pageno={{ pagination.paginationData.current }}\"/>
            <link rel=\"next\" href=\"{{ meta_canonical }}&pageno={{ pagination.paginationData.next }}\" title=\"{{ Category.name }} {{ '%pageno%ページ目'|trans({ '%pageno%': pagination.paginationData.next }) }}\">
            <link rel=\"prev\" href=\"{{ meta_canonical }}&pageno={{ pagination.paginationData.previous }}\" title=\"{{ Category.name }} {{ '%pageno%ページ目'|trans({ '%pageno%': pagination.paginationData.previous }) }}\">
            <link rel=\"canonical\" href=\"{{ meta_canonical }}&pageno={{ pagination.paginationData.current }}\" />
            {% endif %}
        {% else %}
        {# ページング無し #}
        <meta property=\"og:url\" content=\"{{ meta_canonical }}\"/>
        <link rel=\"canonical\" href=\"{{ meta_canonical }}\" />
        {% endif %}
    {# Other #}
    {% else %}
    <meta property=\"og:url\" content=\"{{ meta_canonical }}\"/>
    <link rel=\"canonical\" href=\"{{ meta_canonical }}\" />
    {% endif %}
{% endif %}
", "meta.twig", "D:\\htdocs82\\kendo\\www\\src\\Eccube\\Resource\\template\\default\\meta.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 1, "set" => 2);
        static $filters = array("default" => 3, "escape" => 5, "no_image_product" => 6, "length" => 16, "slice" => 30, "striptags" => 30, "trans" => 41);
        static $functions = array("url" => 4, "absolute_url" => 6, "asset" => 6);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set'],
                ['default', 'escape', 'no_image_product', 'length', 'slice', 'striptags', 'trans'],
                ['url', 'absolute_url', 'asset']
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
