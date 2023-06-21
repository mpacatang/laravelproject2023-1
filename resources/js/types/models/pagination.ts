import {IIdentifierModel, IModel} from "@js/types/models/models";


export interface IPaginationLink extends IModel {
    url: string,
    label: string,
    active: boolean,

}

export interface IPagination extends IIdentifierModel {
    current_page: number,
    from: number,
    last_page: number,
    per_page: number,
    to: number,
    total: number,
    path: string,
    links: IPaginationLink[],

}

export class Pagination {

    static hasNext(pagination: IPagination): boolean {
        return pagination.current_page != pagination.last_page;
    }

    static nextPage(pagination: IPagination): string | null {
        return pagination.links != null && pagination.links.length != 0 ? pagination.links[pagination.links.length-1].url : null;
    }
}
