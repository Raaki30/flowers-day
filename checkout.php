<?php

    namespace midtrans;
    session_start();
    
    

    require_once dirname(__FILE__) . '/midtrans-lib/Midtrans.php';

    Config::$serverKey = 'SB-Mid-server-N-S0Nmmooh4w-Yuk6EJPHr1m';
    Config::$clientKey = 'SB-Mid-client-U66AiCovKvOufMr_';

    // Enable sanitization
    Config::$isSanitized = true;
    //token
    

    // Generate a secure random token
    $kode = bin2hex(random_bytes(16));

    // Store the token in a session variable
    $_SESSION['kode'] = $kode;

    // Enable 3D-Secure
   Config::$is3ds = true;

   $nama = $_GET['namaPengirim'];
$email = $_GET['email'];
$phone = $_GET['nomorTelepon'];

$amount = $_GET['hargaTotal'];

$orderid = rand();

$transaction_details = array(
    'order_id' => $orderid,
    'gross_amount' => $amount, // no decimal allowed for creditcard
);

$customer_details = array(
    'first_name'    => $nama,
    'last_name'     => "",
    'email'         => $email,
    'phone'         => $phone,
    
);

$transaction = array(
    'transaction_details'  => $transaction_details,
    'customer_details' => $customer_details

);

$snap_token = '';
try {
    $snap_token = Snap::getSnapToken($transaction);
}
catch (\Exception $e) {
    echo $e->getMessage();
};




?>
<!DOCTYPE html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    .container {
    padding-top: 100px;
}
</style>
<html>
    <body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Total: <?php echo $amount; ?>
                    </div>
                    <div class="card-body">
                        <button id="pay-button" class="btn btn-primary btn-block">Bayar Sekarang</button>
                    </div>
                </div>
                <div class="mt-3" style="font-size: 10px; text-align: center;">
                    Payment Token: <?php echo $snap_token; ?>
                </div>
            </div>
        </div>
    </div>
        <!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Config::$clientKey;?>"></script>
        <script type="text/javascript">
            document.getElementById('pay-button').onclick = function(){
                // SnapToken acquired from previous step
                snap.pay('<?php echo $snap_token?>', {
                    // Optional
                    onSuccess: function(result) {
                        window.close();

                    // Mengaktifkan kembali tombol di halaman sebelumnya
                    if (window.opener && !window.opener.closed) {
                        var form = window.opener.document.getElementById('form');
                        if (form) {
                            form.submit(); 
                        }
                    }

                    },

                    
                    // Optional
                    onError: function(result){
                      alert("Transaction failed. Try Again");
                        
                    }
                });
            };
        </script>
        </body>
</html>
