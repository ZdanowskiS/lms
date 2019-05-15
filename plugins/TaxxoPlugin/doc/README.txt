Dodatek TAXXO 2019.05.15

Pozwala przesyłać faktury sprzedaży z LMS do TAXXO oraz faktury kosztowe z plików pdf we wskazanym katalogu.

konfiguracja

taxxo.url = https://platforma2.taxxo.pl/api/public/
taxxo.api_key -klucz api taxxo
taxxo.dir - katalog z którego będą wczytywane pliki pdf i przesyłąne do taxxo 
taxxo.notdigitalize - ('true','false') czy NIE dygitalizować fv wysyłane z lms
taxxo.file_notdigitalize - ('true'/'false') czy NIE dygitalizować fv wysyłane z pliku
taxxo.md5 - ('true','false') czy dodawać md5 do notatki dla dodatkowej weryfikacji

TODO:
-skrypty automatyzujące wysyłane weryfikację i aktualizację lokalnych danych
-możliwość filtrowania jakie faktury zostana wysłane
-raport finansowy z faktur kosztowych
