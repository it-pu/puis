<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>


    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>

<div class="container" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="width: 1%;">No</th>
                    <th>NIM</th>
                    <th>Name</th>
                    <th>Prodi</th>
                    <th>Total SKS Ganjil</th>
                    <th>IPK Ganjil</th>

                    <th>Total SKS Genap</th>
                    <th>IPK Genap</th>
                </tr>
                </thead>
                <?php for ($i=0;$i<count($datasc);$i++){ $d = $datasc[$i]; ?>

                    <tr>
                        <td><?= $i+1; ?></td>
                        <td><?= $d['NPM']; ?></td>
                        <td><?= $d['Name']; ?></td>
                        <td><?= $d['Prodi']; ?></td>

                        <td><?= $d['Lalu_TotalSKS']; ?></td>
                        <td><?= $d['Lalu_IPK_Pembulatan']; ?></td>

                        <td><?= $d['TotalSKS']; ?></td>
                        <td><?= $d['IPK_Pembulatan']; ?></td>

                    </tr>

                <?php } ?>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>