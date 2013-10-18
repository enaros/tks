<?php

/* TksBundle:Security:login.html.twig */
class __TwigTemplate_cd0dea8450a14f3033d3574f71ad9cb1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>TKS - Login</title>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <link href=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/tks/bootstrap/css/bootstrap.min.css"), "html", null, true);
        echo "\" rel=\"stylesheet\" media=\"screen\">
    <link href=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/tks/bootstrap/css/bootstrap-responsive.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">
    <link href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/tks/css/styles.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src=\"http://html5shim.googlecode.com/svn/trunk/html5.js\"></script>
    <![endif]-->

    <script src=\"http://use.edgefonts.net/source-sans-pro.js\"></script>
    <style type=\"text/css\">
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            max-width: 300px;
            padding: 19px 29px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }
        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }
        .form-signin input[type=\"text\"],
        .form-signin input[type=\"password\"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 15px;
            padding: 7px 9px;
        }

    </style>
</head>

<body>

    <div class=\"container\">

        <form class=\"form-signin\" action=\"";
        // line 56
        echo $this->env->getExtension('routing')->getPath("login_check");
        echo "\" method=\"post\">
            <h2 class=\"form-signin-heading\">Please sign in</h2>
            ";
        // line 58
        if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
        if ($_error_) {
            // line 59
            echo "            <div class=\"alert alert-error\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
                <strong>Error!</strong> ";
            // line 61
            if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_error_, "message"), "html", null, true);
            echo "
            </div>
            ";
        }
        // line 64
        echo "            <input type=\"text\" class=\"input-block-level\" placeholder=\"Username\" id=\"username\" name=\"_username\" value=\"";
        if (isset($context["last_username"])) { $_last_username_ = $context["last_username"]; } else { $_last_username_ = null; }
        echo twig_escape_filter($this->env, $_last_username_, "html", null, true);
        echo "\">
            <input type=\"password\" id=\"password\" name=\"_password\" class=\"input-block-level\" placeholder=\"Password\">
            <label class=\"checkbox\">
                <input type=\"checkbox\" value=\"remember-me\"> Remember me
            </label>
            <button class=\"btn btn-large btn-primary\" type=\"submit\">Sign in</button>
            ";
        // line 74
        echo "        </form>

    </div> <!-- /container -->

<script src=\"";
        // line 78
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/tks/lib/jquery-1.9.1.min.js"), "html", null, true);
        echo "\"></script>
<script>
    \$('input:first').focus();
</script>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "TksBundle:Security:login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  121 => 78,  115 => 74,  104 => 64,  97 => 61,  93 => 59,  90 => 58,  85 => 56,  35 => 9,  31 => 8,  27 => 7,  19 => 1,);
    }
}
