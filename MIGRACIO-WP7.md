# Migració d'XTECBlocs a WordPress 7.x

Resum de la feina feta a la branca `xtecblocs-7.x` (octubre i novembre de 2026): extensions reescrites, decisions preses,
entorns de prova i tasques pendents.

## 1. Entorns

| Entorn | WordPress | PHP | Branca | Notes |
|---|---|---|---|---|
| MV de desenvolupament (`blocs-aws.xtec.cat`) | 7.1 | 8.5 | `xtecblocs-7.x` | Multisite en subdirectoris, LudicrousDB |
| Integració (`integracio.blocs.xtec.cat`) | 4.7.5 | 7.1 | `master` | Accessible amb VPN |
| Producció | — | — | `master` | Balancejador que fa l'HTTPS; l'aplicació rep HTTP amb capçaleres `X-Forwarded-*` |

### MV
- Vagrant. WP-CLI dins la MV: `cd /dades/blocs/html && wp --allow-root …`.
- LudicrousDB reparteix les taules dels blocs en diverses bases de dades: **no es poden fer consultes `UNION` entre
  blocs**. Cal una consulta per bloc.
- La MV és estrictament de desenvolupament: els problemes de seguretat exclusius del servidor no s'hi tenen en compte.
- **Xarxa i VPN**: la VPN del Departament (Cisco AnyConnect) envia pel túnel tots els rangs privats (10/8, 172.16/12,
  192.168/16), també 192.168.56.0/24. Per això la MV s'ha mogut a **100.64.56.23**:
  - `vms/Vagrantfile`: `ip: "100.64.56.23"` (sense commit, a prova).
  - `/etc/vbox/networks.conf`: `* 192.168.56.0/21 100.64.56.0/24`.
  - `/etc/hosts`: `100.64.56.23 blocs-aws.xtec.cat`.
  - Per tornar enrere: `git -C vms checkout Vagrantfile README.md`, restaurar `/etc/hosts` i `vagrant reload`.
- El `php-fpm` de la MV té `PrivateTmp=true` i desa a la memòria cau els WSDL durant un dia. Si es canvia un WSDL per
  fer proves, les peticions web continuen fent servir la versió en memòria cau.
- Docker té descarregades les imatges `php:7.1-cli` i `php:8.3-cli` (proves de compatibilitat). Es poden esborrar amb
  `docker rmi php:7.1-cli php:8.3-cli`.

### Normes de codi
- PSR-12, arrays amb `[]`, comparacions estrictes (`===`), paràmetres tipats i tipus de retorn, sense codi mort.
- El SonarQube rejecta els comentaris `@todo`, `TODO` i `FIXME`.
- Capçalera estàndard de les extensions i bloc de copyright «Departament d'Educació i Formació Professional».
- Traduccions a `languages/` (`.pot`, `-ca.po`, `-ca.mo`), generades amb `wp i18n make-pot` i `msgfmt`.
- Missatges de commit en anglès.

### Elements antics que no es porten
`testapp.php`, `$isAgora`/`$isBlocs`, la constant `ENVIRONMENT` i les extensions de memòria cau.

### Actualitzacions del WordPress
- El paquet del WordPress porta l'extensió **Akismet**, que no es fa servir: caldria una clau de pagament i els
  comentaris els protegeix CAPTCHA 4WP. Després de cada actualització del nucli cal esborrar
  `html/wp-content/plugins/akismet` abans del commit.

## 2. Estat de les extensions de master

| Extensió de master | Decisió a la 7.x |
|---|---|
| xtec-descriptors, xtec-lastest-posts, xtec-favorites, xtec-api | Incorporades a **xtec-portal** (xtec-favorites no es porta com a extensió) |
| xtec-signup, xtec-settings | Incorporades a **xtec-users** (part de xtec-settings descartada) |
| xtec-users | Reescrita |
| xtec-ldap-login | Submòdul, branca `master` (versió 4.0) |
| xtec-maintenance | Reescrita |
| xtec-mail | Reescrita |
| multisite-plugin-manager | Substituïda per **xtec-plugin-manager** |
| xtec-widget-data-users | Reescrita |
| xtec-weekblog2 | Revisada |
| xtec-link-player | Descartada |
| disable-gutenberg (ginys) | Substituïda per la mu-plugin **xtec-classic-widgets** |
| Tema fukasawa (personalitzacions) | Extensió **xtec-fukasawa** |
| xtec-ms-manager | Revisada. A master és un submòdul; a la 7.x és dins el repositori |
| xtec-eduhack, eduhack (EduHack) | Reescrites sense la creació de blogs, amb les mateixes rutes, perquè els 64 blogs EduHack no canviïn. Vegeu `MIGRACIO-EDUHACK.md` |
| multisite-clone-duplicator (MUCD) | Substituïda per **xtec-site-cloner** |
| wp-recaptcha (fork amb canvis XTEC) | Substituïda per **CAPTCHA 4WP** (`advanced-nocaptcha-recaptcha`), la que fa servir Àgora, sense canvis |
| anti-spam (submòdul, versió 5.5) | No es porta: els comentaris els protegeix **CAPTCHA 4WP**. L'extensió ara és *Titan Anti-spam & Security*, de Themeisle: un paquet de seguretat amb versió de pagament que comprova els comentaris contra una base de dades externa, sense dir quines dades s'hi envien |
| wordpress-importer (fork 0.6.3) | Versió oficial 0.9.7, sense canvis. No es porta el canvi XTEC que treia `force_filtered_html_on_import` (permetia importar HTML sense filtrar) |
| Extensions de tercers (add-to-any, polylang, widget-options, wordpress-social-login…) | **Pendents**. Es portaran: add-to-any, wordpress-social-login. widget-options no es porta: els blogs EduHack només la fan servir per a unes classes CSS, que farà xtec-eduhack. La llista no és tancada |

## 3. Extensions i temes de la branca 7.x

### xtecblocsdefault (tema del portal)
- Escapament de la sortida, correccions XSS, compatibilitat amb PHP 8, pràctiques del WordPress, HTML5, textos en català.
- Dades del bloc de la setmana i accions de preferits mogudes a les extensions.
- La portada té la mateixa amplada que a producció: la de la finestra, entre 990 i 1280 px, centrada.

### xtec-portal
Descriptors, blocs preferits, darreres entrades i darrers blocs per al tema del portal.
- Els darrers blocs són la funció de l'antiga xtec-api, amb el nom corregit: `xtec_api_latest_blogs()` (abans
  `xtec_api_lastest_blogs()`).
- Consultes preparades, nonce a les accions de preferits, `wp_delete_site` en lloc de `delete_blog`.
- Els blocs arxivats, marcats com a brossa o esborrats no es mostren al portal.
- Taula `wp_user_blogs` (preferits), amb la mateixa estructura que a master.

### xtec-weekblog2
Bloc de la setmana: bones pràctiques, PSR-12, descripció amb diverses línies.

### xtec-users
Usuaris XTEC i usuaris locals, i límits de creació de blocs.
- **Usuaris XTEC**: nom d'usuari curt (menys de 9 caràcters) i creador `LDAP_XTEC`/`OAUTH_XTEC` o adreça `@xtec.cat`.
  La contrasenya no es pot gestionar des d'XTECBlocs; es remet a https://xtec.gencat.cat/ca/at_usuari/.
- **Usuaris locals**: nom d'usuari de 9 caràcters o més. Si no s'indica cap adreça, es posa `<usuari>@xtec.invalid`.
- **Pàgina per afegir usuaris** (`users.php?page=xtec-add-users`), que substitueix la del WordPress:
  - comprova el nom d'usuari per AJAX mentre s'escriu;
  - els usuaris XTEC es conviden per correu (s'afegeixen en acceptar la invitació);
  - els usuaris locals existents s'afegeixen directament, i els nous es creen.
  - Si el correu de la invitació no s'envia, s'esborra la invitació i es mostra un error.
- **Gestió d'usuaris** per als superadministradors: tots els usuaris locals i qui els ha creat, paginat.
- Regles de contrasenya de xtec-settings, tancament de sessions en canviar la contrasenya i noms prohibits.
- **Límit de blocs per dia** (opció de xarxa `xtec_signup_maxblogsday`; amb 0, només els superadministradors en creen).

### xtec-ldap-login (submòdul)
Repositori `projectestac/wordpress-xtec-ldap-login`.

| Branca del submòdul | Versió | Ús | Commits rellevants |
|---|---|---|---|
| `master` | 4.0 | Branca 7.x del repositori principal (PHP 8). És l'antiga `WIP-WordPress-7.1` | `40d6c33` Local users can always log in, `31029d3` Detected the OAuth callback by its path, `8dd8ea1` Hardened the OAuth login |
| `RB-version-3.1` | 3.1 | Branca `master` del repositori principal i integració (PHP 7.1). És l'antic `master` | `b5ac873` Fixed the OAuth callback and hardened the OAuth login |

`.gitmodules` segueix la branca `master` del submòdul.

- Els usuaris locals sempre poden entrar, també amb els tipus de validació LDAP i OAuth.
- **OAuth**:
  - `/oauth-callback` es detecta pel camí (hook `parse_request`), sense regles de reescriptura. Abans donava 404
    als servidors on mai s'havien regenerat les regles.
  - La mateixa adreça de retorn (`network_home_url('/oauth-callback')`) en enviar l'usuari i en canviar el codi.
  - Paràmetre `state` a la galeta `xtec_oauth_state` (HttpOnly, Secure, SameSite=Lax, d'un sol ús) contra la CSRF
    a l'inici de sessió.
  - L'adreça ha d'acabar en `@xtec.cat`, estar verificada i, si Google dona `hd`, ser del domini `xtec.cat`.
    Abans, `strpos()` acceptava adreces com `nom@xtec.catalunya.com`.
  - Errors de testimoni o de dades d'usuari comunicats com a tals.
- La versió 3.x (`RB-version-3.1`) depèn de `is_xtecblocs()`, que no existeix a la 7.x. **No es pot fer servir a la
  MV.**

### xtec-maintenance
Blocs inactius i historial de blocs eliminats (xarxa → Manteniment).
- Detecció de blocs inactius per dies, nombre d'entrades i fitxers pujats (adjunts), amb una consulta per bloc
  (LudicrousDB). No compta esborranys automàtics ni paperera.
- Historial de blocs eliminats (taules `delblocs`, `delblocs_users`) i dates localitzades amb `wp_date()`.

### xtec-mail
Envia els correus del WordPress pel servei SOAP **EnviaCorreu** del Departament.
- **Configuració a `wp-config.php`**: `XTEC_MAIL_IDAPP` (`XTECBLOCS`) i `XTEC_MAIL_ENVIRONMENT` (`INT`, `PRE` o
  `PRO`). Sense l'entorn, o sense l'extensió SOAP del PHP, el WordPress envia els correus ell mateix.
- **Servei**: només accessible des de la xarxa del Departament (VPN). Els WSDL locals (`wsdl/int|pre|pro.wsdl`)
  apunten a `*.bus.ensenyament.intranet.gencat.cat`. El bus funciona per HTTP sense xifrar i no té autenticació a
  part de l'`idApp`.
- **Remitents**: `gencat` (`correus_aplicacions.educacio@gencat.cat`, el de producció i per defecte), `xtec` i
  `educacio`.
- **Funcionament**: subclasse de `WP_PHPMailer` (mailer `xtec`); les errades disparen `wp_mail_failed`. Temps
  d'espera de 20 s (`default_socket_timeout`). L'assumpte es neteja de salts de línia.
- **Configuració de xarxa**: Paràmetres de xarxa → Correu. Remitent, adreça de resposta, registre activable amb
  fitxer i formulari «Comprovació de l'enviament de correu» (l'adreça es desa).
- **Registre**: si s'activa, ho desa tot, inclosos els enllaços de restabliment de contrasenyes. Fitxer `.log` o
  `.txt` fora del WordPress, creat amb permisos 640, i hora local del servidor. Si està desactivat, només els errors
  van al registre del PHP.
- **Sense cua de reintents** (decisió presa): si el bus no respon, el correu es perd.
- **Proves fetes**: enviaments reals a INT, PRE i PRO, que van arribar.
- **A tenir en compte a producció**: l'opció antiga `xtec_mail_log` val `1`. Amb aquesta versió, el registre complet
  quedaria activat si la ruta és vàlida.

### xtec-plugin-manager
Substitueix Multisite Plugin Manager i en manté les opcions (`pm_user_control_list`, `pm_auto_activate_list`).
- **Pàgina**: Xarxa → Extensions → Gestió d'extensions.
- **Opcions per a cada extensió no activada a la xarxa**: Ningú, Administradors dels blocs o Activació automàtica
  (en els blocs nous).
- **La restricció és real**: es fa amb els permisos `activate_plugin` i `deactivate_plugin`, de manera que una
  petició feta a mà no la salta. A l'antiga, sí.
- L'API REST no mostra les extensions ocultes als administradors de bloc. WP-CLI sense usuari les veu totes.
- Activació i desactivació massives per lots de 50 blocs (AJAX).
- Amaga la versió, l'autor i els detalls de les extensions als administradors de bloc.
- **Configuració actual de producció**: AddToAny = Activació automàtica, WordPress Social Login = Administradors dels
  blocs, la resta = Ningú.

### xtec-widget-data-users
Giny «Dades d'usuari» per presentar l'autor del bloc. Es fa servir molt, sobretot amb fukasawa.
- Mateixa carpeta, mateix fitxer principal (`xtec-class-data-user.php`), mateix identificador de giny
  (`users_data_widget`) i mateixa imatge per defecte: la migració no canvia res per als blocs.
- Sortida escapada (master tenia XSS emmagatzemat), correu com a enllaç codificat, X en lloc de Twitter, icones SVG
  sense jQuery ni Genericons.
- Si només té la imatge per defecte i cap dada, el giny no es mostra (com a master).

### xtec-ms-manager
Execució d'SQL a tots els blocs i sol·licituds dels blocs als administradors (xarxa → Gestor Multisite; blocs →
Eines → Peticions).
- **Compatibilitat**: es mantenen les taules (`wp_requests`, `wp_request_types`), les opcions de xarxa
  (`xmm_quota_percentage`, `xmm_send_email`, `xmm_email_addresses`) i els identificadors de les pàgines (`xmm-*`).
  El domini de traducció passa de `xmm` a `xtec-ms-manager`.
- **Seguretat**:
  - Consultes preparades: abans, un administrador de bloc podia llegir qualsevol taula amb l'identificador d'una
    sol·licitud.
  - Sortida escapada, també als resultats de l'SQL i als correus, que ara s'envien en HTML.
  - Nonces a totes les accions. L'execució d'SQL té un nonce per a cada pas i només s'executa per POST.
  - Comprovació de permisos a cada pàgina. Els fitxers de les pàgines només es carreguen en obrir-les.
  - Els administradors de bloc només poden demanar tipus de sol·licitud actius.
  - Les adreces de correu de la configuració es validen.
- **Execució d'SQL**:
  - Les dades entre passos es desen en un transient de xarxa per usuari, en lloc de la sessió del PHP, que enviava
    la galeta `PHPSESSID` a tots els visitants.
  - Es mostren els errors de cada bloc i de la consulta de condició, que abans es tractaven com a consultes sense
    resultats.
  - El resum es fa quan tots els resultats tenen una sola columna.
  - S'han tret `WP_SETUP_CONFIG`, que deixava `get_option()` sense funcionar, i el paràmetre `extramem`.
  - **Rendiment**: en cada bloc es canvia només el bloc de `$wpdb` (`set_blog_id()`), sense `switch_to_blog()`.
    Es manté el `blogid` perquè LudicrousDB triï la mateixa base de dades. La llista de blocs s'obté amb una sola
    consulta. Amb 9 blocs, es passa de 19 a 10 consultes.
- **Sol·licituds**:
  - Si un bloc no té quota, la llista mostra «—» en lloc de fallar per divisió per zero.
  - No s'envia el correu si no s'ha pogut actualitzar la sol·licitud.

### xtec-site-cloner
Crea blogs a partir d'un blog plantilla. Substitueix el MUCD; l'NS Cloner es va provar i es va descartar.
- **Còpia a la mateixa petició**, mentre el WordPress inicialitza el blog nou (`wp_initialize_site`, prioritat 50: després
  de les taules del nucli i abans de les extensions d'activació automàtica de xtec-plugin-manager). Cada consulta
  porta el nom d'una sola taula i les files es copien per PHP, perquè funcioni amb LudicrousDB.
- **Substitucions**, també en dades serialitzades i en JSON: adreça del blog, adreça i carpeta dels fitxers
  (`blogs.dir` o `uploads/sites`) i prefix de les taules. Es conserven l'adreça, el títol, el correu, la carpeta dels
  fitxers i la llengua del blog nou; la visibilitat es copia de la plantilla.
- **API**: `xtec_site_cloner_create_site()` (substitueix `MUCD_Duplicate::duplicate_site()`),
  `xtec_site_cloner_copy()` i el filtre `xtec_site_cloner_template`, perquè l'alta del DOSSIER copiï la plantilla sense
  tocar `wp-signup.php`.
- **Errors**: el blog nou s'esborra amb totes les taules. La carpeta de fitxers només s'esborra si és la del blog.
- **Pàgina de xarxa**: Llocs → Clona un blog, i enllaç «Clona» a la llista de llocs, amb la visibilitat del blog nou.
- **Correcció a `db-config.php`**: les consultes amb el prefix d'un blog escapat per `esc_like()` (i `prepare()`)
  anaven a la base de dades global. Afectava l'NS Cloner i `maybe_create_table()` del nucli.

### CAPTCHA 4WP (`advanced-nocaptcha-recaptcha`)
Versió 7.6.2 de WordPress.org, sense canvis. Substitueix wp-recaptcha.
- **Multisite**: s'activa a la xarxa. La configuració (claus i formularis) és l'opció de xarxa `c4wp_admin_options`,
  compartida per tots els blogs, i es fa a Xarxa → CAPTCHA 4WP. No cal cap canvi XTEC.
- **Formularis**: comentaris, inici de sessió, registre, recuperació i restabliment de contrasenya, i alta de
  `wp-signup.php`. L'inici de sessió OAuth de xtec-ldap-login no hi passa.
- **Proveïdor**: la versió gratuïta només admet reCAPTCHA de Google. Configurada amb v2 («No soc un robot») i en català.
- **Important**: l'opció «How to handle failed submissions» (`pass_on_no_captcha_found`) ha de ser «Fail submission».
  Amb el valor per defecte, o si l'opció no s'ha desat mai, les peticions sense el camp del CAPTCHA s'accepten i el
  CAPTCHA es pot saltar. Àgora fa servir la mateixa extensió i convé revisar-ho també.
- Els usuaris que han iniciat sessió també han de resoldre el CAPTCHA per comentar: amagar-lo és de la versió de
  pagament.

### Mu-plugins
- **xtec-admin-menu**: menú d'administració sempre desplegat.
- **xtec-classic-widgets**: gestor clàssic de ginys a tota la xarxa (`use_widgets_block_editor` = `false`).

## 4. Tema fukasawa
A master, fukasawa és la versió 1.10 (submòdul `projectestac/wordpress-theme-fukasawa`, `bbe0712`) amb
personalitzacions XTEC. A la 7.x hi ha la **2.1.3 original**, sense modificar, i les personalitzacions són a
l'extensió **xtec-fukasawa** (activada a la xarxa, només actua als blocs amb aquest tema):

| Personalització de master | A la 7.x |
|---|---|
| Ginys «Entrades recents» i «Comentaris recents» del WordPress, sense els ginys propis del tema | xtec-fukasawa defineix abans que el tema les funcions `fukasawa_widget_registration()` i `fukasawa_unregister_default_widgets()` |
| Sense menú principal a la barra lateral; el giny de menú té el mateix estil | CSS (el menú del mòbil es manté) |
| Fons personalitzable | `add_theme_support('custom-background')` |
| Categories i etiquetes a la graella d'entrades | Filtre `the_excerpt`, amb el color d'accent del tema |
| Retocs de CSS del projecte DOSSIER | CSS: icona de l'element actual del menú, color dels enllaços del menú, sense imatge destacada a les entrades, sense entrades «Pendents» a la portada |
| Traduccions | Paquet d'idioma català de wordpress.org a `wp-content/languages/themes` |
| Descripció de categories i etiquetes | Ja ho fa la 2.1.3 |
| Blocs del projecte DOSSIER (`xtec_blogtype = epa`): enllaç «Sobre aquest espai» i element de menú «Pendents» com a «…» | Enllaç al final de la zona de ginys (el tema no permet afegir res als crèdits) i filtre `wp_nav_menu_objects` |

No s'han portat els estils de master que la 2.1.3 ja no necessita: el títol `h4` de la pàgina d'arxiu, les
descripcions de categories i etiquetes, i el separador abans del giny «Dades d'usuari» (ara el giny té el
contenidor del tema i ja en té).

L'opció `xtec_blogtype = epa` es va posar a mà en un bloc que fa de plantilla, i els blocs del projecte DOSSIER se'n
fan còpia amb l'extensió Multisite Clone Duplicator (MUCD). No la defineix cap codi del repositori.

Les categories i etiquetes s'afegeixen darrere l'extracte, i no surten a les entrades que no en mostren (imatge amb
imatge destacada, vídeo amb «Llegeix més»).

## 5. Tasques pendents
1. **xtec-ldap-login**:
   - desplegar `RB-version-3.1` a integració (WordPress 4.7 i PHP 7.1);
   - fer el commit de la referència del submòdul al repositori principal.
2. **Extensions de tercers pendents d'analitzar**: les que es portaran (add-to-any, wordpress-social-login…).
3. **xtec-mail a producció**: revisar `xtec_mail_log` i la ruta del registre després del desplegament.
4. **CAPTCHA 4WP a producció**: a Xarxa → CAPTCHA 4WP, posar les claus reals de reCAPTCHA v2 (poden ser les de
   wp-recaptcha), la llengua catalana, l'alta de `wp-signup.php` i «How to handle failed submissions» = «Fail
   submission». Comprovar-ho amb `select meta_value from wp_sitemeta where meta_key = 'c4wp_admin_options'`, que ha
   de contenir `s:24:"pass_on_no_captcha_found";s:4:"fail"`.
5. **MV**: decidir si es conserva el canvi de xarxa a 100.64.56.23 (`vms/`, sense commit).
