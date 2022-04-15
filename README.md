# Nöbetci Eczaneler
Php ile günlük nöbetçi eczane listesini veren Php sınıfı

Eczanelere ait veriler <a href="https://bulurum.com/" target="_blank">bulurum.com</a> sitesinden alınmıştır. 

Ecza.io sitesinden çekilen veriler, json olarak tutulabilir. Bu sayede her seferinde Bulurum.com sitesinden çekilmeyeceği için performans artışı sağlanabilir.

<h2>Çalışma Mantığı</h2>

Öncelikle Php Sınıfımızı Sayfaya Dahil Edelim.
```php
require_once("Pharmacy.class.php");
```
Daha Sonra Sınıfımızı Başlatalım. 
```php
// Hangi İlçeyi İstiyorsak Parametre Verileri Gönderlim
$pharmacy = new Pharmacy('Konak');
```

Daha Sonra Nöbetçi Eczanelerimizi Çekelim.
```php
// Verilerimizi Çekebiliriz.
echo $pharmacy->get();
```

Genel Olarak Tam Kodumuz Şöyle. 
```php
header("Content-type:application/json");

// Sınıfımızı Sayfamıza Dahil Ettik 
require_once("Pharmacy.class.php");

// Sınıfı Başlattık 
$pharmacy = new Pharmacy('Konak');

// Nöbetçi Eczanelerimizi JSON Olarak Çektik 
echo $pharmacy->get();
```
