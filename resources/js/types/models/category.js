export class Category {
    static select_helper() {
        return {
            option: {
                value: 'id',
                label: 'name'
            },
        };
    }
}
export class SubCategory {
    static select_helper() {
        return {
            option: {
                value: 'id',
                label: 'name'
            },
            optgroup: {
                label: 'name',
                options: 'sub_categories'
            }
        };
    }
    ;
}
