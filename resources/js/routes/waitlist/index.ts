import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\WaitlistController::agregar
 * @see app/Http/Controllers/WaitlistController.php:26
 * @route '/lista-espera/{evento}/agregar'
 */
export const agregar = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: agregar.url(args, options),
    method: 'post',
})

agregar.definition = {
    methods: ["post"],
    url: '/lista-espera/{evento}/agregar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WaitlistController::agregar
 * @see app/Http/Controllers/WaitlistController.php:26
 * @route '/lista-espera/{evento}/agregar'
 */
agregar.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { evento: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { evento: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    evento: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        evento: typeof args.evento === 'object'
                ? args.evento.id
                : args.evento,
                }

    return agregar.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WaitlistController::agregar
 * @see app/Http/Controllers/WaitlistController.php:26
 * @route '/lista-espera/{evento}/agregar'
 */
agregar.post = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: agregar.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\WaitlistController::agregar
 * @see app/Http/Controllers/WaitlistController.php:26
 * @route '/lista-espera/{evento}/agregar'
 */
    const agregarForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: agregar.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\WaitlistController::agregar
 * @see app/Http/Controllers/WaitlistController.php:26
 * @route '/lista-espera/{evento}/agregar'
 */
        agregarForm.post = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: agregar.url(args, options),
            method: 'post',
        })
    
    agregar.form = agregarForm
/**
* @see \App\Http\Controllers\WaitlistController::remover
 * @see app/Http/Controllers/WaitlistController.php:81
 * @route '/lista-espera/{evento}/remover'
 */
export const remover = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remover.url(args, options),
    method: 'delete',
})

remover.definition = {
    methods: ["delete"],
    url: '/lista-espera/{evento}/remover',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\WaitlistController::remover
 * @see app/Http/Controllers/WaitlistController.php:81
 * @route '/lista-espera/{evento}/remover'
 */
remover.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { evento: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { evento: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    evento: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        evento: typeof args.evento === 'object'
                ? args.evento.id
                : args.evento,
                }

    return remover.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WaitlistController::remover
 * @see app/Http/Controllers/WaitlistController.php:81
 * @route '/lista-espera/{evento}/remover'
 */
remover.delete = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remover.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\WaitlistController::remover
 * @see app/Http/Controllers/WaitlistController.php:81
 * @route '/lista-espera/{evento}/remover'
 */
    const removerForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: remover.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\WaitlistController::remover
 * @see app/Http/Controllers/WaitlistController.php:81
 * @route '/lista-espera/{evento}/remover'
 */
        removerForm.delete = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: remover.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    remover.form = removerForm
/**
* @see \App\Http\Controllers\WaitlistController::mostrar
 * @see app/Http/Controllers/WaitlistController.php:118
 * @route '/lista-espera/{evento}/mostrar'
 */
export const mostrar = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mostrar.url(args, options),
    method: 'get',
})

mostrar.definition = {
    methods: ["get","head"],
    url: '/lista-espera/{evento}/mostrar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WaitlistController::mostrar
 * @see app/Http/Controllers/WaitlistController.php:118
 * @route '/lista-espera/{evento}/mostrar'
 */
mostrar.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { evento: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { evento: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    evento: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        evento: typeof args.evento === 'object'
                ? args.evento.id
                : args.evento,
                }

    return mostrar.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WaitlistController::mostrar
 * @see app/Http/Controllers/WaitlistController.php:118
 * @route '/lista-espera/{evento}/mostrar'
 */
mostrar.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mostrar.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\WaitlistController::mostrar
 * @see app/Http/Controllers/WaitlistController.php:118
 * @route '/lista-espera/{evento}/mostrar'
 */
mostrar.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mostrar.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\WaitlistController::mostrar
 * @see app/Http/Controllers/WaitlistController.php:118
 * @route '/lista-espera/{evento}/mostrar'
 */
    const mostrarForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: mostrar.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\WaitlistController::mostrar
 * @see app/Http/Controllers/WaitlistController.php:118
 * @route '/lista-espera/{evento}/mostrar'
 */
        mostrarForm.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: mostrar.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\WaitlistController::mostrar
 * @see app/Http/Controllers/WaitlistController.php:118
 * @route '/lista-espera/{evento}/mostrar'
 */
        mostrarForm.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: mostrar.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    mostrar.form = mostrarForm
/**
* @see \App\Http\Controllers\WaitlistController::notificar
 * @see app/Http/Controllers/WaitlistController.php:134
 * @route '/lista-espera/{evento}/notificar'
 */
export const notificar = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: notificar.url(args, options),
    method: 'post',
})

notificar.definition = {
    methods: ["post"],
    url: '/lista-espera/{evento}/notificar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WaitlistController::notificar
 * @see app/Http/Controllers/WaitlistController.php:134
 * @route '/lista-espera/{evento}/notificar'
 */
notificar.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { evento: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { evento: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    evento: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        evento: typeof args.evento === 'object'
                ? args.evento.id
                : args.evento,
                }

    return notificar.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WaitlistController::notificar
 * @see app/Http/Controllers/WaitlistController.php:134
 * @route '/lista-espera/{evento}/notificar'
 */
notificar.post = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: notificar.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\WaitlistController::notificar
 * @see app/Http/Controllers/WaitlistController.php:134
 * @route '/lista-espera/{evento}/notificar'
 */
    const notificarForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: notificar.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\WaitlistController::notificar
 * @see app/Http/Controllers/WaitlistController.php:134
 * @route '/lista-espera/{evento}/notificar'
 */
        notificarForm.post = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: notificar.url(args, options),
            method: 'post',
        })
    
    notificar.form = notificarForm
const waitlist = {
    agregar: Object.assign(agregar, agregar),
remover: Object.assign(remover, remover),
mostrar: Object.assign(mostrar, mostrar),
notificar: Object.assign(notificar, notificar),
}

export default waitlist