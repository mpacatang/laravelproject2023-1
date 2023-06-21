import {FontWeightTypes} from "@js/types/custom_types";

export interface IModel {
}

export interface IIdentifierModel extends IModel {
    id: number

}


export interface IColumnLabelStyle {
    fontWeight?: FontWeightTypes
}


export interface ITableColumn<T = any> {
    label: string,
    field: string
    width?: number,
    labelStyle?: IColumnLabelStyle,
    sort?: boolean | {
        compare: (a: T, b: T) => number
    },
    data?: (a: T) => any,
    show?: boolean,
    printNone?: boolean,
    onClick?: (a: T) => void,
    onCopy?: (a: T) => string
}

export interface ITablePlaceholder {
    message: string
}

export interface ITableEntry {
    label: string,
    size: number
}

export interface ITableExportOption<T> {
    enable?: boolean,
    auto?: boolean
    callback?: (data: T[]) => void
}

export interface ITableAutoData {
    data: any[],
    fileName: string
}

export interface ITableExport<T> {
    enableAll?: boolean,
    print?: boolean | ITableExportOption<T>,
    excel?: ITableExportOption<T>,
    pdf?: ITableExportOption<T>,
    csv?: ITableExportOption<T>,
    autoData?: (data: T[]) => ITableAutoData
}

export interface ITableOption<T = any> {
    columns: ITableColumn<T>[],
    placeholder?: ITablePlaceholder,
    entries?: ITableEntry[],
    onRefresh?: Function,
    removeTopAction?: boolean,
    exports?: ITableExport<T>,
    columnCustomizer?: boolean
}


export interface IBreadcrumb {
    text: string,
    active?: boolean,
    route?: string
}


export interface ILocalFile{
    id?: number
    accepted?: boolean,
    uploaded?: boolean,
    dataURL: string,
    size?: number,
    name?: string,
    type?: string

}

export interface ILatLng {
    latitude: number,
    longitude: number
}


export interface IFormSelectOption<T = any> {
    option: {
        label: string | ((item: T) => string), value: string,
    },
    optgroup?: {
        label: string | ((item: T) => string), options: string
    }
}


export interface IMenuItem {
    id?: string,
    title?: string,
    name?: string,
    isHeader?: boolean,
    icon?: string,
    badge?: {
        text: string, variant: string
    },
    meta?: object,
    children?: IMenuItem[],
    path?: string,
    route?: string,
    href?: string,
    pinnable?: boolean,
    classList?: string,
    permission?: string
}
