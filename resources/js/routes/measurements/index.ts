import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\MeasurementController::update
* @see app/Http/Controllers/MeasurementController.php:43
* @route '/measurements/{measurement}'
*/
export const update = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/measurements/{measurement}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\MeasurementController::update
* @see app/Http/Controllers/MeasurementController.php:43
* @route '/measurements/{measurement}'
*/
update.url = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { measurement: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { measurement: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            measurement: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        measurement: typeof args.measurement === 'object'
        ? args.measurement.id
        : args.measurement,
    }

    return update.definition.url
            .replace('{measurement}', parsedArgs.measurement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MeasurementController::update
* @see app/Http/Controllers/MeasurementController.php:43
* @route '/measurements/{measurement}'
*/
update.put = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\MeasurementController::update
* @see app/Http/Controllers/MeasurementController.php:43
* @route '/measurements/{measurement}'
*/
update.patch = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\MeasurementController::update
* @see app/Http/Controllers/MeasurementController.php:43
* @route '/measurements/{measurement}'
*/
const updateForm = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MeasurementController::update
* @see app/Http/Controllers/MeasurementController.php:43
* @route '/measurements/{measurement}'
*/
updateForm.put = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MeasurementController::update
* @see app/Http/Controllers/MeasurementController.php:43
* @route '/measurements/{measurement}'
*/
updateForm.patch = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\MeasurementController::destroy
* @see app/Http/Controllers/MeasurementController.php:50
* @route '/measurements/{measurement}'
*/
export const destroy = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/measurements/{measurement}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MeasurementController::destroy
* @see app/Http/Controllers/MeasurementController.php:50
* @route '/measurements/{measurement}'
*/
destroy.url = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { measurement: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { measurement: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            measurement: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        measurement: typeof args.measurement === 'object'
        ? args.measurement.id
        : args.measurement,
    }

    return destroy.definition.url
            .replace('{measurement}', parsedArgs.measurement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MeasurementController::destroy
* @see app/Http/Controllers/MeasurementController.php:50
* @route '/measurements/{measurement}'
*/
destroy.delete = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\MeasurementController::destroy
* @see app/Http/Controllers/MeasurementController.php:50
* @route '/measurements/{measurement}'
*/
const destroyForm = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MeasurementController::destroy
* @see app/Http/Controllers/MeasurementController.php:50
* @route '/measurements/{measurement}'
*/
destroyForm.delete = (args: { measurement: string | number | { id: string | number } } | [measurement: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const measurements = {
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default measurements