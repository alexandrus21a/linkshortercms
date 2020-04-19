<?php namespace App\Http\Controllers;

use Artisan;
use Cache;
use Common\Core\BaseController;
use Common\Settings\DotEnvEditor;
use Common\Settings\Setting;
use DatabaseSeeder;
use Exception;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Schema;

class UpdateController extends BaseController {
    /**
     * @var DotEnvEditor
     */
    private $dotEnvEditor;

    /**
     * @var Setting
     */
    private $setting;

    /**
     * @param DotEnvEditor $dotEnvEditor
     * @param Setting $setting
     */
    public function __construct(DotEnvEditor $dotEnvEditor, Setting $setting)
    {
        $this->dotEnvEditor = $dotEnvEditor;
        $this->setting = $setting;

        if ( ! config('common.site.disable_update_auth') && version_compare(config('common.site.version'), $this->getAppVersion()) === 0) {
            $this->middleware('isAdmin');
        }
    }

    /**
     * @return View
     */
    public function show()
    {
        return view('update');
    }

    /**
     * @return RedirectResponse
     */
    public function update()
	{
        // fix "index is too long" issue on MariaDB and older mysql versions
        Schema::defaultStringLength(191);

        // TODO: refactor this into separate class, use here and in installer
        $migrator = app('migrator');
        if ( ! $migrator->repositoryExists()) {
            app('migration.repository')->createRepository();
        }
        $paths = $migrator->paths();
        $paths[] = app('path.database').DIRECTORY_SEPARATOR.'migrations';
        $migrator->run($paths);

        // Seed
        $seeder = app(DatabaseSeeder::class);
        $seeder->setContainer(app());
        Model::unguarded(function() use($seeder) {
            $seeder->__invoke();
        });

        // Common seed
        $paths = File::files(app('path.common').'/Database/Seeds');
        foreach ($paths as $path) {
            Model::unguarded(function() use($path) {
                $namespace = 'Common\Database\Seeds\\'.basename($path, '.php');
                $seeder = app($namespace)->setContainer(app());
                $seeder->__invoke();
            });
        }

        $version = $this->getAppVersion();
        $this->dotEnvEditor->write(['app_version' => $version]);

        Cache::flush();

        return redirect()->back()->with('status', 'Updated the site successfully.');
	}

    /**
     * @return string
     */
    private function getAppVersion()
    {
        try {
            return $this->dotEnvEditor->load(base_path('.env.example'))['app_version'];
        } catch (Exception $e) {
            return '1.0.2';
        }
    }
}