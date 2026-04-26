<?php

namespace App\Command;

use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-product-images',
    description: 'Télécharge automatiquement une image pour chaque produit via Pexels API'
)]
class ImportProductImagesCommand extends Command
{
    /**
     * Mapping: mot-clé normalisé → requête de recherche en anglais.
     * Les variantes (G/P, @V, Mini-Marché, 300g…) sont ignorées lors de la normalisation.
     */
    private const KEYWORD_MAP = [
        // --- Boissons gazeuses ---
        'coca'           => 'coca cola bottle',
        'fanta'          => 'fanta orange soda',
        'sprite'         => 'sprite lemon soda',
        '7-up'           => '7up soft drink',
        'red bull'       => 'red bull energy drink',
        'appy'           => 'apple juice drink',
        'booster'        => 'energy drink can',
        'ginger'         => 'ginger beer bottle',
        'jus ceres'      => 'fruit juice box',
        'jus minut'      => 'orange juice bottle',
        'jus rani'       => 'mango juice bottle',
        'vody'           => 'vodka bottle',
        'tonic'          => 'tonic water bottle',
        'eau de table'   => 'water bottle',
        'win bucket'     => 'water bucket',
        'ntay'           => 'local drink bottle',

        // --- Bières ---
        '33 export'      => '33 export beer bottle',
        'castel'         => 'castel beer bottle',
        'castel lite'    => 'castel lite beer',
        'cuca'           => 'beer bottle angola',
        'guinness'       => 'guinness beer glass',
        'heineken'       => 'heineken beer bottle',
        'hunters'        => 'hunters dry cider',
        'kung-fu'        => 'beer bottle african',
        'leffe'          => 'leffe beer glass',
        'malti'          => 'malt drink bottle',
        'savana'         => 'savanna dry cider',
        'simba'          => 'simba beer bottle',
        'stella'         => 'stella artois beer',
        'tembo'          => 'tembo beer bottle',
        'xxl'            => 'beer can',
        'dopel'          => 'beer bottle',
        'doppel'         => 'beer bottle',
        'bombo'          => 'whistle bomb beer',
        'combat'         => 'beer bottle local',
        'chui'           => 'beer bottle dark',
        'd\'jno'         => 'soda bottle',
        'milkit'         => 'milk drink bottle',
        'supershak'      => 'energy drink bottle',

        // --- Spiritueux ---
        'absolute vodka' => 'absolut vodka bottle',
        'amarula'        => 'amarula cream liqueur',
        'baccardi rum'   => 'bacardi rum bottle',
        'black label'    => 'johnnie walker black label',
        'camino tequila' => 'tequila bottle',
        'captain morgan' => 'captain morgan rum',
        'double black'   => 'johnnie walker double black',
        'gin bombay'     => 'bombay sapphire gin',
        'glenfiddich'    => 'glenfiddich whisky bottle',
        'gordon\'s gin'  => 'gordons gin bottle',
        'grants'         => 'grants whisky bottle',
        'grey goose'     => 'grey goose vodka',
        'hennessy'       => 'hennessy cognac bottle',
        'j&b'            => 'jb whisky bottle',
        'jack daniel'    => 'jack daniels whisky',
        'jagermeister'   => 'jagermeister bottle',
        'jameson'        => 'jameson irish whiskey',
        'kavita tequila' => 'tequila bottle',
        'martini'        => 'martini vermouth bottle',
        'moet brut'      => 'moet chandon champagne',
        'moet ice'       => 'moet ice imperial',
        'pastis'         => 'pastis anise drink',
        'red label'      => 'johnnie walker red label',
        'reserve7'       => 'reserve whisky bottle',
        'jagermeister'   => 'jagermeister herbal liqueur',
        'divine power'   => 'liqueur bottle',
        'tomson 18'      => 'whisky bottle',

        // --- Vins ---
        'chandor'        => 'white wine bottle',
        'eva vin'        => 'wine bottle',
        'multana'        => 'red wine bottle',
        'valentino vin'  => 'rose wine bottle',
        'saint emilion'  => 'saint emilion bordeaux wine',
        'mouton cadet'   => 'mouton cadet bordeaux wine',
        'nederburg'      => 'nederburg south africa wine',
        '56 hundred'     => 'chardonnay wine bottle',
        'martini rosso'  => 'martini rosso vermouth',
        'martini rosato' => 'martini rosato bottle',

        // --- Plats de poulet ---
        'poulet frite'       => 'fried chicken french fries',
        'poulet makemba'     => 'grilled chicken plantain',
        'poulet riz'         => 'chicken rice dish',
        'poulet fufu'        => 'chicken fufu african food',
        'poulet simple'      => 'grilled chicken plate',
        'poulet roti'        => 'rotisserie chicken',
        'poulet mayo'        => 'chicken mayonnaise plate',
        'demi poulet'        => 'half chicken grilled',
        'ailes de poulet'    => 'chicken wings',
        'poussin'            => 'spring chicken grilled',
        'shawarma poulet'    => 'chicken shawarma wrap',
        'brochette de poulet'=> 'chicken skewers grilled',

        // --- Plats de bœuf/porc ---
        'brochette de boeuf' => 'beef skewers grilled',
        'boulette de viande' => 'meatballs plate',
        'carre de porc frite'=> 'pork ribs french fries',
        'carre de porc makemba' => 'pork ribs plantain',
        'carre de porc riz'  => 'pork ribs rice',
        'steak frite'        => 'steak french fries',
        'steak makemba'      => 'grilled steak plantain',
        'steak riz'          => 'steak rice plate',
        'salamie'            => 'salami plate',
        'saucisse'           => 'sausage grilled',
        'viande hache'       => 'minced meat dish',

        // --- Plats de poisson/fruits de mer ---
        'tilapia'            => 'tilapia fish grilled',
        'fillet de capitaine'=> 'capitaine fish fillet',
        'crevette frite'     => 'fried shrimp',
        'crevette makemba'   => 'shrimp plantain',
        'crevette pomme'     => 'shrimp sauteed potatoes',
        'caille'             => 'quail grilled',
        'pigeon vert'        => 'pigeon green sauce',

        // --- Accompagnements ---
        'fufu'               => 'fufu african food',
        'makemba'            => 'fried plantain makemba',
        'frite'              => 'french fries',
        'riz'                => 'white rice plate',
        'pomme nature'       => 'boiled potatoes',
        'pomme puree'        => 'mashed potatoes',
        'pomme sautee'       => 'sauteed potatoes',
        'kitoyo'             => 'african bread rolls',
        'tomson'             => 'cassava bread fufu',
        'micthopo'           => 'african fufu cassava',

        // --- Légumes & Salades ---
        'ngai-ngai'          => 'african greens vegetable',
        'sombe'              => 'cassava leaves congolese',
        'lenga lenga'        => 'amaranth greens african',
        'choux'              => 'cabbage cooked',
        'salade au thon'     => 'tuna salad',
        'salade grecque'     => 'greek salad',
        'salade mixte'       => 'mixed salad',
        'salade simple'      => 'green salad bowl',
        'pomme de terre'     => 'potatoes',

        // --- Autres plats ---
        'assiette mixte'     => 'mixed platter african food',
        'burger au boeuf'    => 'beef burger',
        'burger au poulet'   => 'chicken burger',
        'crepe chocolat'     => 'chocolate crepe',
        'omelette'           => 'omelette egg',
        'pancake'            => 'pancake breakfast',
        'samusa'             => 'samosa fried',
        'spaghetti bolonaise'=> 'spaghetti bolognese',
        'spaghetti carbonara'=> 'spaghetti carbonara',
        'sauce champignon'   => 'mushroom sauce pasta',
        'gesiers'            => 'chicken gizzards',
        'youzou'             => 'african fried snack',

        // --- Petit-déjeuner/Boissons chaudes ---
        'the rouge'          => 'red rooibos tea',
        'the vert'           => 'green tea cup',
        'cafe'               => 'coffee cup',

        // --- Épicerie ---
        '1l huile'           => 'cooking oil bottle',
        'huile soja'         => 'soybean oil bottle',
        'huile olive'        => 'olive oil bottle',
        'huile cholesterol'  => 'vegetable oil bottle',
        'sardine'            => 'sardine can',
        'tomate'             => 'tomato fresh',
        'mayonnaise'         => 'mayonnaise jar',
        'farine de mais'     => 'corn flour bag',
        'riz'                => 'rice bag',
        'sel'                => 'salt shaker',
        'sucre'              => 'sugar bag',
        'oeuf'               => 'eggs',
        'jumbo arome'        => 'bouillon cube seasoning',
        'jumbo marinade'     => 'marinade seasoning',

        // --- Snacks ---
        'biscuit'            => 'biscuit cookies',
        'lays'               => 'potato chips lays',
        'supershak'          => 'snack chips bag',

        // --- Hygiène / Ménage ---
        'colgate'            => 'colgate toothpaste',
        'bross a dent'       => 'toothbrush',
        'gilette'            => 'gillette razor',
        'savon'              => 'soap bar',
        'savon de main'      => 'hand soap liquid',
        'omo poudre'         => 'omo washing powder',
        'pampers'            => 'pampers diapers',
        'allumette'          => 'matches box',
        'sirage'             => 'shoe polish',
        'ph'                 => 'hygiene product',
        'boom soap'          => 'liquid soap bottle',
        'glass cleaner'      => 'glass cleaner spray',
        'handy-handy'        => 'cleaning spray',
        'serviette'          => 'paper napkins',

        // --- Tabac ---
        'pal mall'           => 'cigarettes pack',
        'tumbaco'            => 'tobacco cigarettes',
        'master'             => 'cigarettes pack',

        // --- Divers ---
        'vip box'            => 'vip gift box',
        'win bucket'         => 'wine bucket ice',
    ];

    public function __construct(
        private readonly ProduitsRepository    $produitsRepo,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('api-key', 'k', InputOption::VALUE_REQUIRED, 'Clé API Pexels (pexels.com/api)')
            ->addOption('force',   'f', InputOption::VALUE_NONE,     'Re-télécharger même si une image existe déjà')
            ->addOption('delay',   'd', InputOption::VALUE_OPTIONAL,  'Délai entre requêtes en ms (défaut: 500)', 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $apiKey = $input->getOption('api-key');
        $force  = $input->getOption('force');
        $delay  = (int) $input->getOption('delay');

        if (!$apiKey) {
            $io->error([
                'Clé API manquante.',
                'Obtenez une clé gratuite sur https://www.pexels.com/api/',
                'Puis lancez : php bin/console app:import-product-images --api-key=VOTRE_CLE',
            ]);
            return Command::FAILURE;
        }

        $imagesDir = dirname(__DIR__, 2) . '/public/uploads/produits';
        if (!is_dir($imagesDir)) {
            mkdir($imagesDir, 0755, true);
        }

        $produits = $this->produitsRepo->findAll();
        $io->title(sprintf('Import images — %d produits', count($produits)));

        $ok = 0; $skip = 0; $fail = 0;
        $imageCache = []; // keyword → filename déjà téléchargé

        $io->progressStart(count($produits));

        foreach ($produits as $produit) {
            $io->progressAdvance();

            if (!$force && $produit->getImage()) {
                $skip++;
                continue;
            }

            $keyword = $this->resolveKeyword($produit->getDesignation());

            // Réutiliser l'image si même keyword déjà téléchargée
            if (isset($imageCache[$keyword])) {
                $produit->setImage($imageCache[$keyword]);
                $this->em->flush();
                $ok++;
                continue;
            }

            $filename = $this->downloadImage($keyword, $imagesDir, $apiKey);
            if ($filename) {
                $imageCache[$keyword] = $filename;
                $produit->setImage($filename);
                $this->em->flush();
                $ok++;
            } else {
                $fail++;
                $io->writeln(sprintf("\n  <comment>Pas d'image trouvée pour : %s (keyword: %s)</comment>",
                    $produit->getDesignation(), $keyword));
            }

            usleep($delay * 1000);
        }

        $io->progressFinish();
        $io->success(sprintf('%d téléchargées, %d ignorées, %d échecs.', $ok, $skip, $fail));

        return Command::SUCCESS;
    }

    /**
     * Trouve le mot-clé de recherche le plus approprié pour un nom de produit.
     */
    private function resolveKeyword(string $name): string
    {
        $normalized = mb_strtolower($name);

        // Supprimer variantes de taille et de service
        $normalized = preg_replace('/\b(mini[- ]marche|@v|_g\b|_p\b|_m\b|\d+g|\d+cl|\d+ml|\d+l\b|g\b|p\b)\b/i', '', $normalized);
        $normalized = preg_replace('/[_@\-]+/', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));

        // Chercher dans la map du plus long au plus court (éviter les faux positifs)
        $keys = array_keys(self::KEYWORD_MAP);
        usort($keys, fn($a, $b) => strlen($b) - strlen($a));

        foreach ($keys as $key) {
            if (str_contains($normalized, $key)) {
                return self::KEYWORD_MAP[$key];
            }
        }

        // Fallback : utiliser le nom nettoyé directement
        return $normalized . ' food product';
    }

    /**
     * Télécharge une image depuis Pexels et la sauvegarde localement.
     */
    private function downloadImage(string $query, string $dir, string $apiKey): ?string
    {
        $url = 'https://api.pexels.com/v1/search?' . http_build_query([
            'query'       => $query,
            'per_page'    => 1,
            'orientation' => 'square',
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: $apiKey"],
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        $imageUrl = $data['photos'][0]['src']['medium'] ?? null;

        if (!$imageUrl) {
            return null;
        }

        // Télécharger l'image
        $ch = curl_init($imageUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $imageData = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if (!$imageData) {
            return null;
        }

        $ext      = match (true) {
            str_contains($contentType, 'jpeg') => 'jpg',
            str_contains($contentType, 'png')  => 'png',
            str_contains($contentType, 'webp') => 'webp',
            default                            => 'jpg',
        };
        $filename = 'pexels_' . md5($query) . '.' . $ext;
        file_put_contents($dir . '/' . $filename, $imageData);

        return $filename;
    }
}
