1- Copy AdelWAFilter.php to Filters
2- Copy AdelWAF.php to Libraries
3- Edit Config/Filters.php:

use App\Filters\AdelWAFilter;
-----------------------------
        'AdelWAF' => AdelWAFilter::class
-----------------------------
            'AdelWAF',
-----------------------------
