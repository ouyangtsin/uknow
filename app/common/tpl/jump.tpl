{__NOLAYOUT__}
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>跳转提示</title>
    <style>
        body {
            margin: 0;
        }
        .loader {
            position: absolute;
            top: 50%;
            left: 40%;
            margin-left: 10%;
            transform: translate3d(-50%, -50%, 0);
        }
        .dot {
            width: 24px;
            height: 24px;
            background: #3ac;
            border-radius: 100%;
            display: inline-block;
            animation: slide 1s infinite;
        }
        .dot:nth-child(1) {
            animation-delay: 0.1s;
            background: #32aacc;
        }
        .dot:nth-child(2) {
            animation-delay: 0.2s;
            background: #64aacc;
        }
        .dot:nth-child(3) {
            animation-delay: 0.3s;
            background: #96aacc;
        }
        .dot:nth-child(4) {
            animation-delay: 0.4s;
            background: #c8aacc;
        }
        .dot:nth-child(5) {
            animation-delay: 0.5s;
            background: #faaacc;
        }
        @-moz-keyframes slide {
            0% {
                transform: scale(1);
            }
            50% {
                opacity: 0.3;
                transform: scale(2);
            }
            100% {
                transform: scale(1);
            }
        }
        @-webkit-keyframes slide {
            0% {
                transform: scale(1);
            }
            50% {
                opacity: 0.3;
                transform: scale(2);
            }
            100% {
                transform: scale(1);
            }
        }
        @-o-keyframes slide {
            0% {
                transform: scale(1);
            }
            50% {
                opacity: 0.3;
                transform: scale(2);
            }
            100% {
                transform: scale(1);
            }
        }
        @keyframes slide {
            0% {
                transform: scale(1);
            }
            50% {
                opacity: 0.3;
                transform: scale(2);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
    <script>
        window.console = window.console || function(t) {};
    </script>
    <script>
        if (document.location.search.match(/type=embed/gi)) {
            window.parent.postMessage("resize", "*");
        }
    </script>
</head
<body translate="no">
<div class="loader">
    <div class="dot"></div>
    <div class="dot"></div>
    <div class="dot"></div>
    <div class="dot"></div>
    <div class="dot"></div>
</div>
<script type="text/javascript">
    (function(){
        var wait = {$wait},
            href = "{$url}";
        var interval = setInterval(function(){
            var time = --wait;
            if(time <= 0) {
                window.location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>