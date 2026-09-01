<!DOCTYPE html>
<html>

<head>
    <title>Print Kartu Service</title>
</head>

<body style="margin:0">
    <iframe id="pdfFrame" src="data:application/pdf;base64,{{ $base64 }}"
        style="width:100%;height:100vh;border:none;"></iframe>
    <script>
        document.getElementById('pdfFrame').onload = function() {
            this.contentWindow.focus();
            this.contentWindow.print();
        };
    </script>
</body>

</html>
