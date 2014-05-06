<?php
    require_once('dal/DaoDB.php');

    echo '<h3>Képek száma kategóriánként</h3>
            <table class="table striped">
                <thead>
                    <tr>
                        <th class="text-left">Kategória</th>
                        <th class="text-left">Képek száma</th>
                    </tr>
                </thead>
                <tbody>';
                $dal = new DaoDB();
                $kats = $dal->getNumOfPicsByCategory();
                foreach ($kats as $key => $value) {
                    echo '<tr>
                        <td>' . $value['KATEGORIA'] . '</td><td>' . $value['NUM'] . '</td>
                    </tr>';
                }
    echo '</tbody>
            </table>';
    echo '<h3>Legtöbb képpel rendelkező felhasználók</h3>
            <table class="table striped">
                <thead>
                    <tr>
                        <th class="text-left">Név</th>
                        <th class="text-left">Képek száma</th>
                        <th class="text-left">Albumok száma</th>
                        <th class="text-left">Képek átlagértékelése</th>
                    </tr>
                </thead>
                <tbody>';
                $usr = $dal->getNumOfPicsByUser();
                foreach ($usr as $key => $value) {
                    echo '<tr>
                        <td>' . $value['NEV'] . '</td><td>' . $value['PICNUM'] . '</td>
                        <td>' . $dal->getNumOfAlbumsByUserId($value['ID']) . '</td> <td>' . $dal->getUserPictureRating($value['ID']) . '</td>
                    </tr>';
                }
    echo '</tbody>
            </table>';
    echo '<h3>Városonként legtöbb kép</h3>
            <table class="table striped">
                <thead>
                    <tr>
                        <th class="text-left">Ország</th>
                        <th class="text-left">Város</th>
                        <th class="text-left">Képek száma</th>
                    </tr>
                </thead>
                <tbody>';
                $city = $dal->getNumOfPicsByCities();
                foreach ($city as $key => $value) {
                    echo '<tr>
                        <td>' . $value['ORSZAG'] . '</td><td>' . $value['VAROS'] . '</td>
                        <td>' . $value['KEPEK_SZAMA'] . '</td>
                    </tr>';
                }
    echo '</tbody>
            </table>';
    echo '<h3>Általános statisztikák</h3>
            <table class="table striped">
                <thead>
                    <tr>
                        <th class="text-left">Felhasználók száma</th>
                        <th class="text-left">Hozzászólások száma</th>
                        <th class="text-left">Képek száma</th>
                        <th class="text-left">Albumok száma</th>
                        <th class="text-left">Értékelések száma</th>
                    </tr>
                </thead>
                <tbody>';
                    echo '<tr>
                        <td>' . $dal->getNumOfTable('FELHASZNALOK') . '</td><td>' . $dal->getNumOfTable('HOZZASZOLASOK') . '</td>
                        <td>' . $dal->getNumOfTable('KEPEK') . '</td><td>' . $dal->getNumOfTable('ALBUMOK') . '</td>
                        <td>' . $dal->getNumOfTable('ERTEKELESEK') . '</td>
                    </tr>';
    echo '</tbody>
            </table>';
?>