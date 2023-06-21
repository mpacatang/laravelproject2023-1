<!DOCTYPE html>
<html style="user-select: none;">

<!---<html  data-layout-mode="detached" data-topbar-color="light" data-sidenav-color="light" style="user-select: none;">-->
<head>
    <meta charset="utf-8">
    @if(isset($business_settings))
        <meta content="{{$business_settings}}" type="business_setting"/>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Farmers Market - Canada</title>

    <!-- Fonts -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <!---- Customer ---->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@900,700,500,301,701,300,501,401,901,400&display=swap" rel="stylesheet">
    <style>

        .splash-screen .dark {
            display: none;
        }

        html[data-theme=dark] .splash-screen .dark {
            display: block;
        }

        html[data-theme=dark] .splash-screen .light {
            display: none;
        }

        .splash-screen.loader {
            position: fixed;
            top: 50%;
            left: 50%;
            background: white;
            display: flex;
            height: 100%;
            width: 100%;
            transform: translate(-50%, -50%);
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: all 15s linear;
            overflow: hidden;
        }

        html[data-theme=dark] .splash-screen.loader {
            background-color: #292e32;
        }

        div + .splash-screen.loader {
            animation: fadeout 0.7s forwards;
            z-index: 0;
        }

        @keyframes fadeout {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }
    </style>

    <script>
        try {
            const config = JSON.parse(localStorage.getItem('layout_data'));
            const html = document.querySelector("html");
            if (html && config) {
                if (config.is_dark) {
                    html.setAttribute('data-theme', 'dark');
                    html.setAttribute('data-topbar-color', 'dark');
                    html.setAttribute('data-sidenav-color', 'dark');

                } else {
                    if (config.topbar?.color) {
                        html.setAttribute('data-topbar-color', config.topbar.color);
                    }
                    if (config.leftbar?.color) {
                        html.setAttribute('data-sidenav-color', config.leftbar.color);
                    }
                }
                if (config.mode) {
                    html.setAttribute('data-layout-mode', config.mode);
                }
                if (config.leftbar?.mode) {
                    html.setAttribute('data-sidenav-size', config.leftbar.mode);
                }
            }
        } catch (e) {
            console.info(e);
        }
    </script>

</head>

<body>
<div id="app">
    <router-view></router-view>

    <div class="splash-screen loader">
        <img class="light" style="height: 25%"
             src="/assets/images/logo/dark-lg.png"
             alt="Logo"/>
        <img class="dark" style="height: 25%"
             src="/assets/images/logo/light-lg.png"
             alt="Logo"/>
    </div>
</div>
</body>

<footer-script>
    <script src="{{mix('js/app.js')}}" defer></script>

    <link id="root-style-link" rel="stylesheet" href="{{asset('css/app.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/icons.css')}}"/>

    @if(isset($business_settings))
        <script src="{{ "https://maps.googleapis.com/maps/api/js?key=". json_decode($business_settings)->google_map_api_key . "&libraries=drawing,places"}}"></script>
    @endif
</footer-script>

<script>
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function () {
            navigator.serviceWorker.register("/firebase-messaging-sw.js").then((registration) => {
                // registration worked

                registration.update();
            });
        });
    }
</script>

@if(isset($google_analytics_enable) && $google_analytics_enable)
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{$google_analytics_id}}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', "{{$google_analytics_id}}");
    </script>
@endif

</html>


