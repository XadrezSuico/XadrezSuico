<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Process\RatingController;

class RatingCheckDoubleRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rating:check_ratings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica os ratings se há duplicidade e se houver, remove as duplicidades.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rating_controller = new RatingController;
        $rating_controller->checkDoubleRatings();
    }
}
