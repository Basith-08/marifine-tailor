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
    protected $description = 'Create a new CRUD feature (Model, Factory, Migration, Controller, Service, Requests, Vue Pages, and Route)';

    private string $studlyName;
    private string $camelName;
    private string $pluralName;
    private string $lowerName;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->initializeNames();

        $this->createModelAndMigrationAndFactory();
        $this->createService();
        $this->createRequestFiles();
        $this->createController();
        $this->createTypesFile();
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
        $this->camelName = Str::camel($name);
        $this->lowerName = Str::lower($name);
        $this->pluralName = Str::plural($this->lowerName);
    }

    /**
     * Create the Model, its migration file and factory.
     */
    private function createModelAndMigrationAndFactory(): void
    {
        Artisan::call('make:model', ['name' => $this->studlyName, '-m' => true]);
        $this->info("Model and Migration for {$this->studlyName} created successfully.");

        Artisan::call('make:factory', ['name' => "{$this->studlyName}Factory", '--model' => $this->studlyName]);
        $this->info("Factory for {$this->studlyName} created successfully.");
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
        $storeRequestPath = "{$requestPath}/Store{$this->studlyName}Request.php";
        $storeBoilerplate = $this->getBoilerplate('StoreRequest');
        File::put($storeRequestPath, $storeBoilerplate);

        // UpdateRequest
        $updateRequestPath = "{$requestPath}/Update{$this->studlyName}Request.php";
        $updateBoilerplate = $this->getBoilerplate('UpdateRequest');
        File::put($updateRequestPath, $updateBoilerplate);

        $this->info("Request files for {$this->studlyName} created successfully.");
    }


    /**
     * Create the resource Controller.
     */
    private function createController(): void
    {
        $controllerPath = app_path("Http/Controllers/{$this->studlyName}Controller.php");
        $boilerplate = $this->getBoilerplate('Controller');
        File::put($controllerPath, $boilerplate);
        $this->info("Controller for {$this->studlyName} created successfully.");
    }

    /**
     * Create the Vue pages for the feature.
     */
    private function createVuePages(): void
    {
        $vuePagesPath = resource_path("js/pages/{$this->studlyName}");
        File::makeDirectory($vuePagesPath, 0755, true, true);
        File::makeDirectory("{$vuePagesPath}/features", 0755, true, true);


        $this->createVueFile($vuePagesPath, "{$this->studlyName}Index.vue", 'VueIndex');
        $this->createVueFile($vuePagesPath, "{$this->camelName}Columns.ts", 'VueColumns');
        $this->createVueFile("{$vuePagesPath}/features", "{$this->camelName}State.ts", 'VueState');
        $this->createVueFile("{$vuePagesPath}/features", "{$this->camelName}ViewModel.ts", 'VueViewModel');

        $this->info("Vue pages for {$this->studlyName} created successfully in {$vuePagesPath}.");
    }

     /**
     * Create the Typescript type definition file.
     */
    private function createTypesFile(): void
    {
        $typesPath = resource_path("js/types");
        File::makeDirectory($typesPath, 0755, true, true);
        $filePath = "{$typesPath}/{$this->lowerName}.ts";
        $boilerplate = $this->getBoilerplate('VueType');
        File::put($filePath, $boilerplate);
        $this->info("Types file for {$this->studlyName} created successfully.");
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
    private function createVueFile(string $path, string $fileName, string $boilerplateType): void
    {
        $filePath = "{$path}/{$fileName}";
        $boilerplate = $this->getBoilerplate($boilerplateType);
        File::put($filePath, $boilerplate);
    }

    /**
     * Get boilerplate content for a given file type.
     */
    private function getBoilerplate(string $type): string
    {
        $studlyName = $this->studlyName;
        $camelName = $this->camelName;
        $pluralName = $this->pluralName;
        $lowerName = $this->lowerName;

        switch ($type) {
            case 'Service':
                return <<<EOT
<?php

namespace App\Services;

use App\Models\\{$studlyName};
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\BusinessException;

class {$studlyName}Service
{
    public function getAll{$studlyName}s(?string \$search = null, ?string \$sort = 'id', ?string \$direction = 'desc'): LengthAwarePaginator
    {
        \$allowedSort = ['name', 'created_at', 'id']; // TODO: add sortable columns
        \$sort = in_array(\$sort, \$allowedSort) ? \$sort : 'id';
        \$direction = \$direction === 'asc' ? 'asc' : 'desc';

        return {$studlyName}::query()->when(\$search, function (\$query, \$search) {
            \$query->where('name', 'ilike', "{\$search}%"); // TODO: add searchable columns
        })->orderBy(\$sort, \$direction)->paginate(10)->withQueryString();
    }

    public function create(array \$data): {$studlyName}
    {
        return {$studlyName}::create(\$data);
    }

    public function update({$studlyName} \${$camelName}, array \$data): bool
    {
        return \${$camelName}->update(\$data);
    }

    public function destroy({$studlyName} \${$camelName}): ?bool
    {
        // \$this->ensureDeletable(\${$camelName});

        return \${$camelName}->delete();
    }
}
EOT;
            case 'StoreRequest':
                return <<<EOT
<?php

namespace App\Http\Requests\\{$studlyName};

use Illuminate\Foundation\Http\FormRequest;

class Store{$studlyName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
EOT;
            case 'UpdateRequest':
                return <<<EOT
<?php

namespace App\Http\Requests\\{$studlyName};

use Illuminate\Foundation\Http\FormRequest;

class Update{$studlyName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
EOT;
            case 'Controller':
                return <<<EOT
<?php

namespace App\Http\Controllers;

use App\Http\Requests\\{$studlyName}\Store{$studlyName}Request;
use App\Http\Requests\\{$studlyName}\Update{$studlyName}Request;
use App\Models\\{$studlyName};
use App\Services\\{$studlyName}Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;
use App\Exceptions\BusinessException;

class {$studlyName}Controller extends Controller implements HasMiddleware
{
    public function __construct(private readonly {$studlyName}Service \${$camelName}Service) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index(Request \$request): Response
    {
        \$models = \$this->{$camelName}Service->getAll{$studlyName}s(
            \$request->input('search'),
            \$request->input('sort'),
            \$request->input('direction')
        );

        return Inertia::render('{$studlyName}/{$studlyName}Index', [
            '{$pluralName}' => \$models,
            'filters' => \$request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function store(Store{$studlyName}Request \$request): RedirectResponse
    {
        \$this->{$camelName}Service->create(\$request->validated());

        return to_route('{$pluralName}.index')->with('success', '{$studlyName} created successfully');
    }

    public function update(Update{$studlyName}Request \$request, {$studlyName} \${$camelName}): RedirectResponse
    {
        \$this->{$camelName}Service->update(\${$camelName}, \$request->validated());

        return to_route('{$pluralName}.index')->with('success', '{$studlyName} updated successfully');
    }

    public function destroy({$studlyName} \${$camelName}): RedirectResponse
    {
        try {
            \$this->{$camelName}Service->destroy(\${$camelName});
            return to_route('{$pluralName}.index')->with('success', '{$studlyName} deleted successfully');
        } catch (BusinessException \$e) {
            return back()->with('error', \$e->getMessage());
        }
    }
}
EOT;
            case 'VueIndex':
                return <<<EOT
<script setup lang="ts">
import { Plus } from 'lucide-vue-next';
import * as z from 'zod';
import BaseFormDialog, { type FormFieldDefinition } from '@/components/BaseFormDialog.vue';
import DataTable from '@/components/DataTable.vue';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { {$studlyName} } from '@/types/{$lowerName}';
import type { PaginationProps } from '@/types/pagination';
import { columns } from './{$camelName}Columns'
import { use{$studlyName}State } from './features/{$camelName}State';
import { use{$studlyName}ViewModel } from './features/{$camelName}ViewModel';

const props = defineProps<{
	{$pluralName}: PaginationProps<{$studlyName}>;
	filters: {
		search?: string,
		sort?: string,
		direction?: string
	}
}>();

// Schema for the form
const formSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  // TODO: Add other fields
})

// Field definitions for the form
const formFields: FormFieldDefinition<z.infer<typeof formSchema>>[] = [
  { name: 'name', label: 'Name', placeholder: 'Enter name' },
  // TODO: Add other fields
]

const state = use{$studlyName}State(props);
const viewModel = use{$studlyName}ViewModel(props, state);

</script>

<template>
	<AppLayout>
		<div class="flex flex-col gap-4 p-4">
			<div class="flex items-center justify-between">
				<Heading title="{$studlyName}s" />

				<Button @click="viewModel.openCreateDialog" variant="default" size="sm" class="font-semibold w-fit">
					<Plus class="size-4 mr-2" />
					Add {$studlyName}
				</Button>
			</div>

			<BaseFormDialog
				:open="viewModel.isDialogOpen.value"
                @update:open="viewModel.isDialogOpen.value = \$event"
				:title="viewModel.dialogTitle.value"
				:description="viewModel.dialogDescription.value"
				:schema="formSchema"
				:fields="formFields"
				:initial-values="viewModel.initialValues.value"
				@submit="viewModel.handleSubmit"
			/>

			<DataTable
					:columns="columns"
					:data="props.{$pluralName}.data"
					:links="props.{$pluralName}.links"
					:from="props.{$pluralName}.from"
					:total="props.{$pluralName}.total"
					:search="viewModel.search.value"
                    @update:search="viewModel.search.value = \$event"
					:sorting="viewModel.sorting.value"
                    @update:sorting="viewModel.sorting.value = \$event"
					:meta="{
						onEdit: viewModel.onEdit,
						onDelete: viewModel.confirmDelete,
					}"
				/>
		</div>
	</AppLayout>
	
</template>
EOT;
            case 'VueColumns':
                return <<<EOT
import type { ColumnDef } from '@tanstack/vue-table'
import { ArrowUpDown, ArrowDown, ArrowUp } from 'lucide-vue-next'
import { h } from 'vue'
import DropdownAction from '@/components/DataTableDropDown.vue'
import { Button } from '@/components/ui/button'
import type { {$studlyName} } from '@/types/{$lowerName}'

interface TableMeta {
    onEdit: (model: {$studlyName}) => void;
    onDelete: (modelId: number) => void;
    from: number;
}

export const columns: ColumnDef<{$studlyName}>[] = [
    {
        id: 'no',
        header: 'No',
        cell: ({row, table}) => {
            const from = (table.options.meta as TableMeta)?.from ?? 1 
            return from + row.index
        }
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => {
                const sort = column.getIsSorted();
                const icon = sort === 'asc' ? ArrowUp : sort === 'desc' ? ArrowDown : ArrowUpDown;
                return ['Name', h(icon, { class: 'ml-2 h-4 w-4' })]
            })
        },
        cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('name')),
    },
    // TODO: Add more columns
    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row, table }) => {
            const model = row.original;
            const meta = table.options.meta as TableMeta;

            return h('div', { class: 'relative' }, h(DropdownAction, {
                model,
                onEdit: meta.onEdit,
                onDelete: meta.onDelete,
            }))
        },
    },
]
EOT;
            case 'VueState':
                return <<<EOT
import { type SortingState } from '@tanstack/vue-table';
import { ref } from 'vue';
import type { {$studlyName} } from '@/types/{$lowerName}';
import type { PaginationProps } from '@/types/pagination';

export function use{$studlyName}State(props: {
    {$pluralName}: PaginationProps<{$studlyName}>;
    filters: {
        search?: string,
        sort?: string,
        direction?: string
    }
}) {
    const search = ref(props.filters.search ?? '');

    const dialogState = ref<{ mode: 'create' | 'edit' | 'closed'; data?: {$studlyName} }>({
        mode: 'closed',
    });

    const sorting = ref<SortingState>([
        {
            id: props.filters.sort ?? 'name',
            desc: props.filters.direction === 'desc',
        },
    ]);

    return {
        search,
        dialogState,
        sorting,
    };
}
EOT;
            case 'VueViewModel':
                return <<<EOT
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { computed, watch } from 'vue';
import type * as z from 'zod';
import type { {$studlyName} } from '@/types/{$lowerName}';
import type { use{$studlyName}State } from './{$camelName}State';

export function use{$studlyName}ViewModel(props: any, state: ReturnType<typeof use{$studlyName}State>) {
    const { search, dialogState, sorting } = state;

    const isDialogOpen = computed({
        get: () => dialogState.value.mode !== 'closed',
        set: (isOpen) => {
            if (!isOpen) {
                dialogState.value = { mode: 'closed' };
            }
        },
    });
    const dialogTitle = computed(() => dialogState.value.mode === 'create' ? 'Create New {$studlyName}' : 'Edit {$studlyName}');
    const dialogDescription = computed(() => dialogState.value.mode === 'create' ? 'Fill out the form below to add a new {$studlyName}.' : 'Update {$camelName} details.');
    const initialValues = computed(() => dialogState.value.data);

    function handleSubmit(values: z.infer<any>) {
        if (dialogState.value.mode === 'edit' && dialogState.value.data) {
            router.put(route('{$pluralName}.update', dialogState.value.data.id), values, {
                onSuccess: () => {
                    dialogState.value = { mode: 'closed' };
                },
            });
        } else if (dialogState.value.mode === 'create') {
            router.post(route('{$pluralName}.store'), values, {
                onSuccess: () => {
                    dialogState.value = { mode: 'closed' };
                },
            });
        }
    }

    function confirmDelete(modelId: number) {
        if (confirm('Are you sure you want to delete this item?')) {
            router.delete(route('{$pluralName}.destroy', modelId));
        }
    }

    function onEdit(model: {$studlyName}) {
        dialogState.value = { mode: 'edit', data: model };
    }

    function openCreateDialog() {
        dialogState.value = { mode: 'create' };
    }

    const updateParams = debounce(() => {
        const sort = sorting.value[0];
        router.get(route('{$pluralName}.index'), {
            search: search.value,
            sort: sort?.id,
            direction: sort?.desc ? 'desc' : 'asc'
        }, {
            preserveState: true,
            replace: true
        });
    }, 300);

    watch(sorting, updateParams);
    watch(search, updateParams);

    return {
        search,
        sorting,
        isDialogOpen,
        dialogTitle,
        dialogDescription,
        initialValues,
        handleSubmit,
        confirmDelete,
        onEdit,
        openCreateDialog,
    };
}
EOT;
            case 'VueType':
                return <<<EOT
export interface {$studlyName} {
    id: number;
    name: string;
    // TODO: Add other fields
}
EOT;
            default:
                return '';
        }
    }
}
