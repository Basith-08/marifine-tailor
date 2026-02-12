<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeFeatureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:feature {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD feature (Model, Migration, Controller, Service, Requests, Vue Pages, and Route)';

    private string $studlyName;

    private string $pluralName;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->initializeNames();

        $this->createModelAndMigration();
        $this->createService();
        $this->createRequestFiles();
        $this->createController();
        $this->createVuePages();
        $this->addWebRoute();

        $this->info("Feature {$this->studlyName} created successfully!");
    }

    /**
     * Initialize the name variations.
     */
    private function initializeNames(): void
    {
        $name = $this->argument('name');
        $this->studlyName = Str::studly($name);
        $this->pluralName = Str::plural(Str::lower($name));
    }

    /**
     * Create the Model and its migration file.
     */
    private function createModelAndMigration(): void
    {
        Artisan::call('make:model', ['name' => $this->studlyName, '-m' => true]);
        $this->info("Model and Migration for {$this->studlyName} created successfully.");
    }

    /**
     * Create the Service class for the feature.
     */
    private function createService(): void
    {
        $servicePath = app_path("Services/{$this->studlyName}Service.php");
        $boilerplate = $this->getBoilerplate('Service');
        File::put($servicePath, $boilerplate);
        $this->info("Service for {$this->studlyName} created successfully.");
    }

    /**
     * Create the Form Request classes for the feature.
     */
    private function createRequestFiles(): void
    {
        $requestPath = app_path("Http/Requests/{$this->studlyName}");
        File::makeDirectory($requestPath, 0755, true, true);

        // StoreRequest
        $storeRequestPath = "{$requestPath}/StoreRequest.php";
        $storeBoilerplate = $this->getBoilerplate('StoreRequest');
        File::put($storeRequestPath, $storeBoilerplate);

        // UpdateRequest
        $updateRequestPath = "{$requestPath}/UpdateRequest.php";
        $updateBoilerplate = $this->getBoilerplate('UpdateRequest');
        File::put($updateRequestPath, $updateBoilerplate);

        $this->info("Request files for {$this->studlyName} created successfully.");
    }


    /**
     * Create the resource Controller.
     */
    private function createController(): void
    {
        Artisan::call('make:controller', ['name' => "{$this->studlyName}Controller", '--resource' => true]);
        $this->info("Controller for {$this->studlyName} created successfully.");
    }

    /**
     * Create the Vue pages for the feature.
     */
    private function createVuePages(): void
    {
        $vuePagesPath = resource_path("js/pages/{$this->studlyName}");
        File::makeDirectory($vuePagesPath, 0755, true, true);

        $this->createVueFile($vuePagesPath, 'Index.vue', "{$this->studlyName} Index Page");
        $this->createVueFile($vuePagesPath, 'Create.vue', "Create New {$this->studlyName}");
        $this->createVueFile($vuePagesPath, 'Edit.vue', "Edit {$this->studlyName}");

        $this->info("Vue pages for {$this->studlyName} created successfully in {$vuePagesPath}.");
    }

    /**
     * Add the resource route to the web routes file.
     */
    private function addWebRoute(): void
    {
        $route = "Route::resource('/{$this->pluralName}', App\\Http\\Controllers\\{$this->studlyName}Controller::class);";
        File::append(base_path('routes/web.php'), PHP_EOL . $route);
        $this->info("Route for {$this->studlyName} added to routes/web.php.");
    }

    /**
     * Create a Vue file with boilerplate content.
     */
    private function createVueFile(string $path, string $fileName, string $content): void
    {
        $filePath = "{$path}/{$fileName}";
        $boilerplate = <<<EOT
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
</script>

<template>
    <AppLayout>
        <Heading>{{ '{$content}' }}</Heading>
        <p>This is the {$fileName} page.</p>
    </AppLayout>
</template>
EOT;
        File::put($filePath, $boilerplate);
    }

    /**
     * Get boilerplate content for a given file type.
     */
    private function getBoilerplate(string $type): string
    {
        $studlyName = $this->studlyName;

        return match ($type) {
            'Service' => <<<EOT
<?php

namespace App\Services;

use App\Models\\{$studlyName};

class {$studlyName}Service
{
    public function store(array \$data): {$studlyName}
    {
        // Your store logic here
        return {$studlyName}::create(\$data);
    }

    public function update({$studlyName} \${$this->pluralName}, array \$data): bool
    {
        // Your update logic here
        return \${$this->pluralName}->update(\$data);
    }

    public function destroy({$studlyName} \${$this->pluralName}): ?bool
    {
        // Your destroy logic here
        return \${$this->pluralName}->delete();
    }
}
EOT,
            'StoreRequest', 'UpdateRequest' => <<<EOT
<?php

namespace App\Http\Requests\\{$studlyName};

use Illuminate\Foundation\Http\FormRequest;

class {$type} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
EOT,
            default => '',
        };
    }
}
