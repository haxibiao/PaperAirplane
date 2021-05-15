<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$app ? $app->bot->remarks . ' - 订阅管理' : '纸飞机订阅管理平台 - 出错啦！'}}</title>
    <meta name="user" content="{{$my ?? ''}}" />
    <meta name="app_id" content="{{$app->id ?? ''}}" />
    <link rel="stylesheet" media="all" href={{mix('css/subscribe.css')}}>
</head>

<body class="antialiased">
    <div id="root">
    </div>

    <script src={{mix('js/subscribe.js')}}></script>
</body>

</html>
