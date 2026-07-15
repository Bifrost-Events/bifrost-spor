<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Support\IdGenerator;

$alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
$usedTokens = [];
$stopsFile = __DIR__ . '/../storage/data/stops.json';
if (is_file($stopsFile)) {
    $existing = json_decode((string) file_get_contents($stopsFile), true);
    foreach (($existing['items'] ?? []) as $s) {
        if (!empty($s['qr_token'])) {
            $usedTokens[] = (string) $s['qr_token'];
        }
    }
}
$usedTokens = array_values(array_unique($usedTokens));

$tok = static function () use (&$usedTokens, $alphabet): string {
    for ($i = 0; $i < 100; $i++) {
        $t = '';
        for ($j = 0; $j < 8; $j++) {
            $t .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        if (!in_array($t, $usedTokens, true)) {
            $usedTokens[] = $t;
            return $t;
        }
    }
    throw new RuntimeException('Kunne ikke generere QR-token.');
};

$owner = 'participant_01KXGZK58PNXHNQMSF8YSTJR8Q';
$now = '2026-07-15T22:40:00+02:00';

$routesSpec = [
    [
        'name' => 'Fjellmyr rundløype',
        'slug' => 'fjellmyr-rundloype',
        'description' => 'Kort rundløype rundt myra med innsikt i planter, fugler og spor etter dyr. Ca. 1 km.',
        'theme' => 'nature',
        'stops' => [
            [
                'title' => 'Myrekanten',
                'slug' => 'myrekanten',
                'body' => 'Myra er et unikt økosystem. Torvmoser holder vannet, og mange insekter lever her. Hold deg på stien så du ikke tråkker opp vegetasjonen.',
                'q' => 'Hva er typisk for en myr?',
                'opts' => ['Mykje stein og lite vann', 'Torvmoser som holder på vann', 'Bare sand og kaktus', 'Kun saltvann'],
                'ok' => 1,
            ],
            [
                'title' => 'Fuglelåten',
                'slug' => 'fuglelaten',
                'body' => 'Hør etter lyder fra siv og busker. Troster, linerler og enkelte vadefugler bruker myra som matsøk.',
                'q' => 'Hvorfor er myra viktig for fugler?',
                'opts' => ['Den er mer stille om vinteren', 'Den gir mat, skjul og hvileplass', 'Fugler unngår alltid våtmark', 'Den er salt'],
                'ok' => 1,
            ],
            [
                'title' => 'Dyrespor',
                'slug' => 'dyrespor',
                'body' => 'I bløt torv kan du se spor etter elg, rådyr eller hare. Se etter størrelse og avstand mellom avtrykk.',
                'q' => 'Hva sier dyrespor oss?',
                'opts' => ['Hvilke dyr som har vært der nylig', 'Hvor høy skogen er', 'Om det snør i morgen', 'Fargen på blomster'],
                'ok' => 0,
            ],
        ],
    ],
    [
        'name' => 'Kirkehistorisk vandring',
        'slug' => 'kirkehistorisk-vandring',
        'description' => 'Lær om lokale bygg, tradisjoner og historiske merker i nærmiljøet. Ca. 45 minutter.',
        'theme' => 'culture',
        'stops' => [
            [
                'title' => 'Kirkegården',
                'slug' => 'kirkegarden',
                'body' => 'Gamle gravminner forteller om slekter, yrker og tidsepoker. Les symbolene forsiktig og respekter stedet.',
                'q' => 'Hva er viktig når du besøker en kirkegård?',
                'opts' => ['Løpe mellom gravene', 'Vise respekt og ikke forstyrre', 'Flytte blomster til nye graver', 'Bruke stedet som piknikplass'],
                'ok' => 1,
            ],
            [
                'title' => 'Klokkertårnet',
                'slug' => 'klokkertarn',
                'body' => 'Kirkeklokker har i generasjoner varslet om gudstjeneste, brann og viktige hendelser i bygda.',
                'q' => 'Hva har kirkeklokker historisk vært brukt til?',
                'opts' => ['Kun pynt', 'Å varsle om gudstjeneste og viktige hendelser', 'Værmelding', 'Postombæring'],
                'ok' => 1,
            ],
            [
                'title' => 'Minnesmerket',
                'slug' => 'minnesmerket',
                'body' => 'Minnesteiner og plaketter minner oss om mennesker og hendelser som formet lokalsamfunnet.',
                'q' => 'Hvorfor reises minnesmerker?',
                'opts' => ['For å markere parkering', 'For å huske mennesker og hendelser', 'For å erstatte skilt', 'For å skjule historie'],
                'ok' => 1,
            ],
        ],
    ],
    [
        'name' => 'Museumsløypa',
        'slug' => 'museumsloypa',
        'description' => 'En kultursti mellom historiske bygg, gamle redskaper og fortellinger fra hverdagslivet.',
        'theme' => 'culture',
        'stops' => [
            [
                'title' => 'Gårdstunet',
                'slug' => 'gardstunet',
                'body' => 'Tunet var midtpunktet i gårdsdriften. Her møttes folk, dyr og arbeid gjennom hele året.',
                'q' => 'Hva var typisk for et gårdstun?',
                'opts' => ['Kun en parkeringsplass', 'Et midtpunkt for arbeid og liv på gården', 'En motorvei', 'Et kjøpesenter'],
                'ok' => 1,
            ],
            [
                'title' => 'Redskapsskjulet',
                'slug' => 'redskapsskjulet',
                'body' => 'Ljå, rive og treskeverktøy viser hvordan mat og fôr ble produsert før motorene kom.',
                'q' => 'Hva brukte man ofte før maskiner i jordbruket?',
                'opts' => ['Smarttelefon', 'Ljå, rive og håndkraft', 'Droner', 'Robotgravere'],
                'ok' => 1,
            ],
            [
                'title' => 'Stabburet',
                'slug' => 'stabburet',
                'body' => 'Stabbur sto på stolper for å holde maten tørr og trygg mot skadedyr. Det var et viktig lager gjennom vinteren.',
                'q' => 'Hvorfor sto stabbur ofte på stolper?',
                'opts' => ['For mer skygge', 'For å beskytte mat mot fukt og skadedyr', 'For å være nærmere himmelen', 'For å se bedre'],
                'ok' => 1,
            ],
        ],
    ],
    [
        'name' => 'Bypark-quiz',
        'slug' => 'bypark-quiz',
        'description' => 'En urban løype i parken. Se, lytt og lær om byens grønne lunger og møteplasser.',
        'theme' => 'urban',
        'stops' => [
            [
                'title' => 'Inngangsporten',
                'slug' => 'inngangsporten',
                'body' => 'Byparker gir rom for lek, hvile og møter mellom folk. De er viktige i en tett by.',
                'q' => 'Hvorfor er byparker viktige?',
                'opts' => ['De gjør byen mer grå', 'De gir rom for lek, hvile og møter', 'De erstatter alle hus', 'De er bare for bilister'],
                'ok' => 1,
            ],
            [
                'title' => 'Lekeplassen',
                'slug' => 'lekeplassen',
                'body' => 'Lekeplasser er designet for trygg aktivitet. Regler for hensyn og rydding gjelder også her.',
                'q' => 'Hva er lurt på en lekeplass?',
                'opts' => ['Løpe ut i veien', 'Følge trygge regler og rydde etter seg', 'Klatre på biler', 'La søppel ligge'],
                'ok' => 1,
            ],
            [
                'title' => 'Fontenen',
                'slug' => 'fontenen',
                'body' => 'Vannanlegg i byrom skaper stemning, demper støy og tiltrekker fugler. Ser du speilinger i vannflaten?',
                'q' => 'Hva kan en fontene bidra med i byen?',
                'opts' => ['Mer trafikk', 'Stemning, dempet støy og liv for fugler', 'Mindre grønt', 'Høyere fart'],
                'ok' => 1,
            ],
        ],
    ],
    [
        'name' => 'Gatekunst-ruta',
        'slug' => 'gatekunst-ruta',
        'description' => 'Oppdag kunst, skilt og byhistorie langs gatene. Kort løype for byvandring.',
        'theme' => 'urban',
        'stops' => [
            [
                'title' => 'Murarbeidet',
                'slug' => 'murarbeidet',
                'body' => 'Gatekunst kan fortelle om stedets identitet, humor og aktuelle temaer — ofte laget for alle å se.',
                'q' => 'Hva kan gatekunst gjøre?',
                'opts' => ['Bare skjule vegger', 'Formidle stedets identitet og temaer', 'Erstatte alle museer', 'Stoppe kollektivtrafikk'],
                'ok' => 1,
            ],
            [
                'title' => 'Skiltstolpen',
                'slug' => 'skiltstolpen',
                'body' => 'Veiskilt og gateskilt hjelper oss å navigere. Historiske skilt kan også vise gamle gatenavn.',
                'q' => 'Hvorfor er skilt viktige i byen?',
                'opts' => ['De hjelper oss å navigere', 'De er bare pynt', 'De stopper været', 'De er kun for turister'],
                'ok' => 0,
            ],
            [
                'title' => 'Torget',
                'slug' => 'torget',
                'body' => 'Torg har vært handels- og møteplasser i århundrer. I dag er de fortsatt nav i bylivet.',
                'q' => 'Hva har torg tradisjonelt vært?',
                'opts' => ['Parkeringskjeller', 'Handels- og møteplass', 'Flyplass', 'Idrettshall'],
                'ok' => 1,
            ],
        ],
    ],
    [
        'name' => 'Familiestien',
        'slug' => 'familiestien',
        'description' => 'Enkel intro-løype for hele familien. Korte poster, enkle spørsmål og god stemning.',
        'theme' => 'default',
        'stops' => [
            [
                'title' => 'Start',
                'slug' => 'start',
                'body' => 'Velkommen! Gå i eget tempo, hjelp hverandre med spørsmålene og ha det gøy.',
                'q' => 'Hva er lurt når man går en sporløype?',
                'opts' => ['Stresse forbi alle poster', 'Gå i eget tempo og hjelpe hverandre', 'Gjemme skilt', 'Hoppe over alle spørsmål'],
                'ok' => 1,
            ],
            [
                'title' => 'Hjelperen',
                'slug' => 'hjelperen',
                'body' => 'Noen ganger er det greit å diskutere før man svarer. Samarbeid er en del av opplevelsen.',
                'q' => 'Er det lov å snakke sammen før man svarer?',
                'opts' => ['Nei, aldri', 'Ja, samarbeid er ofte en del av opplevelsen', 'Bare om natten', 'Bare hvis det regner'],
                'ok' => 1,
            ],
            [
                'title' => 'Mål',
                'slug' => 'mal',
                'body' => 'Bra jobbet! Sjekk resultattavlen og se hvordan dere ligger an.',
                'q' => 'Hvor kan du se hvordan dere ligger an?',
                'opts' => ['På resultattavlen', 'I postkassen', 'På bussen', 'I skyen uten å se'],
                'ok' => 0,
            ],
        ],
    ],
    [
        'name' => 'Skolespor',
        'slug' => 'skolespor',
        'description' => 'Læresti for skoleklasser med korte oppfølgingsspørsmål og fokus på observasjon.',
        'theme' => 'default',
        'stops' => [
            [
                'title' => 'Samling',
                'slug' => 'samling',
                'body' => 'Start med å bli enige om regler: hold dere sammen, vær høflige og lytt til hverandre.',
                'q' => 'Hva er en god startregel?',
                'opts' => ['Løpe hver sin vei', 'Holde seg sammen og være høflige', 'Ignorere læreren', 'Gjemme telefonen til andre'],
                'ok' => 1,
            ],
            [
                'title' => 'Observasjon',
                'slug' => 'observasjon',
                'body' => 'Se, lytt og noter. Detaljer du oppdager her kan gjøre neste spørsmål lettere.',
                'q' => 'Hvorfor er observasjon nyttig?',
                'opts' => ['Det gjør deg trøttere', 'Det hjelper deg å merke detaljer og svare bedre', 'Det er bortkastet tid', 'Det ødelegger spørsmålene'],
                'ok' => 1,
            ],
            [
                'title' => 'Oppsummering',
                'slug' => 'oppsummering',
                'body' => 'Snakk om hva dere lærte. Hva overrasket dere mest?',
                'q' => 'Hva er nyttig etter en lærersti?',
                'opts' => ['Å glemme alt med en gang', 'Å oppsummere hva man lærte', 'Å slette bildene', 'Å late som man ikke var der'],
                'ok' => 1,
            ],
        ],
    ],
];

$routesPath = __DIR__ . '/../storage/data/routes.json';
$stopsPath = __DIR__ . '/../storage/data/stops.json';
$routes = json_decode((string) file_get_contents($routesPath), true);
$stops = json_decode((string) file_get_contents($stopsPath), true);
if (!is_array($routes) || !isset($routes['items'])) {
    $routes = ['schema_version' => 1, 'items' => []];
}
if (!is_array($stops) || !isset($stops['items'])) {
    $stops = ['schema_version' => 1, 'items' => []];
}

$existingSlugs = array_map(static fn(array $r): string => (string) ($r['slug'] ?? ''), $routes['items']);

foreach ($routesSpec as $spec) {
    if (in_array($spec['slug'], $existingSlugs, true)) {
        echo "Skip existing slug {$spec['slug']}\n";
        continue;
    }

    $routeId = IdGenerator::routeId();
    $routes['items'][] = [
        'id' => $routeId,
        'owner_id' => $owner,
        'name' => $spec['name'],
        'slug' => $spec['slug'],
        'description' => $spec['description'],
        'status' => 'published',
        'theme' => $spec['theme'],
        'created_at' => $now,
        'updated_at' => $now,
    ];

    $pos = 1;
    foreach ($spec['stops'] as $st) {
        $options = [];
        foreach ($st['opts'] as $i => $text) {
            $options[] = ['id' => 'opt_' . ($i + 1), 'text' => $text];
        }
        $stops['items'][] = [
            'id' => IdGenerator::stopId(),
            'route_id' => $routeId,
            'title' => $st['title'],
            'slug' => $st['slug'],
            'body' => $st['body'],
            'question' => [
                'text' => $st['q'],
                'options' => $options,
                'correct_option_id' => 'opt_' . ($st['ok'] + 1),
            ],
            'image_path' => null,
            'position' => $pos++,
            'qr_token' => $tok(),
            'status' => 'published',
            'latitude' => null,
            'longitude' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    echo "Added {$spec['theme']}: {$spec['name']}\n";
}

$jsonFlags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
file_put_contents($routesPath, json_encode($routes, $jsonFlags) . "\n");
file_put_contents($stopsPath, json_encode($stops, $jsonFlags) . "\n");
file_put_contents(__DIR__ . '/../storage/examples/routes.json', json_encode($routes, $jsonFlags) . "\n");
file_put_contents(__DIR__ . '/../storage/examples/stops.json', json_encode($stops, $jsonFlags) . "\n");

echo 'Routes: ' . count($routes['items']) . ', Stops: ' . count($stops['items']) . "\n";
