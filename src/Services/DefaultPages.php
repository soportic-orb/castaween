<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Contingut inicial de les pàgines del CMS.
 *
 * Els textos legals fan servir marcadors {{...}} que s'omplen amb les dades de
 * Configuració → Esdeveniment (titular, NIF, domicili, correu…). Així no cal
 * tocar els textos si canvien aquestes dades. Tot es pot editar des del
 * Panell de Gestió → Pàgines.
 *
 * Són una base redactada per a una associació sense ànim de lucre que ven
 * entrades d'un esdeveniment puntual a Catalunya. Convé que l'entitat les
 * revisi abans de publicar el web.
 */
final class DefaultPages
{
    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return [
            [
                'system_key'     => Pages::LEGAL_NOTICE,
                'slug'           => 'avis-legal',
                'title'          => 'Avís legal',
                'summary'        => 'Dades del titular del web i condicions generals d\'ús.',
                'body'           => self::legalNotice(),
                'published'      => 1,
                'show_in_footer' => 1,
                'sort_order'     => 90,
            ],
            [
                'system_key'     => Pages::PRIVACY,
                'slug'           => 'politica-de-privacitat',
                'title'          => 'Política de privacitat',
                'summary'        => 'Com tractem les dades personals de les persones que s\'inscriuen.',
                'body'           => self::privacy(),
                'published'      => 1,
                'show_in_footer' => 1,
                'sort_order'     => 91,
            ],
            [
                'system_key'     => Pages::TERMS,
                'slug'           => 'condicions-dus',
                'title'          => 'Condicions d\'ús i de compra',
                'summary'        => 'Condicions de la inscripció, de les entrades i normes del túnel del terror.',
                'body'           => self::terms(),
                'published'      => 1,
                'show_in_footer' => 1,
                'sort_order'     => 92,
            ],
            [
                'system_key'     => Pages::COOKIES,
                'slug'           => 'politica-de-galetes',
                'title'          => 'Política de galetes',
                'summary'        => 'Quines galetes (cookies) fa servir aquest web i com gestionar-les.',
                'body'           => self::cookies(),
                'published'      => 1,
                'show_in_footer' => 1,
                'sort_order'     => 93,
            ],
            [
                'system_key'     => null,
                'slug'           => 'el-tunel-del-terror',
                'title'          => 'El túnel del terror',
                'summary'        => 'Com és el túnel, els torns, les edats recomanades i les normes per passar-ho de por… amb seguretat.',
                'body'           => self::tunnelExample(),
                'published'      => 0,
                'show_in_menu'   => 1,
                'show_on_home'   => 1,
                'sort_order'     => 10,
            ],
        ];
    }

    private static function legalNotice(): string
    {
        return <<<'MD'
*Darrera actualització: {{data_actualitzacio}}*

## 1. Dades del titular

En compliment de l'article 10 de la Llei 34/2002, d'11 de juliol, de serveis de la societat de la informació i de comerç electrònic (LSSI-CE), t'informem que aquest web és titularitat de:

- **Titular:** {{titular}}
- **NIF:** {{nif}}
- **Domicili:** {{domicili}}
- **Inscripció registral:** {{registre}}
- **Correu electrònic:** {{email_contacte}}
- **Lloc web:** {{web}}

## 2. Objecte del web

Aquest web informa sobre l'esdeveniment **{{nom_esdeveniment}}**, que organitza {{titular}}, i permet inscriure-s'hi i comprar-ne les entrades. La inscripció i la compra es regeixen per les [Condicions d'ús i de compra](/pagina/condicions-dus).

## 3. Condicions d'ús

L'accés al web és lliure i gratuït. Fer-lo servir implica acceptar aquest avís legal. Com a persona usuària et comprometes a:

- Fer un ús del web d'acord amb la llei, la bona fe i aquest avís legal.
- Facilitar dades certes i, si són d'altres persones, tenir-ne l'autorització.
- No intentar accedir a les zones restringides del web, com el Panell de Gestió, ni a dades d'altres persones.
- No fer servir programes o sistemes automatitzats per comprar o reservar entrades de manera massiva.
- No fer cap acció que pugui danyar, inutilitzar o sobrecarregar el web.

L'organització pot suspendre temporalment el web per tasques de manteniment o actualització.

## 4. Propietat intel·lectual i industrial

Els textos, el disseny, el cartell, les il·lustracions i les fotografies d'aquest web són de {{titular}} o de tercers que n'han autoritzat l'ús. No es poden reproduir, distribuir ni transformar amb finalitats comercials sense autorització.

Les tipografies Creepster i Nunito es fan servir sota la llicència SIL Open Font License. Stripe, Apple Wallet i Google Wallet són marques dels seus titulars respectius.

## 5. Responsabilitat

L'organització treballa perquè la informació del web sigui correcta i estigui actualitzada, i perquè el servei funcioni sense interrupcions, però no ho pot garantir en tot moment. No es fa responsable dels danys que es puguin derivar d'un ús incorrecte del web, d'interrupcions o errors tècnics aliens a la seva voluntat, ni de l'actuació de tercers.

## 6. Enllaços a altres webs

El web pot contenir enllaços a webs de tercers, com la passarel·la de pagament de Stripe o mapes. L'organització no controla aquests webs i no es fa responsable dels seus continguts ni de les seves polítiques de privacitat.

## 7. Protecció de dades i galetes

El tractament de les dades personals s'explica a la [Política de privacitat](/pagina/politica-de-privacitat), i l'ús de galetes, a la [Política de galetes](/pagina/politica-de-galetes).

## 8. Legislació aplicable i jurisdicció

Aquest avís legal es regeix per la legislació espanyola. Per resoldre qualsevol controvèrsia, les parts se sotmeten als jutjats i tribunals que corresponguin segons la llei. Si la persona usuària té la condició de consumidora, seran competents els jutjats del seu domicili.
MD;
    }

    private static function privacy(): string
    {
        return <<<'MD'
*Darrera actualització: {{data_actualitzacio}}*

En aquesta pàgina t'expliquem com tractem les dades personals de les persones que s'inscriuen a **{{nom_esdeveniment}}**, d'acord amb el Reglament (UE) 2016/679 general de protecció de dades (RGPD) i la Llei orgànica 3/2018 de protecció de dades personals i garantia dels drets digitals (LOPDGDD).

## 1. Qui és el responsable del tractament

- **Responsable:** {{titular}}
- **NIF:** {{nif}}
- **Domicili:** {{domicili}}
- **Correu electrònic:** {{email_contacte}}

## 2. Quines dades tractem

- **Dades de la persona que fa la inscripció:** nom, cognoms, correu electrònic i, si l'indiques, telèfon.
- **Dades dels assistents:** el nom de cada persona inscrita i, si el formulari ho demana, altres dades com l'edat o observacions.
- **Dades de salut, només si ens les dones:** per exemple, una al·lèrgia alimentària o una necessitat d'accessibilitat que vulguis que tinguem en compte.
- **Dades de la compra:** entrades, imports, estat del pagament, referència i codi de cada entrada, i el registre de validació a l'entrada de l'esdeveniment.
- **Dades del pagament:** el pagament es fa a la passarel·la de Stripe. Nosaltres no veiem ni desem el número de la targeta; només rebem la confirmació i un identificador del pagament.
- **Dades tècniques:** l'adreça IP i dades de navegació necessàries per a la seguretat del web i, només si ho acceptes, dades d'analítica (vegeu la [Política de galetes](/pagina/politica-de-galetes)).

## 3. Per a què fem servir les dades i amb quina base legal

| Finalitat | Base legal |
| --- | --- |
| Gestionar la inscripció i el pagament, enviar-te les entrades i permetre't consultar-les o anul·lar-les | Execució del contracte (art. 6.1.b RGPD) |
| Controlar l'accés el dia de l'esdeveniment validant el codi QR | Execució del contracte (art. 6.1.b RGPD) |
| Enviar-te avisos sobre l'esdeveniment al qual t'has inscrit (recordatoris, canvis d'horari, anul·lacions) | Execució del contracte i interès legítim (art. 6.1.b i 6.1.f RGPD) |
| Tenir en compte al·lèrgies o necessitats especials que ens indiquis | Consentiment explícit (art. 9.2.a RGPD) |
| Gestionar devolucions i complir obligacions comptables i fiscals | Obligació legal (art. 6.1.c RGPD) |
| Mantenir la seguretat del web i evitar-ne usos fraudulents | Interès legítim (art. 6.1.f RGPD) |
| Elaborar estadístiques d'ús del web amb galetes d'analítica | Consentiment (art. 6.1.a RGPD) |

No elaborem perfils, no prenem decisions automatitzades i no fem servir les dades per enviar publicitat.

## 4. Dades de menors

La inscripció l'ha de fer una persona major d'edat. Si inscrius menors, declares que n'ets el pare, la mare o el tutor legal, o que en tens l'autorització, per facilitar-nos-en el nom i les dades que demana el formulari. D'acord amb l'article 7 de la LOPDGDD, els menors de 14 anys no poden consentir pel seu compte el tractament de les seves dades.

## 5. Fotografies i vídeos

Durant l'esdeveniment l'organització pot fer fotografies o vídeos de l'ambient per a la seva memòria i les seves xarxes. Les imatges en què es pugui identificar menors només es publicaran amb l'autorització de les seves famílies. Si no vols sortir-hi, digues-ho a l'organització el mateix dia o escriu a {{email_contacte}}.

## 6. Quant de temps conservem les dades

- **Inscripció i control d'accés:** fins a un any després de l'esdeveniment, per atendre consultes o reclamacions. Després s'eliminen.
- **Al·lèrgies i observacions:** s'eliminen un cop acabat l'esdeveniment.
- **Dades comptables dels pagaments i devolucions:** el temps que exigeix la normativa fiscal i mercantil (fins a 6 anys).
- **Registres tècnics de seguretat:** el temps imprescindible, i com a màxim 12 mesos.

## 7. A qui comuniquem les dades

No cedim dades a tercers, tret que ho exigeixi la llei. Per prestar el servei comptem amb aquests proveïdors, que les tracten seguint les nostres instruccions:

- **Stripe Payments Europe, Ltd.** (Irlanda), per processar els pagaments amb targeta i les devolucions. Stripe també tracta dades com a responsable independent per prevenir el frau i complir les seves obligacions financeres: https://stripe.com/es/privacy
- **L'empresa d'allotjament del web i del correu electrònic**, que guarda la base de dades i envia els correus amb les entrades.
- **Apple i Google**, només si decideixes afegir l'entrada a Apple Wallet o Google Wallet.
- **Google Ireland Ltd.**, només si acceptes les galetes d'analítica de Google Analytics.

Alguns d'aquests proveïdors poden tractar dades fora de l'Espai Econòmic Europeu, sobretot als Estats Units. En aquests casos, les transferències s'emparen en el Marc de Privacitat de Dades UE-EUA (Data Privacy Framework) o en clàusules contractuals tipus aprovades per la Comissió Europea.

## 8. Els teus drets

Pots exercir els drets d'**accés**, **rectificació**, **supressió**, **oposició**, **limitació del tractament** i **portabilitat**, i retirar en qualsevol moment els consentiments que hagis donat. Escriu a {{email_contacte}} indicant quin dret vols exercir. Si tenim dubtes raonables sobre la teva identitat, et podrem demanar que l'acreditis. Et respondrem en el termini d'un mes.

També pots consultar, descarregar o anul·lar les teves inscripcions en qualsevol moment des de [Les meves entrades](/les-meves-entrades).

Si consideres que no hem tractat correctament les teves dades, pots presentar una reclamació a l'Agència Espanyola de Protecció de Dades: https://www.aepd.es

## 9. Seguretat

El web funciona amb connexió xifrada (HTTPS), el Panell de Gestió només és accessible a les persones de l'organització amb usuari i contrasenya, i els enllaços per consultar les entrades són personals i caduquen.

## 10. Canvis en aquesta política

Si modifiquem aquesta política, publicarem la versió nova en aquesta pàgina amb la data de la darrera actualització.
MD;
    }

    private static function terms(): string
    {
        return <<<'MD'
*Darrera actualització: {{data_actualitzacio}}*

Aquestes condicions regulen l'ús d'aquest web i la inscripció i la compra d'entrades per a **{{nom_esdeveniment}}** ({{data_esdeveniment}}, {{lloc_esdeveniment}}), que organitza {{titular}}. Quan marques la casella d'acceptació del formulari declares que les has llegides i que les acceptes.

## 1. Com funciona la inscripció

1. Tries el tipus i el nombre d'entrades.
2. Omples les teves dades i les de cada assistent.
3. Pagues amb targeta a la passarel·la segura de Stripe.
4. Reps la confirmació a la pantalla i per correu electrònic, amb les entrades en PDF.

Mentre fas el pagament, les places queden reservades uns minuts; si no el completes, s'alliberen. La compra es considera feta quan Stripe confirma el pagament. La inscripció es fa en català i en guardem el justificant, que també reps per correu.

## 2. Preus i pagament

Els preus són en euros, per persona, i són els que es mostren al web en el moment de la compra. Si s'aplica una despesa de gestió pel pagament amb targeta, es mostra abans de pagar. El pagament es fa amb targeta a través de Stripe; l'organització no veu ni desa les dades de la targeta.

## 3. Les entrades

- Cada entrada té un codi QR únic que es valida a l'entrada de l'esdeveniment. Pots portar-la al mòbil, impresa o a Apple Wallet o Google Wallet.
- Cada codi només es pot validar una vegada. Si hi ha còpies, només és vàlida la primera que es presenti.
- Les entrades són per a ús personal i no es poden revendre.
- Pots tornar a descarregar-les o enviar-te-les per correu des de [Les meves entrades](/les-meves-entrades).

## 4. Anul·lacions i devolucions

{{politica_anullacions}}

Pots anul·lar les entrades tu mateix des de [Les meves entrades](/les-meves-entrades). La devolució es fa a la mateixa targeta amb què vas pagar i pot trigar uns dies a aparèixer al compte, segons el banc.

D'acord amb l'article 103.l) del Text refós de la Llei general per a la defensa dels consumidors i usuaris, el dret de desistiment no s'aplica a la compra d'entrades per a activitats d'esbarjo amb una data concreta. Per això les anul·lacions es regeixen per la política anterior.

Si l'organització anul·la l'esdeveniment, et retornarà l'import íntegre de les entrades. Si en canvia la data o l'horari, t'ho comunicarà per correu electrònic i podràs anul·lar la inscripció amb la devolució de l'import.

## 5. Normes del túnel del terror

El túnel del terror és un recorregut amb foscor, actors, sorolls forts i efectes especials pensats per fer por. Per passar-ho bé amb seguretat:

- **Edats.** L'entrada d'adult es recomana a partir de 12 anys. El túnel infantil és una versió suau, amb llum i sense ensurts forts. Els menors d'edat han d'anar acompanyats d'una persona adulta responsable.
- **Salut.** No és recomanable per a persones amb problemes cardíacs, epilèpsia o sensibilitat a les llums intermitents, embarassades, persones amb claustrofòbia o amb qualsevol condició que es pugui agreujar amb sustos, foscor, fum o sorolls forts. Si tens dubtes, consulta-ho a l'organització abans de comprar l'entrada.
- **No toquis.** No es pot tocar els actors ni els decorats. Els actors tampoc no et tocaran.
- **Si vols sortir,** digues ben fort «PROU!» o aixeca els braços: una persona de l'organització t'acompanyarà a la sortida.
- **Camina, no corris,** i segueix les indicacions del personal. Dins del túnel no es pot menjar, beure ni fumar, ni fer fotografies o vídeos amb flaix.
- **Torn.** Si l'entrada té un torn d'hora, presenta't uns minuts abans. Si arribes tard, l'organització intentarà fer-te lloc en un torn posterior, però no ho pot garantir.

L'organització pot denegar l'entrada o fer sortir del recorregut qui no respecti aquestes normes o posi en risc la seguretat de les altres persones, sense dret a devolució.

## 6. Responsabilitat

Els menors són responsabilitat de les persones adultes que els acompanyen. L'organització no es fa responsable dels objectes perduts ni dels danys causats per no seguir aquestes normes o les indicacions del personal.

## 7. Dades personals

Les dades que facilites es tracten tal com s'explica a la [Política de privacitat](/pagina/politica-de-privacitat).

## 8. Contacte i reclamacions

Per a qualsevol dubte, queixa o reclamació, escriu a {{email_contacte}}. Respondrem tan aviat com puguem i, en tot cas, en el termini que marca la normativa de consum.

## 9. Legislació aplicable

Aquestes condicions es regeixen per la legislació espanyola i per la normativa catalana de consum. Si ets una persona consumidora, seran competents els jutjats i tribunals del teu domicili.
MD;
    }

    private static function cookies(): string
    {
        return <<<'MD'
*Darrera actualització: {{data_actualitzacio}}*

## 1. Què són les galetes

Les galetes (*cookies*) són petits fitxers que un web desa al teu navegador per recordar informació mentre hi navegues o quan hi tornes. Aquesta política s'aplica al web {{web}}, que és titularitat de {{titular}}.

## 2. Quines galetes fem servir

{{taula_cookies}}

Les galetes **tècniques** són imprescindibles perquè el web funcioni (per exemple, per completar la inscripció) i no necessiten el teu consentiment. Les galetes **d'analítica**, si n'hi ha, només s'instal·len si les acceptes al bàner de galetes.

## 3. Galetes de tercers durant el pagament

Quan pagues, el web et porta a la passarel·la segura de Stripe (checkout.stripe.com). Stripe hi fa servir les seves pròpies galetes per processar el pagament i prevenir el frau. Pots consultar-les a la política de galetes de Stripe: https://stripe.com/es/cookie-settings

## 4. Com pots gestionar les galetes

- Pots acceptar, rebutjar o canviar en qualsevol moment les galetes opcionals amb l'enllaç **Configurar galetes** del peu de pàgina.
- També pots bloquejar o esborrar les galetes des de la configuració del navegador. Si bloqueges les galetes tècniques, potser no podràs completar la inscripció.

Més informació sobre com fer-ho als navegadors més habituals:

- Chrome: https://support.google.com/chrome/answer/95647
- Firefox: https://support.mozilla.org/ca/kb/galetes-informacio-que-els-llocs-web-emmagatzemen-
- Safari: https://support.apple.com/ca-es/guide/safari/sfri11471/mac
- Edge: https://support.microsoft.com/ca-es/microsoft-edge

## 5. Canvis en aquesta política

Si canviem les galetes que fem servir, actualitzarem aquesta pàgina i et tornarem a demanar el consentiment quan calgui. Per a qualsevol dubte, escriu a {{email_contacte}}.
MD;
    }

    private static function tunnelExample(): string
    {
        return <<<'MD'
> Aquesta és una pàgina de mostra. Revisa-la, completa la informació que falta i publica-la des del Panell de Gestió → Pàgines.

## Què t'espera

Un recorregut a les fosques ple de sorpreses, amb actors, boira i sorolls que posaran a prova el teu valor. A la sortida, t'esperen les castanyes torrades i els panellets per recuperar-te de l'ensurt.

## Dues maneres de viure'l

| Entrada | Per a qui | Com és |
| --- | --- | --- |
| **Entrada adult** | A partir de 12 anys | Recorregut complet amb actors i ensurts forts |
| **Entrada infantil** | Fins a 11 anys | Versió suau, amb llum i sense ensurts forts |

## Horaris i torns

- **Obertura del túnel:** [hora]
- **Darrer torn:** [hora]
- **Castanyada i concurs de disfresses:** [hora]

Presenta't uns minuts abans del teu torn amb l'entrada al mòbil o impresa.

## Normes per passar-ho de por amb seguretat

- No toquis els actors ni els decorats: ells tampoc no et tocaran.
- Si vols sortir, digues ben fort «PROU!» i t'acompanyarem a la sortida.
- Camina, no corris, i segueix les indicacions del personal.
- No és recomanable per a persones amb problemes cardíacs, epilèpsia o claustrofòbia.

Totes les condicions són a les [Condicions d'ús i de compra](/pagina/condicions-dus).

## Com arribar

[Adreça exacta, aparcament i accessibilitat.]
MD;
    }
}
