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

/* Block/news.twig */
class __TwigTemplate_1c1550048bedbb622b208b2a2d0f4af9 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'javascript' => [$this, 'block_javascript'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Block/news.twig"));

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
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Block/news.twig"));

        // line 11
        $context["NewsList"] = twig_get_attribute($this->env, $this->source, $this->env->getFunction('repository')->getCallable()("Eccube\\Entity\\News"), "getList", [], "method", false, false, true, 11);
        // line 12
        echo "
";
        // line 13
        $this->displayBlock('javascript', $context, $blocks);
        // line 49
        echo "<div class=\"ec-role\">
    <div class=\"ec-newsRole\">
        <div class=\"ec-secHeading\">
            <span class=\"ec-secHeading__en\">";
        // line 52
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("NEWS"), "html", null, true);
        echo "</span>
            <span class=\"ec-secHeading__line\"></span>
            <span class=\"ec-secHeading__ja\">";
        // line 54
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("新着情報"), "html", null, true);
        echo "</span>
        </div>
        <div class=\"ec-newsRole__news\">
            ";
        // line 57
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["NewsList"]) || array_key_exists("NewsList", $context) ? $context["NewsList"] : (function () { throw new RuntimeError('Variable "NewsList" does not exist.', 57, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["News"]) {
            // line 58
            echo "                <div class=\"ec-newsRole__newsItem\">
                    <div class=\"ec-newsRole__newsHeading\">
                        <div class=\"ec-newsRole__newsDate\">
                            ";
            // line 61
            echo twig_escape_filter($this->env, $this->extensions['Eccube\Twig\Extension\IntlExtension']->date_day($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["News"], "publish_date", [], "any", false, false, true, 61), 61, $this->source)), "html", null, true);
            echo "
                        </div>
                        <div class=\"ec-newsRole__newsColumn\">
                            <div class=\"ec-newsRole__newsTitle\">
                                ";
            // line 65
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["News"], "title", [], "any", false, false, true, 65), 65, $this->source), "html", null, true);
            echo "
                            </div>
                            ";
            // line 67
            if ((twig_get_attribute($this->env, $this->source, $context["News"], "description", [], "any", false, false, true, 67) || twig_get_attribute($this->env, $this->source, $context["News"], "url", [], "any", false, false, true, 67))) {
                // line 68
                echo "                                <div class=\"ec-newsRole__newsClose\">
                                    <a class=\"ec-newsRole__newsCloseBtn\">
                                        <i class=\"fas fa-angle-down\"></i>
                                    </a>
                                </div>
                            ";
            }
            // line 74
            echo "                        </div>
                    </div>
                    <div class=\"ec-newsRole__newsDescription\">
                        ";
            // line 77
            echo twig_nl2br($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["News"], "description", [], "any", false, false, true, 77), 77, $this->source));
            echo "
                        ";
            // line 78
            if (twig_get_attribute($this->env, $this->source, $context["News"], "url", [], "any", false, false, true, 78)) {
                // line 79
                echo "                            <br>
                            <a href=\"";
                // line 80
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["News"], "url", [], "any", false, false, true, 80), 80, $this->source), "html", null, true);
                echo "\" ";
                if ((twig_get_attribute($this->env, $this->source, $context["News"], "link_method", [], "any", false, false, true, 80) == "1")) {
                    echo "target=\"_blank\"";
                }
                echo ">";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("詳しくはこちら"), "html", null, true);
                echo "</a>
                        ";
            }
            // line 82
            echo "                    </div>
                </div>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['News'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 85
        echo "        </div>
    </div>
</div>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 13
    public function block_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascript"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascript"));

        // line 14
        echo "    <script>
        \$(function() {
            \$('.ec-newsRole__news').each(function() {
                var listLength = \$(this).find('.ec-newsRole__newsItem').length;
                if (listLength > 5) {
                    \$(this).find('.ec-newsRole__newsItem:gt(4)').each(function() {
                        \$(this).hide();
                    });
                    \$(this).append('<a id=\"news_readmore\" class=\"ec-inlineBtn--top\">";
        // line 22
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("more"), "html", null, true);
        echo "</a>');
                    var dispNum = 5;
                    \$(this).find('#news_readmore').click(function() {
                        dispNum += 5;
                        \$(this).parent().find('.ec-newsRole__newsItem:lt(' + dispNum + ')').show();
                        if (dispNum >= listLength) {
                            \$(this).hide();
                        }
                    })
                }
            });

            \$('.ec-newsRole__newsHeading').on('click', function() {
                \$newsItem = \$(this).parent('.ec-newsRole__newsItem');
                \$newsDescription = \$newsItem.children('.ec-newsRole__newsDescription');
                if (\$newsDescription.css('display') == 'none') {
                    \$newsItem.addClass('is_active');
                    \$newsDescription.slideDown(300);
                } else {
                    \$newsItem.removeClass('is_active');
                    \$newsDescription.slideUp(300);
                }
                return false;
            });
        });
    </script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "Block/news.twig";
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
        return array (  178 => 22,  168 => 14,  158 => 13,  145 => 85,  137 => 82,  126 => 80,  123 => 79,  121 => 78,  117 => 77,  112 => 74,  104 => 68,  102 => 67,  97 => 65,  90 => 61,  85 => 58,  81 => 57,  75 => 54,  70 => 52,  65 => 49,  63 => 13,  60 => 12,  58 => 11,);
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
{% set NewsList = repository('Eccube\\\\Entity\\\\News').getList() %}

{% block javascript %}
    <script>
        \$(function() {
            \$('.ec-newsRole__news').each(function() {
                var listLength = \$(this).find('.ec-newsRole__newsItem').length;
                if (listLength > 5) {
                    \$(this).find('.ec-newsRole__newsItem:gt(4)').each(function() {
                        \$(this).hide();
                    });
                    \$(this).append('<a id=\"news_readmore\" class=\"ec-inlineBtn--top\">{{ 'more'|trans }}</a>');
                    var dispNum = 5;
                    \$(this).find('#news_readmore').click(function() {
                        dispNum += 5;
                        \$(this).parent().find('.ec-newsRole__newsItem:lt(' + dispNum + ')').show();
                        if (dispNum >= listLength) {
                            \$(this).hide();
                        }
                    })
                }
            });

            \$('.ec-newsRole__newsHeading').on('click', function() {
                \$newsItem = \$(this).parent('.ec-newsRole__newsItem');
                \$newsDescription = \$newsItem.children('.ec-newsRole__newsDescription');
                if (\$newsDescription.css('display') == 'none') {
                    \$newsItem.addClass('is_active');
                    \$newsDescription.slideDown(300);
                } else {
                    \$newsItem.removeClass('is_active');
                    \$newsDescription.slideUp(300);
                }
                return false;
            });
        });
    </script>
{% endblock %}
<div class=\"ec-role\">
    <div class=\"ec-newsRole\">
        <div class=\"ec-secHeading\">
            <span class=\"ec-secHeading__en\">{{ 'NEWS'|trans }}</span>
            <span class=\"ec-secHeading__line\"></span>
            <span class=\"ec-secHeading__ja\">{{ '新着情報'|trans }}</span>
        </div>
        <div class=\"ec-newsRole__news\">
            {% for News in NewsList %}
                <div class=\"ec-newsRole__newsItem\">
                    <div class=\"ec-newsRole__newsHeading\">
                        <div class=\"ec-newsRole__newsDate\">
                            {{ News.publish_date|date_day }}
                        </div>
                        <div class=\"ec-newsRole__newsColumn\">
                            <div class=\"ec-newsRole__newsTitle\">
                                {{ News.title }}
                            </div>
                            {% if News.description or News.url %}
                                <div class=\"ec-newsRole__newsClose\">
                                    <a class=\"ec-newsRole__newsCloseBtn\">
                                        <i class=\"fas fa-angle-down\"></i>
                                    </a>
                                </div>
                            {% endif %}
                        </div>
                    </div>
                    <div class=\"ec-newsRole__newsDescription\">
                        {{ News.description|raw|nl2br }}
                        {% if News.url %}
                            <br>
                            <a href=\"{{ News.url }}\" {% if News.link_method == '1' %}target=\"_blank\"{% endif %}>{{ '詳しくはこちら'|trans }}</a>
                        {% endif %}
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
</div>
", "Block/news.twig", "D:\\htdocs82\\kendo\\www\\src\\Eccube\\Resource\\template\\default\\Block\\news.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 11, "block" => 13, "for" => 57, "if" => 67);
        static $filters = array("escape" => 52, "trans" => 52, "date_day" => 61, "nl2br" => 77, "raw" => 77);
        static $functions = array("repository" => 11);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'block', 'for', 'if'],
                ['escape', 'trans', 'date_day', 'nl2br', 'raw'],
                ['repository']
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
