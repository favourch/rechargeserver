
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <title>Google Chart Tools AngularJS Directive </title>
</head>
<body ng-app="google-chart-sample" ng-cloak="">
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">angular-google-chart</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li>
               
                </li>
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" style="cursor: pointer" data-toggle="dropdown">Samples <b
                            class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#/generic/BarChart"><i class="fa fa-bars"></i> Bar chart</a></li>
                        <li><a href="#/generic/ColumnChart"><i class="fa fa-bar-chart-o"></i> Column chart</a></li>
                        <li><a href="#/generic/PieChart"><i class="fa fa-circle"></i> Pie chart</a></li>
                        <li><a href="#/generic/LineChart">Line chart</a></li>
                        <li><a href="#/annotation">Annotation chart</a></li>
                        <li><a href="#/gauge">Gauge chart</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">All together</li>
                        <li><a href="#/fat">Mutli charts sample</a></li>
                    </ul>
                </li>
              
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12" style="text-align: center;">
            <h1>AngularJs Google Chart Tools directive</h1>
            <hr/>
        </div>
    </div>
    <div class="row">
        <div ng-view></div>
    </div>

</div>
<!-- jQuery imported here only for Bootstrap.js and for this sample functions. It's not needed for the directive itself. -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular-route.min.js"></script>
<script src="ng-google-chart.js"></script>
<script src="sample.js"></script>
<script src="partials/annotation.js"></script>
<script src="partials/generic.js"></script>
<script src="partials/gauge.js"></script>
<script src="partials/fat.js"></script>
<!-- Google Analytics -->
<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
    ga('create', 'UA-3451018-11', 'github.io');
    ga('send', 'pageview');
</script>
</body>
</html>