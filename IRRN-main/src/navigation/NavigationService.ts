import {
    CommonActions,
    DrawerActions,
    NavigationContainerRef,
    Route,
    StackActions,
} from '@react-navigation/native';

let navigator: NavigationContainerRef<any>;

export function setTopLevelNavigator(navigatorRef: NavigationContainerRef<{}>): void {
    navigator = navigatorRef;
}

export function setParams(params: object) {
    navigator?.dispatch(CommonActions.setParams(params));
}

export function navigate(name: string, params: object = {}, key?: any, merge: boolean = false): void {
    navigator?.dispatch(CommonActions.navigate({ name, params, ...(!!key && { key }), merge }));
}

export function goBack(prevScreen?: any): void {
    navigator?.dispatch(CommonActions.goBack());
    if (prevScreen && typeof prevScreen === 'string') {
        navigator?.setParams({ prevScreen: prevScreen });
    }
}

export function pop(numberPop: number = 1): void {
    navigator?.dispatch(StackActions.pop(numberPop));
}

export function popToTop(): void {
    navigator?.dispatch(StackActions.popToTop());
}

export function push(name: string, params: object = {}): void {
    navigator?.dispatch(StackActions.push(name, params));
}

export function replace(name: string, params: object = {}): void {
    navigator?.dispatch(StackActions.replace(name, params));
}

export function reset(name: string, params: object = {}): void {
    // navigator.dispatch(StackActions.popToTop());
    // replace(name, params);
    navigator?.dispatch(CommonActions.reset({ index: 0, routes: [{ name, params }] }));
}

export function openDrawer(): void {
    navigator?.dispatch(DrawerActions.openDrawer());
}

export function closeDrawer(): void {
    navigator?.dispatch(DrawerActions.closeDrawer());
}

export function toggleDrawer(): void {
    navigator?.dispatch(DrawerActions.toggleDrawer());
}

export function getCurrentOptions(): object | undefined {
    return navigator?.getCurrentOptions();
}

export function getCurrentRoute(): Route<string, object | undefined> | undefined {
    return navigator?.getCurrentRoute();
}

export function resetRoute(routesFilter: string[], route: any) {
    navigator?.dispatch((state) => {
        let routes = state.routes;

        routesFilter.forEach((item) => {
            routes = state.routes.filter((i) => i.name !== item);
        });

        return CommonActions.reset({
            ...state,
            routes: [...routes, route],
            index: routes.length,
        });
    });
}

const NavigationService = {
    goBack,
    navigate,
    setTopLevelNavigator,
    pop,
    popToTop,
    openDrawer,
    closeDrawer,
    toggleDrawer,
    setParams,
    getCurrentOptions,
    getCurrentRoute,
    push,
    replace,
    reset,
    resetRoute,
};

export default NavigationService;
