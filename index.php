<?php
    /**
     * 	@author: Ufuk OZDEMIR
     * 	@mail: ufuk.ozdemir1990@gmail.com || info@ufukozdemir.website
     * 	@website: ufukozdemir.website
     */

    // Sınıfımızı Sayfamıza Dahil Ettik
    require_once(__DIR__."/Pharmacy.class.php");

    // Sınıfı Başlattık
    $pharmacy = new Pharmacy('Konak');

    // Nöbetçi Eczanelerimizi JSON Olarak Çektik
    $data = $pharmacy->get();
?>
<!doctype html>
<html lang="tr" class="h-100">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Pharmacy on Duty</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.27.0/themes/prism-okaidia.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.27.0/prism.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.27.0/plugins/autoloader/prism-autoloader.min.js"></script>
    </head>
    <body class="align-items-center d-flex h-100 justify-content-center">
        <div class="container">
            <pre>
                <code class="language-php">
require_once('Pharmacy.class.php');
$pharmacy = new Pharmacy('Konak');
$data = $pharmacy->get();

foreach ($data as $item) {
    echo $item->name;
    echo $item->address;
    echo $item->phone;
    echo $item->time;
}
                </code>
            </pre>
            <div class="card shadow-lg">
                <div class="bg-warning card-header py-3">
                    <h5 class="mb-0 text-center"><?php echo $data[0]->title. ' - ' .$data[0]->day. ' ' .$data[0]->date; ?></h5>
                </div>
                <div class="card-body px-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pharmacy Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($data !== NULL): ?>
                                <?php foreach ($data as $item): ?>
                                    <tr>
                                        <td><?php echo $item->name; ?></td>
                                        <td><a href="<?php echo $item->maps; ?>" target="_blank" class="text-decoration-none text-body"><i class="bi bi-link-45deg"></i> <?php echo $item->address; ?></a></td>
                                        <td><a href="tel:<?php echo $item->phone; ?>" target="_blank" class="text-decoration-none text-body"><i class="bi bi-telephone"></i> <?php echo $item->phone; ?></a></td>
                                        <td><?php echo $item->time; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No Found Pharmacy on Duty</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
