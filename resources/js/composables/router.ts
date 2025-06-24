export const routeWithQuery = (...args: Parameters<typeof route>) => {
    return route(...args) + window.location.search;
}
