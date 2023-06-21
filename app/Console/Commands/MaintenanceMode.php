<?php

namespace App\Console\Commands;

use App\Helpers\Util\EnvUtil;
use App\Http\Controllers\Api\v1\InstallationController;
use Exception;
use http\Env;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MaintenanceMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'm:r';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return void
     */
    public function handle(): void
    {
        //php artisan migrate --pretend  ---- Use for see upcoming changes
        //php artisan migrate:status  ----    Use for see which table remains
        //php artisan migrate   ----    Use for migrate the latest migrations [not effect on old migrations]
        //php artisan migrate:fresh   ----   Remove all tables and run

        $answer = $this->ask("[Danger]: Are you sure to reset server. [Y/N]", "N");
        if ($answer != "Y") {
            dd("Server resetting stopped. If you need to reset server then re-enter command and press 'Y'");
        }

//        if (!env('DEMO')) {
//            dd("You are not in demo");
//        }

        //Every hour reset data
//        try{
//            dispatch(function () {
//                Artisan::call("m:r");
//            })->delay(now()->addHours(6));
//        }catch (\Exception $e){
//
//        }


        try {
            //Maintenance mode on
            Artisan::call('down');


            $this->resetAllImages();

            //Commands
            Artisan::call('db:wipe');
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed', ['class' => 'TestDatabaseSeeder']);


            //Maintenance mode off
            Artisan::call('up');

//            EnvUtil::changeEnvVariable('VERSION', InstallationController::$NEED_VERSION);

            Artisan::call('optimize:clear');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function resetAllImages()
    {
        try {
            File::deleteDirectory('storage/app/public/admin_avatars');
            File::deleteDirectory('storage/app/public/delivery_boy_avatars');
            File::deleteDirectory('storage/app/public/seller_avatars');
            File::deleteDirectory('storage/app/public/customer_avatars');
            File::deleteDirectory('storage/app/public/shop_covers');
            File::deleteDirectory('storage/app/public/category_images');
            File::deleteDirectory('storage/app/public/product_images');
            File::deleteDirectory('storage/app/public/home_banner_images');

            File::copyDirectory('dummy/demo_images', 'storage/app/public/');
        } catch (Exception $e) {
            dd($e);
        }
    }

//    public function resetAllImages(){
//        File::copyDirectory('public/storage/demo_images', 'public/storage/');
//    }
}
