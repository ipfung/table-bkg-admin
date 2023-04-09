<html>
    <head>
        <title>Redirecting to payment gateway</title>
    </head>
    <body>
    <form id="payForm" action="{{ $url }}" method="post">
        @foreach($data as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <input type="hidden" name="hash" value="{{ $hash }}">
    </form>
    <script type="text/javascript">
        document.getElementById('payForm').submit();
    </script>
    </body>
</html>
