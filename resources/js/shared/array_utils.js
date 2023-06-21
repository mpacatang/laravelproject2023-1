export function array_last_element(array) {
    return array[array.length - 1];
}
export function array_get_from_id(array, id) {
    for (const ele of array) {
        if (ele.id == id)
            return ele;
    }
    return null;
}
export function array_get_index(array, element) {
    for (let i = 0; i < array.length; i++) {
        if (array[i].id == element.id)
            return i;
    }
    return null;
}
export function array_add_unique(array, element) {
    if (!array_contains_unique(array, element)) {
        array.push(element);
    }
    return array;
}
export function array_add_all_unique(array, elements) {
    for (const element of elements) {
        array = array_add_unique(array, element);
    }
    return array;
}
export function array_insert_all_unique(array, elements, position = 0) {
    for (const element of elements) {
        array = array_add_unique(array, element);
    }
    return array;
}
export function array_remove_unique(array, object) {
    return array.filter(function (item) {
        return item !== object;
    });
}
export function array_update_unique(array, object) {
    return array.map(function (item) {
        if (item.id == object.id) {
            return object;
        }
        return item;
    });
}
export function array_contains_unique(array, element) {
    for (const ele of array) {
        if (ele.id === element.id)
            return true;
    }
    return false;
}
export function array_toggle_unique(array, element) {
    if (array_contains_unique(array, element)) {
        return array_remove_unique(array, element);
    }
    else {
        return array_add_unique(array, element);
    }
}
export function array_contains(array, element) {
    for (const ele of array) {
        if (ele == element)
            return true;
    }
    return false;
}
export function array_remove(array, object) {
    return array.filter(function (item) {
        return item !== object;
    });
}
export function array_toggle(array, element) {
    if (array_contains(array, element)) {
        return array_remove(array, element);
    }
    else {
        array.push(element);
    }
    return array;
}
