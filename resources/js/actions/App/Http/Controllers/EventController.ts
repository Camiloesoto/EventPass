import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\EventController::index
 * @see app/Http/Controllers/EventController.php:22
 * @route '/eventos'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/eventos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EventController::index
 * @see app/Http/Controllers/EventController.php:22
 * @route '/eventos'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::index
 * @see app/Http/Controllers/EventController.php:22
 * @route '/eventos'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EventController::index
 * @see app/Http/Controllers/EventController.php:22
 * @route '/eventos'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\EventController::index
 * @see app/Http/Controllers/EventController.php:22
 * @route '/eventos'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EventController::index
 * @see app/Http/Controllers/EventController.php:22
 * @route '/eventos'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EventController::index
 * @see app/Http/Controllers/EventController.php:22
 * @route '/eventos'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\EventController::disponibles
 * @see app/Http/Controllers/EventController.php:34
 * @route '/eventos/disponibles'
 */
export const disponibles = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: disponibles.url(options),
    method: 'get',
})

disponibles.definition = {
    methods: ["get","head"],
    url: '/eventos/disponibles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EventController::disponibles
 * @see app/Http/Controllers/EventController.php:34
 * @route '/eventos/disponibles'
 */
disponibles.url = (options?: RouteQueryOptions) => {
    return disponibles.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::disponibles
 * @see app/Http/Controllers/EventController.php:34
 * @route '/eventos/disponibles'
 */
disponibles.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: disponibles.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EventController::disponibles
 * @see app/Http/Controllers/EventController.php:34
 * @route '/eventos/disponibles'
 */
disponibles.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: disponibles.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\EventController::disponibles
 * @see app/Http/Controllers/EventController.php:34
 * @route '/eventos/disponibles'
 */
    const disponiblesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: disponibles.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EventController::disponibles
 * @see app/Http/Controllers/EventController.php:34
 * @route '/eventos/disponibles'
 */
        disponiblesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: disponibles.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EventController::disponibles
 * @see app/Http/Controllers/EventController.php:34
 * @route '/eventos/disponibles'
 */
        disponiblesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: disponibles.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    disponibles.form = disponiblesForm
/**
* @see \App\Http\Controllers\EventController::create
 * @see app/Http/Controllers/EventController.php:46
 * @route '/eventos/crear'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/eventos/crear',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EventController::create
 * @see app/Http/Controllers/EventController.php:46
 * @route '/eventos/crear'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::create
 * @see app/Http/Controllers/EventController.php:46
 * @route '/eventos/crear'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EventController::create
 * @see app/Http/Controllers/EventController.php:46
 * @route '/eventos/crear'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\EventController::create
 * @see app/Http/Controllers/EventController.php:46
 * @route '/eventos/crear'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EventController::create
 * @see app/Http/Controllers/EventController.php:46
 * @route '/eventos/crear'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EventController::create
 * @see app/Http/Controllers/EventController.php:46
 * @route '/eventos/crear'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
/**
* @see \App\Http\Controllers\EventController::store
 * @see app/Http/Controllers/EventController.php:57
 * @route '/eventos'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/eventos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EventController::store
 * @see app/Http/Controllers/EventController.php:57
 * @route '/eventos'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::store
 * @see app/Http/Controllers/EventController.php:57
 * @route '/eventos'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\EventController::store
 * @see app/Http/Controllers/EventController.php:57
 * @route '/eventos'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\EventController::store
 * @see app/Http/Controllers/EventController.php:57
 * @route '/eventos'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\EventController::show
 * @see app/Http/Controllers/EventController.php:83
 * @route '/eventos/{evento}'
 */
export const show = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/eventos/{evento}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EventController::show
 * @see app/Http/Controllers/EventController.php:83
 * @route '/eventos/{evento}'
 */
show.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::show
 * @see app/Http/Controllers/EventController.php:83
 * @route '/eventos/{evento}'
 */
show.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EventController::show
 * @see app/Http/Controllers/EventController.php:83
 * @route '/eventos/{evento}'
 */
show.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\EventController::show
 * @see app/Http/Controllers/EventController.php:83
 * @route '/eventos/{evento}'
 */
    const showForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EventController::show
 * @see app/Http/Controllers/EventController.php:83
 * @route '/eventos/{evento}'
 */
        showForm.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EventController::show
 * @see app/Http/Controllers/EventController.php:83
 * @route '/eventos/{evento}'
 */
        showForm.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\EventController::edit
 * @see app/Http/Controllers/EventController.php:96
 * @route '/eventos/{evento}/editar'
 */
export const edit = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/eventos/{evento}/editar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EventController::edit
 * @see app/Http/Controllers/EventController.php:96
 * @route '/eventos/{evento}/editar'
 */
edit.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::edit
 * @see app/Http/Controllers/EventController.php:96
 * @route '/eventos/{evento}/editar'
 */
edit.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EventController::edit
 * @see app/Http/Controllers/EventController.php:96
 * @route '/eventos/{evento}/editar'
 */
edit.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\EventController::edit
 * @see app/Http/Controllers/EventController.php:96
 * @route '/eventos/{evento}/editar'
 */
    const editForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EventController::edit
 * @see app/Http/Controllers/EventController.php:96
 * @route '/eventos/{evento}/editar'
 */
        editForm.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EventController::edit
 * @see app/Http/Controllers/EventController.php:96
 * @route '/eventos/{evento}/editar'
 */
        editForm.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\EventController::update
 * @see app/Http/Controllers/EventController.php:108
 * @route '/eventos/{evento}'
 */
export const update = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/eventos/{evento}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\EventController::update
 * @see app/Http/Controllers/EventController.php:108
 * @route '/eventos/{evento}'
 */
update.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::update
 * @see app/Http/Controllers/EventController.php:108
 * @route '/eventos/{evento}'
 */
update.put = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\EventController::update
 * @see app/Http/Controllers/EventController.php:108
 * @route '/eventos/{evento}'
 */
    const updateForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\EventController::update
 * @see app/Http/Controllers/EventController.php:108
 * @route '/eventos/{evento}'
 */
        updateForm.put = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\EventController::destroy
 * @see app/Http/Controllers/EventController.php:132
 * @route '/eventos/{evento}'
 */
export const destroy = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/eventos/{evento}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\EventController::destroy
 * @see app/Http/Controllers/EventController.php:132
 * @route '/eventos/{evento}'
 */
destroy.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::destroy
 * @see app/Http/Controllers/EventController.php:132
 * @route '/eventos/{evento}'
 */
destroy.delete = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\EventController::destroy
 * @see app/Http/Controllers/EventController.php:132
 * @route '/eventos/{evento}'
 */
    const destroyForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\EventController::destroy
 * @see app/Http/Controllers/EventController.php:132
 * @route '/eventos/{evento}'
 */
        destroyForm.delete = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\EventController::reporte
 * @see app/Http/Controllers/EventController.php:147
 * @route '/eventos/{evento}/reporte'
 */
export const reporte = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reporte.url(args, options),
    method: 'get',
})

reporte.definition = {
    methods: ["get","head"],
    url: '/eventos/{evento}/reporte',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EventController::reporte
 * @see app/Http/Controllers/EventController.php:147
 * @route '/eventos/{evento}/reporte'
 */
reporte.url = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return reporte.definition.url
            .replace('{evento}', parsedArgs.evento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EventController::reporte
 * @see app/Http/Controllers/EventController.php:147
 * @route '/eventos/{evento}/reporte'
 */
reporte.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reporte.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EventController::reporte
 * @see app/Http/Controllers/EventController.php:147
 * @route '/eventos/{evento}/reporte'
 */
reporte.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reporte.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\EventController::reporte
 * @see app/Http/Controllers/EventController.php:147
 * @route '/eventos/{evento}/reporte'
 */
    const reporteForm = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: reporte.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EventController::reporte
 * @see app/Http/Controllers/EventController.php:147
 * @route '/eventos/{evento}/reporte'
 */
        reporteForm.get = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: reporte.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EventController::reporte
 * @see app/Http/Controllers/EventController.php:147
 * @route '/eventos/{evento}/reporte'
 */
        reporteForm.head = (args: { evento: number | { id: number } } | [evento: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: reporte.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    reporte.form = reporteForm
const EventController = { index, disponibles, create, store, show, edit, update, destroy, reporte }

export default EventController