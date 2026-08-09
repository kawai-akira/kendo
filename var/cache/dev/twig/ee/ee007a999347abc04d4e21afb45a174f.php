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

/* @admin/nav.twig */
class __TwigTemplate_bdb4b5baaf92545a924b436123f0c151 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@admin/nav.twig"));

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
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@admin/nav.twig"));

        // line 11
        echo "<nav>
    <ul class=\"c-mainNavArea__nav\">
        <!-- ホーム -->
        <li class=\"c-mainNavArea__navItem\">
            <a class=\"c-mainNavArea__navItemTitle\" href=\"";
        // line 15
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl("admin_homepage");
        echo "\">
                <i class=\"fa fa-home fa-fw\" aria-hidden=\"true\"></i>
                <span>";
        // line 17
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.home"), "html", null, true);
        echo "</span>
            </a>
        </li>
        ";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["eccubeNav"]) || array_key_exists("eccubeNav", $context) ? $context["eccubeNav"] : (function () { throw new RuntimeError('Variable "eccubeNav" does not exist.', 20, $this->source); })()));
        foreach ($context['_seq'] as $context["key1"] => $context["level1"]) {
            // line 21
            echo "            <li class=\"c-mainNavArea__navItem\">
                ";
            // line 22
            if ((twig_get_attribute($this->env, $this->source, $context["level1"], "children", [], "any", true, true, true, 22) && (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["level1"], "children", [], "any", false, false, true, 22)) > 0))) {
                // line 23
                echo "                    <a class=\"c-mainNavArea__navItemTitle";
                echo (((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus($this->sandbox->ensureToStringAllowed((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 23, $this->source); })()), 23, $this->source)), 0, [], "array", false, false, true, 23) != $context["key1"])) ? (" collapsed") : (""));
                echo "\"
                       data-bs-toggle=\"collapse\" href=\"#nav-";
                // line 24
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key1"], 24, $this->source), "html", null, true);
                echo "\"
                       aria-expanded=\"";
                // line 25
                echo (((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus($this->sandbox->ensureToStringAllowed((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 25, $this->source); })()), 25, $this->source)), 0, [], "array", false, false, true, 25) == $context["key1"])) ? ("true") : ("false"));
                echo "\"
                       aria-controls=\"nav-";
                // line 26
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key1"], 26, $this->source), "html", null, true);
                echo "\">
                        <i class=\"fa ";
                // line 27
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level1"], "icon", [], "any", false, false, true, 27), 27, $this->source), "html", null, true);
                echo " fa-fw\" aria-hidden=\"true\"></i>
                        <span>";
                // line 28
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level1"], "name", [], "any", false, false, true, 28), 28, $this->source)), "html", null, true);
                echo "</span>
                    </a>
                    <ul class=\"collapse ";
                // line 30
                if ((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 30, $this->source); })())), 0, [], "array", false, false, true, 30) == $context["key1"])) {
                    echo " show";
                }
                echo "\" id=\"nav-";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key1"], 30, $this->source), "html", null, true);
                echo "\">
                        ";
                // line 31
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["level1"], "children", [], "any", false, false, true, 31));
                foreach ($context['_seq'] as $context["key2"] => $context["level2"]) {
                    // line 32
                    echo "                            <li>
                                ";
                    // line 33
                    if ((twig_get_attribute($this->env, $this->source, $context["level2"], "children", [], "any", true, true, true, 33) && (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["level2"], "children", [], "any", false, false, true, 33)) > 0))) {
                        // line 34
                        echo "                                    <a class=\"c-mainNavArea__navItemSubTitle";
                        echo (((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus($this->sandbox->ensureToStringAllowed((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 34, $this->source); })()), 34, $this->source)), 1, [], "array", false, false, true, 34) != $context["key2"])) ? (" collapsed") : (""));
                        echo "\"
                                       data-bs-toggle=\"collapse\" href=\"#nav-";
                        // line 35
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key2"], 35, $this->source), "html", null, true);
                        echo "\"
                                       aria-expanded=\"";
                        // line 36
                        echo (((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus($this->sandbox->ensureToStringAllowed((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 36, $this->source); })()), 36, $this->source)), 1, [], "array", false, false, true, 36) != $context["key2"])) ? ("true") : ("false"));
                        echo "\"
                                       aria-controls=\"nav-";
                        // line 37
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key2"], 37, $this->source), "html", null, true);
                        echo "\">
                                        <span>";
                        // line 38
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level2"], "name", [], "any", false, false, true, 38), 38, $this->source)), "html", null, true);
                        echo "</span>
                                    </a>
                                    <ul class=\"collapse";
                        // line 40
                        echo (((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus($this->sandbox->ensureToStringAllowed((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 40, $this->source); })()), 40, $this->source)), 1, [], "array", false, false, true, 40) == $context["key2"])) ? (" show") : (""));
                        echo "\" id=\"nav-";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key2"], 40, $this->source), "html", null, true);
                        echo "\">
                                        ";
                        // line 41
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["level2"], "children", [], "any", false, false, true, 41));
                        foreach ($context['_seq'] as $context["key3"] => $context["level3"]) {
                            // line 42
                            echo "                                            <li>
                                                <a href=\"";
                            // line 43
                            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level3"], "url", [], "any", false, false, true, 43), 43, $this->source), ((twig_get_attribute($this->env, $this->source, $context["level3"], "param", [], "any", true, true, true, 43)) ? (twig_get_attribute($this->env, $this->source, $context["level3"], "param", [], "any", false, false, true, 43)) : ([]))), "html", null, true);
                            echo "\"";
                            echo (((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus($this->sandbox->ensureToStringAllowed((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 43, $this->source); })()), 43, $this->source)), 2, [], "array", false, false, true, 43) == $context["key3"])) ? (" class=\"is-active\"") : (""));
                            echo ">
                                                    <span>";
                            // line 44
                            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level3"], "name", [], "any", false, false, true, 44), 44, $this->source)), "html", null, true);
                            echo "</span>
                                                </a>
                                            </li>
                                        ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['key3'], $context['level3'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 48
                        echo "                                    </ul>
                                ";
                    } else {
                        // line 50
                        echo "                                    <a href=\"";
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level2"], "url", [], "any", false, false, true, 50), 50, $this->source), ((twig_get_attribute($this->env, $this->source, $context["level2"], "param", [], "any", true, true, true, 50)) ? (twig_get_attribute($this->env, $this->source, $context["level2"], "param", [], "any", false, false, true, 50)) : ([]))), "html", null, true);
                        echo "\"";
                        echo (((twig_get_attribute($this->env, $this->source, $this->extensions['Eccube\Twig\Extension\EccubeExtension']->getActiveMenus($this->sandbox->ensureToStringAllowed((isset($context["menus"]) || array_key_exists("menus", $context) ? $context["menus"] : (function () { throw new RuntimeError('Variable "menus" does not exist.', 50, $this->source); })()), 50, $this->source)), 1, [], "array", false, false, true, 50) == $context["key2"])) ? (" class=\"is-active\"") : (""));
                        echo ">
                                        <span>";
                        // line 51
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level2"], "name", [], "any", false, false, true, 51), 51, $this->source)), "html", null, true);
                        echo "</span>
                                    </a>
                                ";
                    }
                    // line 54
                    echo "                            </li>
                        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['key2'], $context['level2'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 56
                echo "                    </ul>
                ";
            } else {
                // line 58
                echo "                    <a class=\"c-mainNavArea__navItemTitle\" href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level1"], "url", [], "any", false, false, true, 58), 58, $this->source), ((twig_get_attribute($this->env, $this->source, $context["level1"], "param", [], "any", true, true, true, 58)) ? (twig_get_attribute($this->env, $this->source, $context["level1"], "param", [], "any", false, false, true, 58)) : ([]))), "html", null, true);
                echo "\">
                        <i class=\"fa ";
                // line 59
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level1"], "icon", [], "any", false, false, true, 59), 59, $this->source), "html", null, true);
                echo "\" aria-hidden=\"true\"></i>
                        <span>";
                // line 60
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["level1"], "name", [], "any", false, false, true, 60), 60, $this->source)), "html", null, true);
                echo "</span>
                    </a>
                ";
            }
            // line 63
            echo "            </li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key1'], $context['level1'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 65
        echo "        <!-- 情報 -->
        <li class=\"c-mainNavArea__navItem\">
            <a class=\"c-mainNavArea__navItemTitle collapsed\" data-bs-toggle=\"collapse\" href=\"#others\" aria-expanded=\"false\"
               aria-controls=\"others\">
                <i class=\"fa fa-info-circle fa-fw\" aria-hidden=\"true\"></i>
                <span>";
        // line 70
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.info"), "html", null, true);
        echo "</span>
            </a>
            <ul class=\"collapse\" id=\"others\">
                <li>
                    <a href=\"";
        // line 74
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["eccube_config"]) || array_key_exists("eccube_config", $context) ? $context["eccube_config"] : (function () { throw new RuntimeError('Variable "eccube_config" does not exist.', 74, $this->source); })()), "eccube_official_site_url", [], "any", false, false, true, 74), 74, $this->source), "html", null, true);
        echo "\" target=\"_blank\">
                        <span>";
        // line 75
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.info.official_site"), "html", null, true);
        echo "</span>
                    </a>
                </li>
                <li>
                    <a href=\"";
        // line 79
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["eccube_config"]) || array_key_exists("eccube_config", $context) ? $context["eccube_config"] : (function () { throw new RuntimeError('Variable "eccube_config" does not exist.', 79, $this->source); })()), "eccube_community_site_url", [], "any", false, false, true, 79), 79, $this->source), "html", null, true);
        echo "\" target=\"_blank\">
                        <span>";
        // line 80
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.info.community"), "html", null, true);
        echo "</span>
                    </a>
                </li>
                <li>
                    <a href=\"";
        // line 84
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["eccube_config"]) || array_key_exists("eccube_config", $context) ? $context["eccube_config"] : (function () { throw new RuntimeError('Variable "eccube_config" does not exist.', 84, $this->source); })()), "eccube_document_url", [], "any", false, false, true, 84), 84, $this->source), "html", null, true);
        echo "\" target=\"_blank\">
                        <span>";
        // line 85
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.info.document"), "html", null, true);
        echo "</span>
                    </a>
                </li>
                <li>
                    <a href=\"";
        // line 89
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["eccube_config"]) || array_key_exists("eccube_config", $context) ? $context["eccube_config"] : (function () { throw new RuntimeError('Variable "eccube_config" does not exist.', 89, $this->source); })()), "eccube_manual_url", [], "any", false, false, true, 89), 89, $this->source), "html", null, true);
        echo "\" target=\"_blank\">
                        <span>";
        // line 90
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.info.manual"), "html", null, true);
        echo "</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@admin/nav.twig";
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
        return array (  278 => 90,  274 => 89,  267 => 85,  263 => 84,  256 => 80,  252 => 79,  245 => 75,  241 => 74,  234 => 70,  227 => 65,  220 => 63,  214 => 60,  210 => 59,  205 => 58,  201 => 56,  194 => 54,  188 => 51,  181 => 50,  177 => 48,  167 => 44,  161 => 43,  158 => 42,  154 => 41,  148 => 40,  143 => 38,  139 => 37,  135 => 36,  131 => 35,  126 => 34,  124 => 33,  121 => 32,  117 => 31,  109 => 30,  104 => 28,  100 => 27,  96 => 26,  92 => 25,  88 => 24,  83 => 23,  81 => 22,  78 => 21,  74 => 20,  68 => 17,  63 => 15,  57 => 11,);
    }

    public function getSourceContext()
    {
        return new Source("{#
This file is part of EC-CUBE

Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.

http://www.ec-cube.co.jp/

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
#}
<nav>
    <ul class=\"c-mainNavArea__nav\">
        <!-- ホーム -->
        <li class=\"c-mainNavArea__navItem\">
            <a class=\"c-mainNavArea__navItemTitle\" href=\"{{ url('admin_homepage') }}\">
                <i class=\"fa fa-home fa-fw\" aria-hidden=\"true\"></i>
                <span>{{ 'admin.home'|trans }}</span>
            </a>
        </li>
        {% for key1, level1 in eccubeNav %}
            <li class=\"c-mainNavArea__navItem\">
                {% if level1.children is defined and level1.children|length > 0 %}
                    <a class=\"c-mainNavArea__navItemTitle{{ active_menus(menus)[0] != key1 ? ' collapsed' }}\"
                       data-bs-toggle=\"collapse\" href=\"#nav-{{ key1 }}\"
                       aria-expanded=\"{{ active_menus(menus)[0] == key1 ? 'true' : 'false' }}\"
                       aria-controls=\"nav-{{ key1 }}\">
                        <i class=\"fa {{ level1.icon }} fa-fw\" aria-hidden=\"true\"></i>
                        <span>{{ level1.name|trans }}</span>
                    </a>
                    <ul class=\"collapse {% if active_menus(menus)[0] == key1 %} show{% endif %}\" id=\"nav-{{ key1 }}\">
                        {% for key2, level2 in level1.children %}
                            <li>
                                {% if level2.children is defined and level2.children|length > 0 %}
                                    <a class=\"c-mainNavArea__navItemSubTitle{{ active_menus(menus)[1] != key2 ? ' collapsed' }}\"
                                       data-bs-toggle=\"collapse\" href=\"#nav-{{ key2 }}\"
                                       aria-expanded=\"{{ active_menus(menus)[1] != key2 ? 'true' : 'false' }}\"
                                       aria-controls=\"nav-{{ key2 }}\">
                                        <span>{{ level2.name|trans }}</span>
                                    </a>
                                    <ul class=\"collapse{{ active_menus(menus)[1] == key2 ? ' show' }}\" id=\"nav-{{ key2 }}\">
                                        {% for key3, level3 in level2.children %}
                                            <li>
                                                <a href=\"{{ url(level3.url, level3.param is defined ? level3.param : []) }}\"{{ active_menus(menus)[2] == key3 ? ' class=\"is-active\"' }}>
                                                    <span>{{ level3.name|trans }}</span>
                                                </a>
                                            </li>
                                        {% endfor %}
                                    </ul>
                                {% else %}
                                    <a href=\"{{ url(level2.url, level2.param is defined ? level2.param : []) }}\"{{ active_menus(menus)[1] == key2 ? ' class=\"is-active\"' }}>
                                        <span>{{ level2.name|trans }}</span>
                                    </a>
                                {% endif %}
                            </li>
                        {% endfor %}
                    </ul>
                {% else %}
                    <a class=\"c-mainNavArea__navItemTitle\" href=\"{{ url(level1.url, level1.param is defined ? level1.param : []) }}\">
                        <i class=\"fa {{ level1.icon }}\" aria-hidden=\"true\"></i>
                        <span>{{ level1.name|trans }}</span>
                    </a>
                {% endif %}
            </li>
        {% endfor %}
        <!-- 情報 -->
        <li class=\"c-mainNavArea__navItem\">
            <a class=\"c-mainNavArea__navItemTitle collapsed\" data-bs-toggle=\"collapse\" href=\"#others\" aria-expanded=\"false\"
               aria-controls=\"others\">
                <i class=\"fa fa-info-circle fa-fw\" aria-hidden=\"true\"></i>
                <span>{{ 'admin.info'|trans }}</span>
            </a>
            <ul class=\"collapse\" id=\"others\">
                <li>
                    <a href=\"{{ eccube_config.eccube_official_site_url }}\" target=\"_blank\">
                        <span>{{ 'admin.info.official_site'|trans }}</span>
                    </a>
                </li>
                <li>
                    <a href=\"{{ eccube_config.eccube_community_site_url }}\" target=\"_blank\">
                        <span>{{ 'admin.info.community'|trans }}</span>
                    </a>
                </li>
                <li>
                    <a href=\"{{ eccube_config.eccube_document_url }}\" target=\"_blank\">
                        <span>{{ 'admin.info.document'|trans }}</span>
                    </a>
                </li>
                <li>
                    <a href=\"{{ eccube_config.eccube_manual_url }}\" target=\"_blank\">
                        <span>{{ 'admin.info.manual'|trans }}</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
", "@admin/nav.twig", "D:\\htdocs82\\kendo\\www\\src\\Eccube\\Resource\\template\\admin\\nav.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 20, "if" => 22);
        static $filters = array("escape" => 17, "trans" => 17, "length" => 22);
        static $functions = array("url" => 15, "active_menus" => 23);

        try {
            $this->sandbox->checkSecurity(
                ['for', 'if'],
                ['escape', 'trans', 'length'],
                ['url', 'active_menus']
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
