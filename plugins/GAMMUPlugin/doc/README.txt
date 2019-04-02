Wtyczka GAMMU 2019.04.02 (BETA)

Wtyczka pozwala odczytywać zawartośc tablic inbox i outbox gammu. Poprzez opcję "smstools" i skrypt perl możliwe jest również wysyłanie sms. Jednocześnie można uniknąć używania kalkuna

Odczyt wymaga ustawienia danych do połaczenia z bazą danych.
Wysyłanie wymaga dania systemowi dostępu do katalogu z którego skrypt będzie mówgł wykonywać "gammu-smsd-inject".

INJECT-SMS
Skrypt przeznaczony na system z zainstalowanym gammu. Odczyta smsy w formie plików ze wskazanej ścierzki, wywoła dla każdego gammu-smsd-inject.
Kożysata z konfiguracji w pliku /etc/gammu/inject-sms.ini analogicznego do konfiguracji LMS

KONFIGURACJA
gammudb.type - mysqli lub postgres
gammudb.user
gammudb.password
gammudb.database

sms.service - należy ustawić "smstools"
sms.transliterate_message - włączyć jeżeli w plikach pojawią sie krzaki. Ustawia UTF-8 dla tekstu wiadomości.

sms.smstools_outdir - konfiguracja dla skryptu inject-sms
