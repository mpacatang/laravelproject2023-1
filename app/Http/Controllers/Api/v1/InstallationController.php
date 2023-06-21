<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\InstallationException;
use App\Exceptions\InstallationFallbackException;
use App\Helpers\Util\EnvUtil;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminResource;
use App\Models\Admin;
use App\Models\HomeLayout;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Output\BufferedOutput;


class InstallationController extends Controller
{

    static string $NEED_VERSION = "1.2.0";

    /**
     * @throws InstallationFallbackException
     * @throws ValidationException
     */
    public function update_step_2(Request $request): array
    {
        $result = $this->get_step_1();

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_1');
        }
        $validated_data = $this->validate(
            $request,
            [
                'db_host' => ['required'],
                'db_name' => ['required'],
                'db_username' => ['required'],
                'db_password' => ['required'],
            ]
        );

        $db_host = $validated_data['db_host'];
        $db_name = $validated_data['db_name'];
        $db_username = $validated_data['db_username'];
        $db_password = $validated_data['db_password'];

        $database_valid = $this->check_database_connection($db_host, $db_name, $db_username, $db_password);

        if (is_bool($database_valid) && $database_valid) {
            EnvUtil::changeEnvVariable('DB_HOST', $db_host);
            EnvUtil::changeEnvVariable('DB_DATABASE', $db_name);
            EnvUtil::changeEnvVariable('DB_USERNAME', $db_username);
            EnvUtil::changeEnvVariable('DB_PASSWORD', $db_password);
        }

        $reason = null;

        if (!is_bool($database_valid)) {
            $reason = $database_valid;
            $database_valid = false;
        }


        return ['can_next' => $database_valid, 'database_valid' => $database_valid, 'reason' => $reason];
    }

    public function get_step_1(): array
    {
        $phpVersion = number_format((float)phpversion(), 2, '.', '');
        $needToUpdatePhp = $phpVersion < 7.4;
        $curlEnable = function_exists('curl_version');
        $env_write_permission = is_writable(base_path('.env'));
        $bootstrap_permission = is_writable(base_path('bootstrap/cache'));
        $storage_permission = is_writable(base_path('storage'));

        return [
            'can_next' => $curlEnable && !$needToUpdatePhp && $env_write_permission && $bootstrap_permission && $storage_permission,
            'curl_enable' => $curlEnable,
            'php_version' => $phpVersion,
            'bootstrap_permission' => $bootstrap_permission,
            'storage_permission' => $storage_permission,
            'need_to_update_php' => $needToUpdatePhp,
            'env_write_permission' => $env_write_permission
        ];
    }

    public function check_database_connection($db_host, $db_name, $db_user, $db_pass): bool|string
    {
        try {
            if (@mysqli_connect($db_host, $db_user, $db_pass, $db_name, 3306)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @throws InstallationFallbackException
     */
    public function step_3_migrate(Request $request): Application|ResponseFactory|Response|array
    {
        $result = $this->get_step_2($request);

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_2');
        }
        try {
            Artisan::call('migrate');
            return $this->get_step_3($request);
        } catch (Exception $e) {

            return $this->errorResponse("Something went wrong!!! Please contact our support team. Error: " . $e->getMessage());
        }
    }

    /**
     * @throws InstallationFallbackException
     */
    public function get_step_2(Request $request): array
    {
        $result = $this->get_step_1();

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_1');
        }

        Artisan::call('config:cache');
        $db_host = env('DB_HOST');
        $db_name = env('DB_DATABASE');
        $db_username = env('DB_USERNAME');
        $db_password = env('DB_PASSWORD');

        $database_valid = $this->check_database_connection($db_host, $db_name, $db_username, $db_password);
        $reason = null;

        if (!is_bool($database_valid)) {
            $reason = $database_valid;
            $database_valid = false;
        }

        return [
            'can_next' => $database_valid,
            'reason' => $reason,
            'database_valid' => $database_valid,
            'db_host' => $db_host,
            'db_name' => $db_name,
            'db_username' => $db_username,
            'db_password' => $db_password,
        ];
    }

    /**
     * @throws InstallationFallbackException
     * @throws ValidationException
     */
    public function step_4_create(Request $request): Response|array|Application|ResponseFactory
    {
        $result = $this->get_step_3($request);

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_3');
        }
        if (Admin::count() != 0) {
            return $this->errorResponse("Already admin");
        }

        $request->merge(['is_super' => true]);
        $validated_data = $this->validate($request, Admin::rules());
        $admin = new Admin($validated_data);
        $admin->password = Hash::make($validated_data['password']);

        DB::transaction(function () use ($admin) {
            $admin->save();
            Artisan::call('db:seed');
        });

        return ['can_next' => true, 'admin' => new StrictAdminResource($admin)];
    }

    /**
     * @throws InstallationFallbackException
     */
    public function get_step_3(Request $request): array
    {
        $result = $this->get_step_2($request);

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_2');
        }

        try {
            $output = new BufferedOutput();

            Artisan::call('migrate', ['--pretend' => true]);
            Artisan::call('migrate:status', [], $output);

            $any_remaining = false;

            $list = [];
            $o_t = $output->fetch();
            if (!str_contains($o_t, "01_00_00")) {
                return [
                    'can_next' => false,
                    'migrations' => [
                        [
                            "migration" => "Initial Database setup",
                            "pending" => true
                        ]
                    ]
                ];
            }
            $o_t = substr($o_t, strpos($o_t, "01_00_00"));
            $o_t = trim($o_t);
            $o_t = explode(PHP_EOL, $o_t);
            foreach ($o_t as $item) {
                $item = trim($item);
                $item = str_replace('.', '', $item);
                $pending = str_contains($item, 'Pending');
                if ($pending) {
                    $any_remaining = true;
                }
                $item = str_replace('Pending', '', $item);
                $item = str_replace('Ran', '', $item);

                $list[] = ['migration' => trim($item), 'pending' => $pending];
            }
            return ['can_next' => !$any_remaining, 'migrations' => $list];

        } catch (Exception $e) {
            return ['can_next' => false, 'migrations' =>[], 'reason'=>$e->getMessage()];
        }

    }

    /**
     * @throws InstallationFallbackException
     */
    public function get_step_5(Request $request): array
    {
        $result = $this->get_step_4($request);

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_4');
        }
        return ['can_next' => false, 'version' => InstallationController::$NEED_VERSION];
    }

    /**
     * @throws InstallationFallbackException
     */
    public function get_step_4(Request $request): array
    {
        $result = $this->get_step_3($request);

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_3');
        }
        $admin = Admin::where('is_super', true)->first();

        if ($admin) {
            return ['can_next' => true, 'admin' => new StrictAdminResource($admin)];
        } else {
            return ['can_next' => false,];
        }
    }

    /**
     * @throws InstallationFallbackException
     * @throws InstallationException
     */
    public function update_step_5(Request $request): array
    {
        $result = $this->get_step_4($request);

        if (!$result['can_next']) {
            throw new InstallationFallbackException('step_4');
        }

        $this->configure();

        return ['can_next' => true, 'version' => InstallationController::$NEED_VERSION];
    }


    /**
     * @throws InstallationException
     */
    private function configure()
    {
        try {
            DB::transaction(function () {
                $this->upgradeTo_v1_0_1();
                $this->upgradeTo_v1_1_0();

                EnvUtil::changeEnvVariable('VERSION', InstallationController::$NEED_VERSION);
            });
        } catch (Exception $e) {
            throw new InstallationException("Something got wrong. Please contact to author team.", $e->getMessage());
        }
    }

    private function upgradeTo_v1_0_1()
    {
        if (HomeLayout::count() == 0) {
            $home_layouts = [
                [
                    'type' => HomeLayout::$banner_type,
                    'item_ids' => '[]',
                    'priority' => 1,
                    'active' => false,
                ],
                [
                    'type' => HomeLayout::$featured_shops_type,
                    'item_ids' => '[]',
                    'priority' => 2,
                    'active' => false
                ],
                [
                    'type' => HomeLayout::$featured_products_type,
                    'item_ids' => '[]',
                    'priority' => 3,
                    'active' => false,
                ],
                [
                    'type' => HomeLayout::$latest_products_type,
                    'active' => false,
                ],
                [
                    'type' => HomeLayout::$popular_products_type,
                    'active' => false,
                ],
            ];
            foreach ($home_layouts as $home_layout) {
                HomeLayout::create($home_layout);
            }
        }
    }

    private function upgradeTo_v1_1_0()
    {
        $home_layout = HomeLayout::where('type', HomeLayout::$recommended_products_type)->first();
        if ($home_layout == null) {
            HomeLayout::create([
                'type' => HomeLayout::$recommended_products_type,
                'active' => false,
            ]);
        }
    }

}

