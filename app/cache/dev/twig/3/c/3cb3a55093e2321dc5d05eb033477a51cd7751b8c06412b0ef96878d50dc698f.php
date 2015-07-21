<?php

/* DidYouDoItBundle:Error:error.html.twig */
class __TwigTemplate_3cb3a55093e2321dc5d05eb033477a51cd7751b8c06412b0ef96878d50dc698f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("::base.html.twig", "DidYouDoItBundle:Error:error.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "<h2 class=\"text-left\">Error</h2>

    <p>
        ";
        // line 7
        echo twig_escape_filter($this->env, (isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "html", null, true);
        echo "
    </p>
    <ul class=\"list-unstyled\">
        <li>
            <a class=\"btn btn-primary\" role=\"button\" href=\"";
        // line 11
        echo $this->env->getExtension('routing')->getPath("did_you_do_it_get_all_tasklist");
        echo "\">
                Back to the list of all tasklist
            </a>
        </li>
    </ul>
";
    }

    public function getTemplateName()
    {
        return "DidYouDoItBundle:Error:error.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 11,  36 => 7,  31 => 4,  28 => 3,  11 => 1,);
    }
}
