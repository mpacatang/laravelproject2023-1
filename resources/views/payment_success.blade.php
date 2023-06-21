<!DOCTYPE html>
<html style="user-select: none;">

<!---<html  data-layout-mode="detached" data-topbar-color="light" data-sidenav-color="light" style="user-select: none;">-->
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Payment Success</title>

    <!-- Fonts -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <script>
        function handleSuccess() {
            try {
                if (window.flutter_inappwebview != null) {
                    window.flutter_inappwebview.callHandler('flutter_channel', true)
                }
                if (window.opener) {
                    window.opener.postMessage({'payment_success': true});
                }
                window.postMessage({'payment_success': true})

                window.close();
            } catch (e) {
                console.info(e);
            }
        }


        setTimeout(() => handleSuccess(), 5000);
    </script>

</head>

<body>
<div style="text-align: center; margin-top: 100px">
    <div style="display:flex; justify-content: center; align-items: center">
        <img height="36" width="36"
             src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIxNDYuMTc5IDI4Ni4yNjggNTAuMzg4IDQ4LjE3MyIgd2lkdGg9IjUwLjM4OCIgaGVpZ2h0PSI0OC4xNzMiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPHBhdGggZD0iTTI0IDQ0cS00LjI1IDAtNy45LTEuNTI1LTMuNjUtMS41MjUtNi4zNS00LjIyNS0yLjctMi43LTQuMjI1LTYuMzVRNCAyOC4yNSA0IDI0cTAtNC4yIDEuNTI1LTcuODVRNy4wNSAxMi41IDkuNzUgOS44cTIuNy0yLjcgNi4zNS00LjI1UTE5Ljc1IDQgMjQgNHEzLjc1IDAgNyAxLjJ0NS44NSAzLjNsLTIuMTUgMi4xNXEtMi4yLTEuNzUtNC45LTIuN1EyNy4xIDcgMjQgN3EtNy4yNSAwLTEyLjEyNSA0Ljg3NVQ3IDI0cTAgNy4yNSA0Ljg3NSAxMi4xMjVUMjQgNDFxNy4yNSAwIDEyLjEyNS00Ljg3NVQ0MSAyNHEwLTEuNS0uMjI1LTIuOTI1LS4yMjUtMS40MjUtLjY3NS0yLjc3NWwyLjMtMi4zcS44IDEuODUgMS4yIDMuODUuNCAyIC40IDQuMTUgMCA0LjI1LTEuNTUgNy45LTEuNTUgMy42NS00LjI1IDYuMzUtMi43IDIuNy02LjM1IDQuMjI1UTI4LjIgNDQgMjQgNDRabS0yLjk1LTEwLjktOC4yNS04LjMgMi4yNS0yLjI1IDYgNiAyMC43LTIwLjcgMi4zIDIuMjVaIiB0cmFuc2Zvcm09Im1hdHJpeCgxLCAwLCAwLCAxLCAxNDYuNTE3NjM5LCAyODQuOTcwMDkzKSIgc3R5bGU9ImZpbGw6IHJnYigxNiwgMTk2LCAxMDUpOyIvPgo8L3N2Zz4=">
        <h3 style="margin: 0 0 0 16px; color: #0b9654; letter-spacing: 0.4px">Payment Success</h3>
    </div>
    <p style="font-size: 15px">We will redirect you in 5 seconds...</p>
</div>
</body>
</html>