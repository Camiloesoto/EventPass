declare module '@/routes' {
    export type RouteHelper = (...args: any[]) => any;
    export const dashboard: RouteHelper;
    export const home: RouteHelper;
    export const login: RouteHelper;
    export const register: RouteHelper;
    export const logout: RouteHelper;
    export const appearance: RouteHelper;
    export const send: RouteHelper;
    export const request: RouteHelper;
    export const edit: RouteHelper;
}

declare module '@/routes/*' {
    export type RouteHelper = (...args: any[]) => any;
    export const dashboard: RouteHelper;
    export const home: RouteHelper;
    export const login: RouteHelper;
    export const register: RouteHelper;
    export const logout: RouteHelper;
    export const appearance: RouteHelper;
    export const send: RouteHelper;
    export const request: RouteHelper;
    export const edit: RouteHelper;
}

declare module '@/actions/*' {
    const controller: any;
    export default controller;
}
