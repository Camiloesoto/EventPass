import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TicketScanController::redeem
 * @see app/Http/Controllers/TicketScanController.php:16
 * @route '/tickets/redeem'
 */
export const redeem = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: redeem.url(options),
    method: 'post',
})

redeem.definition = {
    methods: ["post"],
    url: '/tickets/redeem',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TicketScanController::redeem
 * @see app/Http/Controllers/TicketScanController.php:16
 * @route '/tickets/redeem'
 */
redeem.url = (options?: RouteQueryOptions) => {
    return redeem.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketScanController::redeem
 * @see app/Http/Controllers/TicketScanController.php:16
 * @route '/tickets/redeem'
 */
redeem.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: redeem.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\TicketScanController::redeem
 * @see app/Http/Controllers/TicketScanController.php:16
 * @route '/tickets/redeem'
 */
    const redeemForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: redeem.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TicketScanController::redeem
 * @see app/Http/Controllers/TicketScanController.php:16
 * @route '/tickets/redeem'
 */
        redeemForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: redeem.url(options),
            method: 'post',
        })
    
    redeem.form = redeemForm
const TicketScanController = { redeem }

export default TicketScanController