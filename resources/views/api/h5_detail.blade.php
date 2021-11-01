<!DOCTYPE html>
<html lang="en" style="font-size: 100px;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $row['name'] ?? '' }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1,user-scalable=0">
    <script>
        //改变font-size
        (function(doc,win){
            var docEI = doc.documentElement,
                resizeEvt = 'orientationchange' in window?'orientataionchange':'resize',
                recalc = function(){
                    var clientWidth = docEI.clientWidth;
                    if(!clientWidth) return;
                    docEI.style.fontSize = 100*(clientWidth/750)+'px';
                }
            if(!doc.addEventListener) return;
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document,window);
    </script>
    <link rel="stylesheet" href="{{ API_CSS }}/style.css" />
    <style>
        body{
            font-size: 0.3rem !important;
        }
        .redactor-styles img, .redactor-styles video {
            width: 100%;
            max-width: 100% !important;
            min-width: auto !important;
        }
    </style>
</head>
<body>
<div id="app" class="redactor-styles">
    {!! $row['content'] ?? '' !!}
</div>
</body>
</html>
