import {IIdentifierModel} from "@js/types/models/models";


export function array_last_element<T>(array: T[]) {
    return array[array.length - 1] as T;
}

export function array_get_from_id<T extends IIdentifierModel>(array: T[], id: number): T | null {
    for (const ele of array) {
        if (ele.id == id) return ele;
    }
    return null;
}

export function array_get_index<T extends IIdentifierModel>(array: T[], element: T): number | null {
    for (let i = 0; i < array.length; i++) {
        if(array[i].id==element.id)
            return i;
    }
    return null;
}

export function array_add_unique<T extends IIdentifierModel>(array: T[], element: T): T[] {
    if (!array_contains_unique(array, element)) {
        array.push(element);
    }
    return array;
}

export function array_add_all_unique<T extends IIdentifierModel>(array: T[], elements: T[]): T[] {
    for (const element of elements) {
        array = array_add_unique(array, element)
    }
    return array;
}

export function array_insert_all_unique<T extends IIdentifierModel>(array: T[], elements: T[], position: number=0): T[] {
    for (const element of elements) {
        array = array_add_unique(array, element)
    }
    return array;
}

export function array_remove_unique<T>(array: T[], object: T): T[] {
    return array.filter(function (item) {
        return item !== object
    })
}

export function array_update_unique<T extends IIdentifierModel>(array: T[], object: T): T[] {
    return array.map(function (item) {
        if (item.id == object.id) {
            return object;
        }
        return item;
    })
}


export function array_contains_unique<T extends IIdentifierModel>(array: T[], element: T): boolean {
    for (const ele of array) {
        if (ele.id === element.id) return true;
    }
    return false;
}

export function array_toggle_unique<T extends IIdentifierModel>(array: T[], element: T): T[] {
    if (array_contains_unique(array, element)) {
        return array_remove_unique(array, element);
    } else {
        return array_add_unique(array, element);
    }
}

export function array_contains<T>(array: T[], element: T): boolean {
    for (const ele of array) {
        if (ele == element) return true;
    }
    return false;
}

export function array_remove<T>(array: T[], object: T): T[] {
    return array.filter(function (item) {
        return item !== object
    })
}


export function array_toggle<T>(array: T[], element: T): T[] {
    if (array_contains(array, element)) {
        return array_remove(array, element);
    } else {
        array.push(element);
    }

    return array;
}
