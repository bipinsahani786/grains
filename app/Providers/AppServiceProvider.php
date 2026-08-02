<?php

namespace App\Providers;

use App\Helpers\UnitHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Share display unit with ALL views ──────────────────────────────
        View::composer('*', function ($view) {
            $displayUnit = 'Quintal';
            $bagWeightKg = 50;

            if (Auth::check()) {
                $company = Auth::user()->company;
                if ($company) {
                    $displayUnit = $company->display_unit ?? 'Quintal';
                    $bagWeightKg = (float) ($company->bag_weight_kg ?? 50);
                }
            }

            $view->with([
                'displayUnit' => $displayUnit,
                'bagWeightKg' => $bagWeightKg,
            ]);
        });

        // ── Blade Directives ───────────────────────────────────────────────

        /**
         * @qty($qtlValue)
         * Converts from Qtl to $displayUnit and formats with label.
         * Requires $displayUnit and $bagWeightKg to be in view scope (always true via composer above).
         */
        Blade::directive('qty', function ($expression) {
            return "<?php echo \\App\\Helpers\\UnitHelper::formatQty($expression, \$displayUnit, \$bagWeightKg); ?>";
        });

        /**
         * @qtyRaw($qtlValue)
         * Returns just the numeric value (no label), for use inside <td> or where you need the number only.
         */
        Blade::directive('qtyRaw', function ($expression) {
            return "<?php echo number_format(\\App\\Helpers\\UnitHelper::fromQtl($expression, \$displayUnit, \$bagWeightKg), 2); ?>";
        });

        /**
         * @unitLabel
         * Outputs the unit label (e.g. Qtl, Kg, Ton).
         */
        Blade::directive('unitLabel', function () {
            return "<?php echo \\App\\Helpers\\UnitHelper::label(\$displayUnit); ?>";
        });
    }
}
