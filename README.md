# freielasten_wptheme

Freie Lasten Theme 0.7 auf den Schultern des Neve Themes

Funktionen:

   -Artikelseiten (cb_items) aus ACF Feldern automatisch generieren

   -Automatische Post Grids die im Kontext der Artikel dazu kompatible Artikel anzeigen

   -Automatische Post Grids für Location Seiten mit Kategorien

   -Zusätzlicher Menüpunkt der dynamisch nach der Taxonomie der Artikel und Standorte Menüpunkte auffüllen kann

   -Menüpunkte können mit Color Picker und Icons verschönert werden

   -Einige kleinere UM Anpassungen

Nutzt Plugins: Commonsbooking, ACF Pro, ACF Extended, ACF: Font Awesome, ACF Tab & Accordion Titel Icons, Ultimate Member

Ich werde aber auch hier versuchen so weit wie es geht die einzelnen Funktionen zu beschreiben.
Endgültiges Ziel ist es natürlich, das Child Theme soweit es geht überflüssig zu machen. Arbeit bei CommonsBooking hat dafür schon begonnen und einige Teile konnten rausgenommen werden.
Es ist aber immer noch viel Arbeit, bis alles integriert wäre. Manches ist so auf uns zugeschnitten, dass es wahrscheinlich nie in ein Plugin übernommen werden würde.

h3. functions.php

Die erste Datei die geladen wird, von da aus wird mit require_once jede Funktion des Child Themes einzeln geladen. Praktisch ist alles modular, Dateien können also durch Auskommentieren des jeweiligen includes auch ausgenommen werden. Die jeweiligen Abhängigkeiten sind in den Modulen selber definiert.
In der Functions.php wird auch der svg-loader geladen (https://github.com/shubhamjain/svg-loader) , damit können die SVG Icons "on the fly" eingefärbt werden, sobald sie geladen wurden.

h3. inc/AdminOptions.php

Fügt das "Freie LASTEN Optionen" Feld im Dashboard hinzu

h3. inc/CB_Meta_Key.php
-
FÜgt den Meta Key __bookable Artikeln hinzu, die buchbar sind (also einen Zeitrahmen haben)-
Entfernt, benutzt jetzt Standard CB Funktionen

h3. inc/QueryFunctions.php

Verschiedene Custom Abfragefunktionen (z.B. um alle Räder mit passenden Kupplung zu finden), von verschiedenen Modulen genutzt

h3. inc/Shortcodes.php

Fügt SHortcodes cb_itemgallery, cb_postgrid und cb_locationcats hinzu. Die Funktionen zur Generierung der PostGrid und ItemGallery liegen im "View" Ordner.

* cb_postgrid
** Eine Liste mit Artikelbildern wird angezeigt (2xn Format). 
** Parameter:
*** itemcat=''
**** Kategorieslug der anzuzeigenden Items
*** locationcat=''
**** Kategorieslug der Location, an denen die Items sein sollen
*** class=''
**** CSS Klasse, die dem Postgrid zugewiesen werden soll
*** hidedefault=true
**** Wenn true gesetzt wird, dann werden die Metadaten (availability table, kurztext des Standorts) erst bei einem hover angezeigt
*** sortbyavailability=true
**** Wenn true gesetzt, dann wird der am nächsten buchbare ARtikel an erster Stelle angezeigt
*** kupplung=''
**** Wenn true gesetzt, werden nur Artikel mit entsprechender Kupplung angezeigt
*** mobile=true
**** wenn true gesetzt, dann wird die PostGrid auch auf Handys angezeigt, ansonsten wird sie bei Handy nicht angezeigt

* cb_itemgallery
** Eine durchklickbare Galerie von Artikel wird angezeigt
** Parameter:
*** itemcat=''
**** Kategorieslug der anzuzeigenden Items
*** locationcat=''
**** Kategorieslug der Location, an denen die Items sein sollen
*** class=''
**** CSS Klasse, die dem Postgrid zugewiesen werden soll
*** hidedefault=true
**** Wenn true gesetzt wird, dann werden die Metadaten (availability table, kurztext des Standorts) erst bei einem hover angezeigt
*** sortbyavailability=true
**** Wenn true gesetzt, dann wird der am nächsten buchbare ARtikel an erster Stelle angezeigt
*** mobile=true
**** wenn true gesetzt, dann wird die PostGrid auch auf Handys angezeigt, ansonsten wird sie bei Handy nicht angezeigt

* cb_locationcats

** Gibt eine Linkliste mit Standortkategorien, an denen Artikel stehen zurück (z.B. Ebsdorfergrund, Fronhausen, Gladenbach, Kirchhain, Landkreis Marburg-Biedenkopf, Lohra, Marburg, Rauschenberg, Weimar)
** Parameter:
*** itemcat=''
**** Kategorieslug der Items, die auf Standortkategorien gecheckt werden sollen

h3. inc/acf_field_groups.php

* Von ACF automatisch generiert, muss manuell nach Generierung neuer Felder reinkopiert werden

h3.  inc/cb-item-single_acf.php 

* Die Ausgangsfunktion, für alle  Custom Felder auf Lastenrad, Anhänger, Inklusionsräder Seiten etc.
* Nutzt den Action Hook 'commonsbooking_before_item-single' (ab CB 2.7) um die Templates einzubinden
* alle Templates für die jeweiligen ARtikel liegen in inc/Templates
