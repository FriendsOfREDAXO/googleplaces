Grundsätzlich können die Reviews aus der Tabelle auch per SQL geholt werden und anschließend individuell ausgegeben und dargestellt werden.<br>
Das AddOn bringt ein Beispiel-Modul für den Output mit. Hierzu wird z.B. das Fragment `googleplaces_reviews.bs4.php` verwendet. Das Fragment kann auch leicht z.B. über das Theme-AddOn oder das Project-AddOn updatesicher überschrieben werden.

Die Beispiel-Module holen die Reviews jeweils aus der eigenen Datenbank und nicht über die Google-API.

Die entsprechende CSS-Datei mit den Styles für die Ausgabe liegt im assets-Ordner des AddOns.

## Beispiel-Modul mit Bootstrap 4 Markup

![BS4 Modul](../assets/addons/googleplaces/img/bsp-modul-bs4.jpg)

Das Modul benötigt Bootstrap 4 und Fontawesome für die Sterne. <br>
CSS-Style:<br>
`assets/addons/googleplaces/css/googleplaces-reviews-bs4.css`

```php
<?php
$fragment = new rex_fragment();
echo $fragment->parse('googleplaces_reviews.bs4.php');
?>
```

## Beispiel-Modul mit Bootstrap 3 Markup

![BS3 Modul](../assets/addons/googleplaces/img/bsp-modul-bs3.jpg)

Das Modul benötigt Bootstrap 3 und Fontawesome für die Sterne. <br>
CSS-Style:<br>
`assets/addons/googleplaces/css/googleplaces-reviews-bs3.css`

```php
<?php
$fragment = new rex_fragment();
echo $fragment->parse('googleplaces_reviews.bs3.php');
?>
```
